<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API de Comandas"
 * )
 *
 *  * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Ambiente público via ngrok"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="API Token"
 * )
 *
 * @OA\Tag(
 *     name="Comandas",
 *     description="Operações relacionadas a comandas"
 * )
 */
class ComandaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/comandas",
     *     summary="Listar usuários com suas comandas e produtos",
     *     tags={"Comandas"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="idUsuario", type="integer", example=1),
     *                 @OA\Property(property="nomeUsuario", type="string", example="Maria"),
     *                 @OA\Property(property="telefoneUsuario", type="string", example="999999999")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function index()
    {
        try {
            $usuarios = Usuario::select('id as idUsuario', 'nomeUsuario', 'telefoneUsuario')->get();
            return response()->json($usuarios, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erro ao buscar usuários: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/comandas/{id}",
     *     summary="Detalhes do usuário com suas comandas e produtos",
     *     tags={"Comandas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do usuário e seus produtos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="idUsuario", type="integer", example=1),
     *             @OA\Property(property="nomeUsuario", type="string", example="Maria"),
     *             @OA\Property(property="telefoneUsuario", type="string", example="999999999"),
     *             @OA\Property(
     *                 property="produtos",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="nome", type="string", example="Produto A"),
     *                     @OA\Property(property="preco", type="number", format="float", example=9.99)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function show($id)
    {
        try {
            $usuario = Usuario::select('id', 'nomeUsuario', 'telefoneUsuario')
                ->with(['comandas.produtos' => function ($query) {
                    $query->select('produtos.id', 'nome', 'preco');
                }])
                ->findOrFail($id);

            $produtos = $usuario->comandas
                ->flatMap->produtos
                ->unique('id')
                ->map(fn($produto) => $produto->makeHidden('pivot'))
                ->values();

            return response()->json([
                'idUsuario' => $usuario->id,
                'nomeUsuario' => $usuario->nomeUsuario,
                'telefoneUsuario' => $usuario->telefoneUsuario,
                'produtos' => $produtos
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erro ao buscar comanda: ' . $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/comandas",
     *     summary="Criar uma nova comanda com usuário e produtos",
     *     tags={"Comandas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do usuário e produtos para a comanda",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="idUsuario", type="integer", example=1),
     *             @OA\Property(property="nomeUsuario", type="string", example="Maria"),
     *             @OA\Property(property="telefoneUsuario", type="string", example="999999999"),
     *             @OA\Property(
     *                 property="produtos",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"id","nome","preco"},
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="nome", type="string", example="Produto A"),
     *                     @OA\Property(property="preco", type="number", format="float", example=9.99)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Comanda criada com sucesso"),
     *     @OA\Response(response=400, description="Dados do produto incompletos"),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function store(Request $request)
    {
        try {
            $usuario = Usuario::updateOrCreate(
                ['id' => $request->input('idUsuario')],
                [
                    'nomeUsuario' => $request->input('nomeUsuario'),
                    'telefoneUsuario' => $request->input('telefoneUsuario')
                ]
            );

            $comanda = Comanda::create(['idUsuario' => $usuario->id]);

            $produtosData = $request->input('produtos', []);
            $produtoIds = [];

            foreach ($produtosData as $produtoData) {
                if (!isset($produtoData['id'], $produtoData['nome'], $produtoData['preco'])) {
                    return response()->json([
                        'error' => 'Dados do produto incompletos.'
                    ], 400);
                }

                $produto = Produto::updateOrCreate(
                    ['id' => $produtoData['id']],
                    ['nome' => $produtoData['nome'], 'preco' => $produtoData['preco']]
                );
                $produtoIds[] = $produto->id;
            }

            $comanda->produtos()->sync($produtoIds);

            return response()->json([
                'id' => $comanda->id,
                'idUsuario' => $usuario->id,
                'nomeUsuario' => $usuario->nomeUsuario,
                'telefoneUsuario' => $usuario->telefoneUsuario,
                'produtos' => $produtosData
            ], 201); // Created

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Erro no banco de dados: ' . $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erro inesperado: ' . $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/comandas/{id}",
     *     summary="Atualizar os produtos de uma comanda existente",
     *     tags={"Comandas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da comanda",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Lista de produtos para atualizar na comanda",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="produtos",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"id","nome","preco"},
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="nome", type="string", example="Produto A"),
     *                     @OA\Property(property="preco", type="number", format="float", example=9.99)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Comanda atualizada com sucesso"),
     *     @OA\Response(response=404, description="Comanda não encontrada"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $comanda = Comanda::findOrFail($id);

            $produtosData = $request->input('produtos', []);
            $produtoIds = [];

            foreach ($produtosData as $produtoData) {
                $produto = Produto::updateOrCreate(
                    ['id' => $produtoData['id']],
                    ['nome' => $produtoData['nome'], 'preco' => $produtoData['preco']]
                );
                $produtoIds[] = $produto->id;
            }

            $comanda->produtos()->syncWithoutDetaching($produtoIds);
            /* $comanda->produtos()->sync($produtoIds); */  //remove os itens

            return response()->json([
                'id' => $comanda->id,
                'produtos' => $produtosData
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Comanda não encontrada'], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/comandas/{id}",
     *     summary="Excluir uma comanda",
     *     tags={"Comandas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da comanda",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Comanda removida com sucesso"),
     *     @OA\Response(response=404, description="Comanda não encontrada"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function destroy($id)
    {
        try {
            $comanda = Comanda::findOrFail($id);
            $comanda->produtos()->detach();
            $comanda->delete();

            return response()->json("", 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Comanda não encontrada'], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Erro no banco de dados: ' . $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erro inesperado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/comandas/minha",
     *     summary="Listar comandas e produtos do usuário autenticado",
     *     tags={"Comandas"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de comandas com produtos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="comandas",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="produtos", type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=10),
     *                             @OA\Property(property="nome", type="string", example="Produto A"),
     *                             @OA\Property(property="preco", type="number", format="float", example=9.99)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=500, description="Erro interno do servidor")
     * )
     */
    public function minhasComandas(Request $request)
    {
        try {
            return $request->user()->load('comandas.produtos');
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao carregar as minhas comandas',
                'details' => $th->getMessage()
            ], 500);
        }
    }
}
