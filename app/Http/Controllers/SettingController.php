<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    private $data = array();

    public function index(Request $request) {
        if(!\Auth::user()->role == "ADMIN") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if($request->has('history')) {

            if(is_array($request->history) && count($request->history)) {
                foreach ($request->history as $key => $hist) {
                    if($hist) {
                        $explode = explode('-', $key);
                        $history = \App\MonetaryCorrectionIndexHistory::where('indexes_id', $request->index_id)->where('month', $explode[1])->where('year', $explode[0])->first();

                        if($history) {
                            if(!$history->value) {
                                $history->update([ 'value' => toCoin($hist), 'valid_at' => \Carbon\Carbon::createFromDate($explode[0], $explode[1], null)->endOfMonth()->toDateString() ]);
                            }
                        } else {
                            $history = \App\MonetaryCorrectionIndexHistory::firstOrCreate(
                                [ 'indexes_id' => $request->index_id, 'month' => $explode[1], 'year' => $explode[0] ],
                                [ 'value' => toCoin($hist), 'valid_at' => \Carbon\Carbon::createFromDate($explode[0], $explode[1], null)->endOfMonth()->toDateString() ]
                            );
                        }
                    }
                }
            }

            return redirect()->route('settings');
        }

        if($request->has('index')) {
            $index = \App\MonetaryCorrectionIndex::create([ 'name' => $request->index ]);

            for ($i = date('Y'); $i < date('Y') + 1; $i++) {
                foreach (getMonths() as $key => $month) {
                    $history = \App\MonetaryCorrectionIndexHistory::firstOrCreate(
                        [ 'indexes_id' => $index->id, 'month' => $key, 'year' => $i ],
                        [ 'value' => 0 ]
                    );
                }
            }

            return redirect()->route('settings');
        }

        if($request->has('status')) {
            foreach ($request->status as $key => $status) {
                \App\Color::updateOrCreate([ 'status' => $key ], [ 'color' => str_replace('#', '', $status) ]);
            }
            return redirect()->route('settings');
        }

        if($request->has('stages') || $request->has('old_stages')) {
            if($request->has('old_stages')) {
                foreach ($request->old_stages as $key => $stage) {
                    \App\Stage::find($key)->update([ 'name' => $stage, 'icon' => $request->old_icons[$key] ]);
                }
            }

            if($request->has('stages')) {
                foreach ($request->stages as $key => $stage) {
                    \App\Stage::create([ 'name' => $stage, 'icon' => $request->icons[$key] ]);
                }
            }

            return redirect()->route('settings');
        }

        $this->data['colors'] = \App\Color::all()->mapWithKeys(function ($item) {
            return [ $item->status => $item->color ];
        })->toArray();

        $this->data['stages'] = \App\Stage::all();
        $this->data['indexes'] = \App\MonetaryCorrectionIndex::all();

        return view('settings.index', $this->data);
    }
}
