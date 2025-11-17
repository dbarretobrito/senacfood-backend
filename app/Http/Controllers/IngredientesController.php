<?php

namespace App\Http\Controllers;

use App\Models\Ingredientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngredientesController extends Controller
{
     public function index()
    {
        $user = Auth::user();

        $ingredientes = Ingredientes::where('user_id', $user->id)->select('id', 'nome')->get();

        return response()->json([
            'status' => 'Sucesso',
            'data' => $ingredientes
        ], 200);
    }
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $ingrediente = Ingredientes::create([
            'user_id' => $user->id,
            'nome' => $validated['nome'],
        ]);

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Ingrediente criado com sucesso!',
            'data' => $ingrediente->only(['id','nome'])
        ], 201);
    }
 public function show(string $id)
    {
        $user = Auth::user();

        $ingrediente = Ingredientes::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$ingrediente) {
            return response()->json([
                'status' => 'Erro',
                'message' => 'Ingrediente não encontrado.'
            ], 404);
        }

        return response()->json([
            'status' => 'Sucesso',
            'data' => $ingrediente->only('id', 'nome')
        ], 200);
    }
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        $ingrediente = Ingredientes::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$ingrediente) {
            return response()->json([
                'status' => 'Erro',
                'message' => 'Ingrediente não encontrado.'
            ], 404);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $ingrediente->update($validated);

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Ingrediente atualizado com sucesso!',
            'data' => $ingrediente->only('id', 'nome')
        ], 200);
    }
    public function destroy(string $id)
    {
        $user = Auth::user();

        $ingrediente = Ingredientes::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$ingrediente) {
            return response()->json([
                'status' => 'Erro',
                'message' => 'Ingrediente não encontrado.'
            ], 404);
        }

        $ingrediente->delete();

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Ingrediente excluído com sucesso!'
        ], 200);
    }
}
