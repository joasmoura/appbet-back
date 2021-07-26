<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function registrar(Request $request){

    }

    public function login(Request $request){
        $dados = $request->all();

         if(Auth::attempt(['username' => $dados['username'], 'password' => $dados['password']])){
            $user = auth()->user();

            return response()->json([
                'status' => true,
                'usuario' => $user,
                'authenticationToken' => $user->createToken($dados['username'])->accessToken
            ],Response::HTTP_OK);
         }else{
             return response()->json([
                'status' => false
             ],Response::HTTP_OK);
         }
    }
}
