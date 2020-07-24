<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Project;
use App\Building;
use App\Block;
use App\Property;

use Auth;

class MapController extends Controller
{
    private $data = array();

    public function all() {
        if (Auth::user()->role == 'ADMIN') {
            $this->data['projects'] = Project::all();
        } else {
            $this->data['projects'] = Auth::user()->projects;
        }
        return view('map.all', $this->data);
    }

    public function index(Request $request) {
        if ($request->has('allow')) {
            if (!$request->has('property_id')) return redirect()->back()->with('error', 'Não foi possível concluir essa operação.');

            $property = Property::find($request->property_id);
            if (!$property) return redirect()->back()->with('error', 'Não foi possível concluir essa operação.');

            if(!Auth::user()->checkPermission($property->block->building->project->id, ['PROPERTY_STATUS']) &&  !Auth::user()->role == "ADMIN") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

            $property->situation = 'AVAILABLE';
            $property->save();

            return redirect()->back()->with('success', 'Status alterado com sucesso.');
        }

        if ($request->has('block')) {
            if (!$request->has('property_id')) return redirect()->back()->with('error', 'Não foi possível concluir essa operação.');

            $property = Property::find($request->property_id);
            if (!$property) return redirect()->back()->with('error', 'Não foi possível concluir essa operação.');

            if(!Auth::user()->checkPermission($property->block->building->project->id, ['PROPERTY_STATUS']) &&  !Auth::user()->role == "ADMIN") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

            $property->situation = 'BLOCKED';
            $property->save();

            return redirect()->back()->with('success', 'Status alterado com sucesso.');
        }

        if ($request->has('delete')) {
            if (!$request->has('property_id')) return redirect()->back()->with('error', 'Não foi possível concluir essa operação.');

            $property = Property::find($request->property_id);
            if (!$property) return redirect()->back()->with('error', 'Não foi possível concluir essa operação.');

            if(!Auth::user()->checkPermission($property->block->building->project->id, ['PROPERTY_DELETE']) &&  !Auth::user()->role == "ADMIN") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

            if($property->proposals_actives->count()) return redirect()->back()->with('error', 'Não foi possível deletar esse imóvel, pois ele possui propostas em aberto.');

            $property->delete();

            return redirect()->back()->with('success', 'Imóvel deletado com sucesso.');
        }

        if ($request->has('block_delete')) {
            if (!$request->has('block_id')) return redirect()->back()->with('error', 'Não foi possível concluir essa operação.');

            $block = Block::find($request->block_id);
            if (!$block) return redirect()->back()->with('error', 'Não foi possível concluir essa operação.');

            if(!Auth::user()->checkPermission($block->building->project->id, ['BLOCK_DELETE']) &&  !Auth::user()->role == "ADMIN") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

            $impede = false;
            if ($block->properties->count()) {
                foreach ($block->properties as $key => $property) {
                    if($property->proposals_actives->count()) {
                        $impede = true;
                        break;
                    }
                }
            }

            if ($impede) return redirect()->back()->with('error', 'Não foi possível deletar essa quadra, pois ela possui imóveis com propostas em aberto.');

            $block->delete();

            return redirect()->back()->with('success', 'Quadra/Andar deletado com sucesso.');
        }

        if ($request->has('insert_block')) {
            if(!Auth::user()->checkPermission($request->empreendimento, ['BLOCK_CREATE']) &&  !Auth::user()->role == "ADMIN") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

            $block = Block::create([
                'label'       => $request->label,
                'building_id' => $request->predio
            ]);

            return redirect()->back()->with('success', 'Quadra/Andar criado com sucesso.');
        }

        if (!$request->has('empreendimento') || !$request->has('predio')) return redirect()->route('projects.index')->with('error', 'Não foi possível completar a operação.');

        $project = Project::find($request->empreendimento);
        if (!$project) return redirect()->route('projects.index');

        $building = Building::find($request->predio);
        if (!$building) return redirect()->route('projects.index');

        if ($building->project_id != $request->empreendimento) return redirect()->route('projects.index')->with('error', 'Não foi possível completar a operação.');

        $this->data['project'] = $project;
        $this->data['building'] = $building;
        $this->data['blocks'] = $building->blocks;
        $this->data['accounts'] = $project->all_accounts;

        return view('map.index', $this->data);
    }

    public function export(Request $request) {

        if(!$request->has('type') || !$request->has('project')) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        $project = \App\Project::find($request->project);
        if(!$project || !\Auth::user()->projects->contains($project)) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        switch ($request->type) {
            case 'SHEET':

                \Excel::create(str_slug($project->name), function($excel) use ($project) {
                    $excel->sheet('Empreendimento', function($sheet) use ($project) {

                        for ($i = 1; $i < 6; $i++) {
                            $sheet->cells('A'.$i.':F'.$i, function($cells) { $cells->setBackground('#EEEEEE'); });
                        }

                        $count = 1;
                        $sheet->mergeCells('B1:F1');
                        $sheet->row($count, [ 'NOME', $project->name ]);

                        $count++;
                        $sheet->mergeCells('B2:F2');
                        $sheet->row($count, [ 'PREVISAO DE ENTREGA', formatData($project->finish_at) ]);

                        $count++;
                        $sheet->mergeCells('B3:F3');
                        $sheet->row($count, [ 'STATUS', $project->status ]);

                        $count++;
                        $sheet->mergeCells('B4:F4');
                        $sheet->row($count, [ 'LOCAL', $project->local ]);

                        $count++;
                        $sheet->mergeCells('B5:F5');
                        $sheet->row($count, [ 'OBSERVACOES', $project->notes ]);

                        $count++;
                        $count++;

                        $sheet->row($count, [
                            'BLOCO',
                            'QUADRA/ANDAR',
                            'NUMERO',
                            'STATUS',
                            'VALOR (R$)',
                            'AREA (m²)',
                        ]);

                        foreach($project->buildings->sortBy('name') as $building) {
                            foreach($building->blocks->sortBy('label') as $block) {
                                foreach($block->properties->sortBy('number') as $property) {
                                    $count++;
                                    $sheet->setColumnFormat([ 'E'.$count => '0.00', 'F'.$count => '0.00' ]);

                                    if($property->situation == 'AVAILABLE') {
                                        if($property->proposals_actives->count() == 0) {
                                            $status = 'Disponível';
                                        } else {
                                            if($property->proposals_actives->first()->status == 'SOLD') {
                                                $status = 'Vendido';
                                            } else {
                                                $status = 'Análise';
                                            }
                                        }
                                    } else {
                                        $status = 'Bloqueado';
                                    }

                                    $sheet->row($count, [
                                        $property->block->building->name,
                                        $property->block->label,
                                        $property->number,
                                        $status,
                                        $property->value,
                                        $property->size,
                                    ]);
                                }
                            }
                        }

                        $count++;

                        $count++;
                        $sheet->mergeCells('A'.$count.':F'.$count);
                        $sheet->row($count, [ 'Tabela de Valores para Simples Conferência, favor verificar disponibilidade no sistema MG2 Incorp.' ]);

                        $count++;
                        $sheet->mergeCells('A'.$count.':F'.$count);
                        $sheet->row($count, [ 'Valores sujeitos a alteração sem aviso prévio.' ]);
                    });
                })->download('xlsx');

            break;
            case 'PDF':

                $name = str_slug($project->name).'.pdf';
                $pdf = \PDF::loadView('pdf.project_export', [ 'project' => $project ]);

                return $pdf->download($name);

            break;
            default: return redirect()->back()->with('error', 'Não foi possível completar a operação.'); break;
        }
    }
}
