<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Payment;
use App\Project;

use Carbon\Carbon;

use Excel;
use Auth;

class ReportController extends Controller
{
    private $data = array();

    public function payments(Request $request) {

        if ($request->has('start') && $request->has('end')) {
            $inicio = Carbon::parse($request->start)->startOfDay();
            $fim = Carbon::parse($request->end)->endOfDay();
        } else {
            $inicio = Carbon::today()->startOfDay();
            $fim = Carbon::today()->endOfDay();
        }

        $payments = Payment::whereHas('proposal', function($query) { $query->where('status', 'SOLD'); });

        if ($request->has('method')) {
            if ($request->method != "ALL") {
                $payments = $payments->where('method', $request->method);
            }
            $this->data['method'] = $request->method;
        }

        $project_ids = \Auth::user()->projects->pluck('id')->toArray();

        $payments = $payments->get();

        if ($request->has('empreendimento') && $request->empreendimento != 'ALL') {
            if(!Auth::user()->projects->contains('id', $request->empreendimento)) return redirect()->route('reports.payments')->with('error', 'Você não tem permissão para completar essa operação.');

            $emp = $request->empreendimento;
            $payments = $payments->reject(function($value, $key) use ($emp) { return $value->proposal->property->block->building->project->id != $emp; });
            $this->data['empreendimento'] = $request->empreendimento;
        } else {
            $payments = $payments->reject(function($value, $key) use ($project_ids) { return !in_array($value->proposal->property->block->building->project->id, $project_ids); });
        }

        // if ($request->has('empreendimento')) {
        //     if ($request->empreendimento != 'ALL') {
        //         if(!Auth::user()->projects->contains('id', $request->empreendimento)) return redirect()->route('reports.payments')->with('error', 'Você não tem permissão para completar essa operação.');
        //     }

        //     $emp = $request->empreendimento;
        //     if ($request->empreendimento != 'ALL') {
        //         $payments = $payments->reject(function($value, $key) use ($emp) { return $value->proposal->property->block->building->project->id != $emp; });
        //     } else {
        //         $payments = $payments->reject(function($value, $key) use ($project_ids) { return !in_array($value->proposal->property->block->building->project->id, $project_ids); });
        //     }

        //     $this->data['empreendimento'] = $request->empreendimento;
        // }

        $this->data['start'] = $inicio->copy()->toDateString();
        $this->data['end'] = $fim->copy()->toDateString();

        if ($payments->count()) {
            foreach ($payments as $key => $payment) {
                $primeira_parcela = Carbon::parse($payment->expires_at);

                switch ($payment->quantity) {
                    case 1:
                        if (!$primeira_parcela->between($inicio, $fim)) $payments->forget($key);
                    break;
                    default:
                        $parcelas_restantes = $payment->quantity;

                        switch ($payment->component) {
                            case 'Anual': $param = 12; break;
                            case 'Semestre': $param = 6; break;
                            case 'Trimestral': $param = 3; break;
                            case 'Bimestral': $param = 2; break;
                            case 'Mensal': case 'Cartão de crédito': case 'Financiamento': case 'Entrada/Sinal': $param = 1; break;
                            default: $param = 1;
                        }

                        for ($i = 0; $i < $parcelas_restantes; $i++) {
                            $aux = $primeira_parcela;
                            $parcela = $aux->addMonths($param*$i);

                            if ($parcela->between($inicio, $fim)) {
                                break 2;
                            }
                        }

                        $payments->forget($key);
                    break;
                }
            }
        }

        if ($request->has('export')) {
            Excel::create('RelatorioPagamentos_'.dateString($inicio).'_'.dateString($fim), function($excel) use ($payments, $inicio, $fim) {
                $excel->sheet('Pagamentos', function($sheet) use ($payments, $inicio, $fim) {
                    $sheet->row(1, [
                        'Nº',
                        'VENCIMENTO',
                        'PROPONENTE',
                        'EMPREENDIMENTO',
                        'CORRETOR',
                        'IMOBILIÁRIA',
                        'MÉTODO',
                        'VALOR À VISTA',
                        'VALOR CONTRATO',
                        'VALOR PARCELA']
                    );

                    $count = 2;
                    foreach ($payments as $key => $payment) {

                        switch ($payment->component) {
                            case 'Anual': $param = 12; break;
                            case 'Semestre': $param = 6; break;
                            case 'Trimestral': $param = 3; break;
                            case 'Bimestral': $param = 2; break;
                            case 'Mensal': case 'Cartão de crédito': case 'Financiamento': $param = 1; break;
                            default: $param = 1;
                        }

                        for ($i = 0; $i < $payment->quantity; $i++) {
                            $aux = Carbon::parse($payment->expires_at);
                            $parcela = $aux->addMonths($param*$i);

                            if ($parcela->between($inicio, $fim)) {
                                $sheet->row($count, [
                                    $payment->proposal_id,
                                    dateString($parcela),
                                    $payment->proposal->main_proponent->name,
                                    $payment->proposal->property->block->building->project->name,
                                    $payment->proposal->user->name,
                                    $payment->proposal->user->user_projects->where('project_id', $payment->proposal->property->block->building->project->id)->first()->company->name,
                                    $payment->method,
                                    'R$ '.formatMoney($payment->proposal->property->value),
                                    'R$ '.formatMoney($payment->proposal->payments->sum('total_value')),
                                    'R$ '.formatMoney($payment->unit_value)]
                                );
                                $count++;
                            }
                        }
                    }

                    $sheet->row($count, [ '', '',  '', '', '', '', '', '', '']);
                    $count++;

                    $sheet->row($count, [
                        '', '',  '', '', '', '', '',
                        'TOTAL CONTRATOS (À VISTA)',
                        'R$ '.formatMoney($payments->sum(function($payment) { return $payment->proposal->property->value; }))]
                    );
                    $count++;

                    $sheet->row($count, [
                        '', '',  '', '', '', '', '',
                        'TOTAL CONTRATOS (À PRAZO)',
                        'R$ '.formatMoney($payments->sum(function($payment) { return $payment->proposal->payments->sum('total_value'); }))]
                    );
                    $count++;

                    $sheet->row($count, [
                        '', '',  '', '', '', '', '',
                        'TOTAL DAS PARCELAS',
                        'R$ '.formatMoney($payments->sum('unit_value'))]
                    );
                    $count++;

                });
            })->download('xlsx');

            return redirect()->back()->with('success', 'Relatório exportado com sucesso.');
        }

        $this->data['payments'] = $payments;

        //$this->data['projects'] = Project::all();
        $this->data['projects'] = Auth::user()->projects;

        return view('reports.payments', $this->data);
    }

    public function billing(Request $request) {
        $this->data['breadcrumb'][] = ['text' => 'Relatório - Cobranças', 'is_link' => 0, 'link' => null];

        $filters = $params = array();
        if ($request->has('start'))     $filters['start'] = $request->start;
        if ($request->has('end'))       $filters['end'] = $request->end;
        if ($request->has('status'))    $filters['status'] = $request->status;
        if ($request->has('method'))    $filters['method'] = $request->method;
        if ($request->has('project'))   $filters['project'] = $request->project;

        $filters = array_filter($filters, 'strlen');
        $this->data['filters'] = $filters;

        $builder = '';
        $status = null;
        if(count($filters)) $builder = '&'.http_build_query($filters);
        $this->data['builder'] = $builder;

        switch (\Auth::user()->role) {
            case 'ADMIN': $projects = \App\Project::all(); break;
            case 'INCORPORATOR': $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get(); break;
            default: return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
        }

        $property_ids = array();
        foreach ($projects as $key => $project) {
            if(!\Auth::user()->checkPermissionOrAdmin($project->id, ['FINANCIAL_MODULE_ACCESS'])) {
                unset($projects[$key]);
                continue;
            }
            $property_ids = array_merge($property_ids, $project->properties->pluck('id')->toArray());
        }
        $property_ids = array_unique($property_ids);

        $this->data['projects'] = $projects;

        $has_period = $has_status = $has_method = false;
        foreach ($filters as $key => $filter) {
            switch ($key) {
                case 'project':
                    $params['project'] = 'Todos';
                    if($filter != 'ALL') {
                        $project = \App\Project::find($filter);
                        if(!\Auth::user()->projects->contains('id', $filter)) return redirect()->route('reports.billing')->with('error', 'Você não tem permissão para completar essa operação.');
                        $property_ids = $project->properties->pluck('id')->toArray();
                        $params['project'] = $project->name;
                    }
                break;
                case 'status':
                    $params['status'] = 'Todos';
                    if($filter != 'ALL') {
                        $has_status = true;
                        $status = $filter;
                        $params['status'] = getBillingStatusLayout($status)['content'];
                    }
                break;
                case 'method':
                    $params['method'] = 'Todos';
                    if($filter != 'ALL') {
                        $has_method = true;
                        $method = $filter;
                        $params['method'] = $method;
                    }
                break;
                case 'start': case 'end':
                    $has_period = true;
                    $start = $filters['start'];
                    $end = $filters['end'];
                    $params['start'] = formatData($start);
                    $params['end'] = formatData($end);
                break;
            }
        }

        $proposal_ids = \App\Proposal::whereIn('property_id', $property_ids)->get()->pluck('id')->toArray();
        $payments = \App\Payment::whereIn('proposal_id', $proposal_ids);
        if($has_method) $payments->where('method', $method);
        $payment_ids = $payments->get()->pluck('id')->toArray();

        $billings = \App\Billing::whereIn('payment_id', $payment_ids);
        if($has_status) $billings->where('status', $status);
        if($has_period) $billings->whereBetween('expires_at', [ \Carbon\Carbon::parse($filters['start']), \Carbon\Carbon::parse($filters['end']) ]);

        $this->data['billings'] = $cobrancas = $billings->orderBy('expires_at', 'ASC')->get();

        if(!count($filters)) {
            $this->data['billings'] = collect(array());
            return view('reports.billing', $this->data);
        }

        if($request->has('export')) {
            switch ($request->export) {
                case 'XLSX':
                    return $this->export_xlsx($cobrancas, !$status ? 'ALL' : $status, $params);
                break;
                case 'PDF':
                    $name = str_slug('RelatorioCobrancas-'.str_slug(\Carbon\Carbon::now()->toDateTimeString())).'.pdf';
                    $retorno = $this->export_pdf($cobrancas, !$status ? 'ALL' : $status, $params);
                    return $retorno->download($name);
                break;
                default: return redirect()->back()->with('error', 'Formato para exportação inválido.'); break;
            }
        }

        return view('reports.billing', $this->data);
    }

    public function export_xlsx($billings, $status, $filters) {
        \Excel::create('RelatorioCobrancas-'.str_slug(\Carbon\Carbon::now()->toDateTimeString()), function($excel) use ($billings, $status, $filters) {
            $excel->sheet('Relatório', function($sheet) use ($billings, $status, $filters) {

                for ($i = 1; $i < count($filters)+2; $i++) {
                    $sheet->cells('A'.$i.':I'.$i, function($cells) { $cells->setBackground('#EEEEEE'); });
                }

                $count = 1;
                $sheet->mergeCells('A1:I1');
                $sheet->row($count, [ 'Relatório de Cobranças' ]);
                $count++;

                if(isset($filters['start'])) {
                    $sheet->mergeCells('B3:I3');
                    $sheet->row($count, [ 'Período do Vencimento - Início', $filters['start'] ]);
                    $count++;
                }

                if(isset($filters['end'])) {
                    $sheet->mergeCells('B4:I4');
                    $sheet->row($count, [ 'Período do Vencimento - Final', $filters['end'] ]);
                    $count++;
                }

                if(isset($filters['project'])) {
                    $sheet->mergeCells('B5:I5');
                    $sheet->row($count, [ 'Empreendimento', $filters['project'] ]);
                    $count++;
                }

                if(isset($filters['status'])) {
                    $sheet->mergeCells('B6:I6');
                    $sheet->row($count, [ 'Status', $filters['status'] ]);
                    $count++;
                }

                if(isset($filters['method'])) {
                    $sheet->mergeCells('B7:I7');
                    $sheet->row($count, [ 'Metódo', $filters['method'] ]);
                    $count++;
                }

                $count++;

                $sheet->row($count, [
                    'Contrato',
                    'Vencimento',
                    'Proponente',
                    'Empreendimento',
                    'Método',
                    'Data do Pagamento',
                    'Status',
                    'Valor Cobrança',
                    'Valor Pago'
                ]);

                foreach ($billings as $key => $billing) {
                    $count++;

                    $stat = getBillingStatusLayout($billing->status);
                    $sheet->row($count, [
                        $billing->payment->proposal_id,
                        formatData($billing->expires_at),
                        $billing->payment->proposal->main_proponent->name,
                        $billing->payment->proposal->property->block->building->project->name,
                        $billing->payment->method,
                        $billing->getPaymentDate(),
                        $stat['content'],
                        $billing->value,
                        $billing->paid_value
                    ]);
                }

                $count++;

                if(in_array($status, [ 'PENDING', 'ALL' ])) {
                    $count++;
                    $sheet->row($count, [ '', '', '', '', '', '', 'Total Pendente', $billings->where('status', 'PENDING')->sum('value'), $billings->where('status', 'PENDING')->sum('paid_value') ]);
                }

                if(in_array($status, [ 'PAID', 'PAID_MANUAL', 'ALL' ])) {
                    $count++;
                    $sheet->row($count, [ '', '', '', '', '', '', 'Total Pago', $billings->where('status', 'PAID')->sum('value') + $billings->where('status', 'PAID_MANUAL')->sum('value'), $billings->where('status', 'PAID')->sum('paid_value') + $billings->where('status', 'PAID_MANUAL')->sum('paid_value') ]);
                }

                // if(in_array($status, [ 'PAID_MANUAL', 'ALL' ])) {
                //     $count++;
                //     $sheet->row($count, [ '', '', '', '', '', 'Pago Manual', $billings->where('status', 'PAID_MANUAL')->sum('value'), ]);
                // }

                if(in_array($status, [ 'CANCELED', 'ALL' ])) {
                    $count++;
                    $sheet->row($count, [ '', '', '', '', '', '', 'Total Cancelado', $billings->where('status', 'CANCELED')->sum('value'), $billings->where('status', 'CANCELED')->sum('paid_value') ]);
                }

                if(in_array($status, [ 'OUTDATED', 'ALL' ])) {
                    $count++;
                    $sheet->row($count, [ '', '', '', '', '', '', 'Total Vencido', $billings->where('status', 'OUTDATED')->sum('value'), $billings->where('status', 'OUTDATED')->sum('paid_value') ]);
                }
            });
        })->download('xlsx');
    }

    public function export_pdf($billings, $status, $filters) {
        return \PDF::loadView('pdf.billing_report_export', [ 'billings' => $billings, 'status' => $status, 'filters' => $filters ]);
    }
}
