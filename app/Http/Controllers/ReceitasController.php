<?php

namespace App\Http\Controllers;

use App\Models\Receitas;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReceitasController extends Controller
{
    public function index()
    {
        try{

            $user = Auth::user();
            
            $receitas = Receitas::with(['categoria:id,nome', 'ingredientes:id,nome'])
            ->where('user_id', $user->id)
            ->get(['id', 'titulo', 'descricao', 'tempo_preparo', 'categoria_id']);


            return response()->json([
                'status' => 'Sucesso',
                'user' => $user->only(['name']),
                'total_receitas'=> count($receitas),
                'receitas' => $receitas
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'status'=> 'Falha',
                'error' => $e
            ],500);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descricao' => 'required|string',
                'modo_preparo' => 'required|string',
                'tempo_preparo' => 'required|integer|min:1',
                'categoria_id' => 'sometimes|exists:categorias,id',
                'ingredientes' => 'sometimes|array',
                'ingredientes.*.id' => 'required|exists:ingredientes,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'Falha',
                    'message' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            $data = $request->only([
                'titulo',
                'descricao',
                'modo_preparo',
                'tempo_preparo',
                'categoria_id',
            ]);

            $data['user_id'] = $user->id;

            $receita = Receitas::create($data);

            if ($request->has('ingredientes')) {
                $ingredientesIds = collect($request->ingredientes)->pluck('id')->toArray();
                $receita->ingredientes()->sync($ingredientesIds);
            }

            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Receita criada com sucesso',
                'receita' => $receita->only(['titulo', 'descricao'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Falha',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
{
    try {
        $user = Auth::user();

        $id = (int) $id;

        $receita = Receitas::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['categoria', 'ingredientes'])
            ->firstOrFail();

        return response()->json([
            'status' => 'Sucesso',
            'receita' => $receita->only('id', 'categoria_id','categoria', 'titulo', 'descricao', 'modo_preparo','tempo_preparo', 'ingredientes')
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'status' => 'Falha',
            'message' => 'Receita não encontrada.'
        ], 404);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'Erro',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, string $id)
    {
         $user = Auth::user();

        $receita = Receitas::find($id);

        if (!$receita) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Receita não encontrada.'
            ], 404);
        }

        // Garante que o usuário só possa editar a própria receita
        if ($receita->user_id !== $user->id) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Você não tem permissão para editar esta receita.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|string|max:255',
            'descricao' => 'sometimes|string',
            'modo_preparo' => 'sometimes|string',
            'tempo_preparo' => 'sometimes|integer|min:1',
            'categoria_id' => 'sometimes|exists:categorias,id',
            'ingredientes' => 'sometimes|array',
            'ingredientes.*' => 'exists:ingredientes,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Falha',
                'message' => $validator->errors()
            ], 422);
        }

        $receita->update($request->only([
            'titulo',
            'descricao',
            'modo_preparo',
            'tempo_preparo',
            'categoria_id'
        ]));

         if ($request->has('ingredientes')) {
            $receita->ingredientes()->sync($request->ingredientes);
        }

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Receita atualizada com sucesso',
            'receita' => $receita->only(['titulo', 'descricao'])
        ], 200);
    }

    public function destroy(string $id)
    {
        $user = Auth::user();
        $receita = Receitas::find($id);

        if (!$receita) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Receita não encontrada.'
            ], 404);
        }

        if ($receita->user_id !== $user->id) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Você não tem permissão para excluir esta receita.'
            ], 403);
        }

        $receita->delete();

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Receita excluída com sucesso.'
        ], 200);
    }
}
