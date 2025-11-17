<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoriasController extends Controller
{
    public function index()
    {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'Não autenticado',
                    'message' => 'Token inválido ou ausente.'
                ], 401);
            }
            
            $categorias = Categorias::where('user_id', $user->id)->select('id', 'nome')->get();
            
            return response()->json([
            'status' => 'Sucesso',
            'data' => $categorias
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Falha',
                'message' => $validator->errors()
            ], 422);
        }

        $categoria = Categorias::create([
            'user_id' => $user->id,
            'nome' => $request->input('nome')
        ]);

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Categoria criada com sucesso!',
            'data' => $categoria->only('id','nome')
        ], 201);
    }
     public function show(string $id)
    {
        $user = Auth::user();

        $categoria = Categorias::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$categoria) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Categoria não encontrada ou não pertence ao usuário.'
            ], 404);
        }

        return response()->json([
            'status' => 'Sucesso',
            'data' => $categoria->only('id', 'nome')
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Falha',
                'message' => $validator->errors()
            ], 422);
        }

        $categoria = Categorias::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$categoria) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Categoria não encontrada.'
            ], 404);
        }

        $categoria->update([
            'nome' => $request->input('nome')
        ]);

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Categoria atualizada com sucesso!',
            'data' => $categoria->only('id', 'nome')
        ], 200);
    }
   public function destroy(string $id)
    {
        $user = Auth::user();

        $categoria = Categorias::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$categoria) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $categoria->delete();

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Categoria excluída com sucesso!',
            'categoria' => $categoria->only('nome')
        ], 200);
    }
}
