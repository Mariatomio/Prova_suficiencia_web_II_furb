<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticação do usuário e obtenção do token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Credenciais do usuário",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="usuario@exemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login efetuado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string", example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciais inválidas"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function login(Request $request)
    {
        $usuario = Usuario::where('email', $request->email)->first();

        if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
            return response()->json(['erro' => 'Credenciais inválidas'], 401);
        }

        $token = $usuario->createToken('token')->plainTextToken;

        return response()->json([
            'usuario' => $usuario,
            'token' => $token
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout do usuário (invalida o token)",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout efetuado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function logout(Request $request)
    {
        /* $request->user()->tokens()->delete();  Aqui eu to apagando todos os meus tokens*/
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }
}
