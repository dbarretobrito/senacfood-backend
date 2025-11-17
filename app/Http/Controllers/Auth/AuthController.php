<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){ 

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'perfil' => 'sometimes',
            'password' => 'required|confirmed'
        ], [
            'name.require' => 'Nome obrigatório',
            'email.required' => 'E-mail obrigatório',
            'email.unique' => 'E-mail em uso',
            'password.required' => 'Senha obrigatória', 
            'password.confirmed' => 'Credenciais inváldias'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'Falha',
                'message' => $validator->errors()
            ], 403);
        }

        $data = $request->all();
        User::create($data);

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Usuário criado com sucesso'
        ],200);
    }

    public function login (Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=> 'Falha',
                'message' => $validator->errors()
            ], 400);
        };

        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
            $user = Auth::user();
            $user->tokens()->delete();

            

            $response['token'] = $user->createToken('APIToken')->plainTextToken;
            $response['email'] = $user->email;

            return response()->json([
                'status' => 'success',
                'message'=>'Login successfully',
                'data'=> $response
            ],200);
        }else{
            return response()->json([
                'status' => 'Falha',
                'message' => 'Credenciais inválidas'
            ], 400);
        }
    }

    public function logout(){
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Logout realizado com sucesso'
        ], 200);
    }
}
