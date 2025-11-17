<?php

namespace App\Http\Controllers;

use App\Models\Receitas;
use Illuminate\Http\Request;

class FavoritosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
    {
        $favoritos = $request->user()->favoritos()->with('user')->get();

        return response()->json([
            'status' => 'Sucesso',
            'data' => $favoritos
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function toggle(Request $request, $receitaId)
    {
        $user = $request->user();

        if(!Receitas::find($receitaId)){
            return response()->json([
                'status' => 'Falha',
                'message' => 'Receita não encontrada'
            ], 404);
        }
        $favorita = $user->favoritos()->where('receita_id', $receitaId)->exists();

        if($favorita){
            $user->favoritos()->detach($receitaId);
            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Receita desfavoritada com sucesso'
            ],201);
        }else{
            $user->favoritos()->attach($receitaId);
            return response()->json([
                'status' => 'Sucesso',
                'message'=> 'Receita adicionada aos favoritos'
            ], 201);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
