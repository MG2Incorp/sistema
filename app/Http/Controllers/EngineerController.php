<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EngineerController extends Controller
{
    private $data = array();

    public function index() {
        if(!in_array(\Auth::user()->role, ['ADMIN', 'INCORPORATOR', 'ENGINEER'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $this->data['projects'] = \Auth::user()->projects;
        $this->data['stages'] = \App\Stage::all();

        return view('engineer.index', $this->data);
    }

    public function stage(Request $request) {
        if(!in_array(\Auth::user()->role, ['ADMIN', 'INCORPORATOR', 'ENGINEER'])) return redirect()->back()->with('error', 'Você não tem permissão para realizar essa operação.');
        if(\Auth::user()->role != 'ADMIN' && !\Auth::user()->checkPermission($request->project, ['UPDATE_CONSTRUCTION'])) return redirect()->back()->with('error', 'Você não tem permissão para realizar essa operação.');

        if(!$project = \App\Project::find($request->project)) return redirect()->back()->with('error', 'Empreendimento não encontrado.');

        $project->update([ 'map_stages_position' => $request->position ]);

        $old_ids = array();
        if($request->has('old_stage')) $old_ids = array_keys($request->old_stage);

        if($project->stages && $project->stages->count()) {
            foreach ($project->stages as $key => $st) {
                if(!in_array($st->id, $old_ids)) $st->delete();
            }
        }

        if(count($old_ids)) {
            foreach ($request->old_stage as $key => $st) {
                $start_at = null;
                if($request->old_month[$key] && $request->old_year[$key]) $start_at = \Carbon\Carbon::createFromDate($request->old_year[$key], $request->old_month[$key], 1)->toDateString();

                \App\ProjectStage::updateOrCreate(
                    [
                        'id'            => $key,
                        'project_id'    => $request->project,
                    ],
                    [
                        'stage_id'      => $st,
                        'percentage'    => $request->old_percentage[$key],
                        'is_visible'    => $request->old_visible[$key],
                        'start_at'      => $start_at,
                        'show_start_at' => $request->old_show_start[$key]
                    ]
                );
            }
        }


        if($request->has('stage')) {
            foreach ($request->stage as $key => $stage) {
                $start_at = null;
                if($request->month[$key] && $request->year[$key]) $start_at = \Carbon\Carbon::createFromDate($request->year[$key], $request->month[$key], 1)->toDateString();

                \App\ProjectStage::updateOrCreate(
                    [
                        'project_id'    => $request->project,
                        'stage_id'      => $stage,
                    ],
                    [
                        'percentage'    => $request->percentage[$key],
                        'is_visible'    => $request->visible[$key],
                        'start_at'      => $start_at,
                        'show_start_at' => $request->show_start[$key]
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Informações atualizadas');
    }
}
