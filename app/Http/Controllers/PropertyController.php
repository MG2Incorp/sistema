<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Property;
use App\Block;

use Log;
use Exception;
use Auth;

class PropertyController extends Controller
{
    public function store(Request $request) {
        $block = Block::find($request->block_id);
        if (!$block) return redirect()->back()->with('error', 'Não foi possível concluir a operação.');

        if(!Auth::user()->checkPermission($block->building->project->id, ['PROPERTY_CREATE'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        try {
            $property = Property::create([
                'block_id'              => $request->block_id,
                'number'                => $request->number,
                'value'                 => toCoin($request->value),
                'notes'                 => $request->notes,
                'size'                  => toCoin($request->size),
                'dimensions'            => $request->dimensions,
                'owner'                 => @$request->owner,
                'account_id'            => $request->account_id,
                'numero_matricula'      => $request->numero_matricula,
                'cadastro_imobiliario'  => $request->cadastro_imobiliario
            ]);
            return redirect()->back()->with('success', 'Imóvel adicionado com sucesso');
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível adicionar o imóvel.');
        }
    }

    public function update(Request $request, $id) {
        $property = Property::find($id);
        if (!$property) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        if(!Auth::user()->checkPermission($property->block->building->project->id, ['PROPERTY_EDIT'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        try {
            $property->update([
                'number'                => $request->number,
                'value'                 => toCoin($request->value),
                'notes'                 => $request->notes,
                'size'                  => toCoin($request->size),
                'dimensions'            => $request->dimensions,
                'owner'                 => @$request->owner,
                'account_id'            => $request->account_id,
                'numero_matricula'      => $request->numero_matricula,
                'cadastro_imobiliario'  => $request->cadastro_imobiliario
            ]);
            return redirect()->back()->with('success', 'Imóvel editado com sucesso');
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível editar o imóvel.');
        }
    }
}
