<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'perfil')->get();

        return response()->json([
            'status' => 'Sucesso',
            'data' => $users
        ], 200);
    }

    public function show(string $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        if ((int)$user->id !== (int) $id) {
            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Sem permissão para esta operação'
            ], 203);
        }
        return response()->json([
            'status' => 'Sucesso',
            'message' => $user->only(['id','name', 'email','perfil'])
        ], 200);
    }
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if ((int)$user->id !== (int) $id) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Você não está autorizado para realizar esta operação'
            ], 203);
        }

        $validated = Validator::make($request->all(), [
            'name' => 'string|sometimes',
            'email' => 'string|sometimes',
            'perfil' => 'string|sometimes',
            'password' => 'required',
        ], [
            'password.required' => 'Senha obrigatória'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'Falha',
                'message' => $validated->errors()
            ], 403);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        $user->update($validated->validated());

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Usuário atualizado com sucesso'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if ((int)$user->id !== (int) $id) {
            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Sem permissão para esta operação'
            ], 203);
        }

        if (!$user) {
            return response()->json([
                'status' => 'Falha',
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Usuário deletado com suceeso'
        ], 200);
    }
}
