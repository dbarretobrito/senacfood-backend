<?php

namespace App\Http\Controllers;

use App\Models\Ingredientes;
use App\Models\Receitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqController extends Controller
{
    /**
     * Texto base do sistema para todas as chamadas.
     */
    private string $systemBase = ' Você é um Chef Executivo de alta gastronomia especializado em criar refeições equilibradas, saborosas e personalizadas. Seu objetivo é gerar uma saudação ao usuário e dar sugestões de refeições completas que incluam proteínas, carboidratos, legumes e/ou vegetais, com combinações harmoniosas e bem estruturadas.
                    RETORNE SEMPRE EXCLUSIVAMENTE UM JSON VÁLIDO, seguindo exatamente o formato:
                    {
                        "saudacao": "",
                        "cafe_da_manha": {
                            "titulo": "",
                            "descricao": "",
                            "modo_preparo": "",
                            "tempo_preparo": ""
                        },
                        "almoco": {
                            "titulo": "",
                            "descricao": "",
                            "modo_preparo": "",
                            "tempo_preparo": ""
                        },
                        "jantar": {
                            "titulo": "",
                            "descricao": "",
                            "modo_preparo": "",
                            "tempo_preparo": ""
                        }
                    }

                    Não use texto fora do JSON.
                    Apenas o JSON puro.';

    /**
     * Método auxiliar para chamar a Groq e devolver o array já decodificado.
     */
    private function gerarRefeicoesComGroq(string $userPrompt): array
    {
        $apiKey = env('GROQ_API_KEY');
        $model  = env('GROQ_MODEL', 'llama-3.3-70b-versatile');

        if (empty($apiKey)) {
            throw new \Exception('GROQ_API_KEY não está configurada no ambiente.');
        }

        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        // ATENÇÃO: verify => false desliga a verificação de SSL.
        // Isso é aceitável para desenvolvimento local / trabalho de faculdade,
        // mas em produção o ideal é configurar os certificados corretamente.
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->withOptions(['verify' => false])
            ->post($endpoint, [
                'model'    => $model,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => $this->systemBase,
                    ],
                    [
                        'role'    => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);

        if ($response->failed()) {
            // loga o corpo da resposta p/ debug
            Log::error('Falha ao chamar Groq API', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new \Exception('Falha ao chamar a API da Groq.');
        }

        $json = $response->json();

        // estrutura estilo OpenAI: choices[0].message.content
        $content = $json['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            throw new \Exception('Resposta da Groq não contém conteúdo válido.');
        }

        $dados = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON inválido retornado pela Groq', [
                'content' => $content,
                'error'   => json_last_error_msg(),
            ]);

            throw new \Exception('A IA retornou um JSON inválido.');
        }

        return $dados;
    }

    public function insightPerfil()
    {
        try {
            $user = Auth::user();
            $perfil = $user?->perfil;

            if ($perfil !== null) {
                $userPrompt = 'Baseado no meu perfil ' . $perfil . ' retorne as refeições em JSON.';
            } else {
                $userPrompt = 'Retorne sugestões de refeições completas com ingredientes comuns no dia a dia em JSON.';
            }

            $dados = $this->gerarRefeicoesComGroq($userPrompt);

            $saudacao = $dados['saudacao'] ?? null;
            $cafe     = $dados['cafe_da_manha'] ?? null;
            $almoco   = $dados['almoco'] ?? null;
            $jantar   = $dados['jantar'] ?? null;

            return response()->json([
                'saudacao'      => $saudacao,
                'cafe_da_manha' => $cafe,
                'almoco'        => $almoco,
                'jantar'        => $jantar,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro em insightPerfil', ['exception' => $e]);

            if (env('APP_DEBUG')) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return response()->json(['error' => 'IA indisponível'], 500);
        }
    }

    public function insightIngredientes()
    {
        try {
            $user = Auth::user();
            $ingredientes = Ingredientes::where('user_id', $user->id)->get();

            if ($ingredientes->count() > 2) {
                // transforma a collection em uma lista mais "limpa" pra IA
                $lista = $ingredientes->pluck('nome')->implode(', ');
                $userPrompt = 'Baseado nos meus ingredientes: ' . $lista . ' retorne as refeições em JSON.';
            } else {
                $userPrompt = 'Retorne sugestões de refeições completas com ingredientes comuns no dia a dia em JSON.';
            }

            $dados = $this->gerarRefeicoesComGroq($userPrompt);

            $saudacao = $dados['saudacao'] ?? null;
            $cafe     = $dados['cafe_da_manha'] ?? null;
            $almoco   = $dados['almoco'] ?? null;
            $jantar   = $dados['jantar'] ?? null;

            return response()->json([
                'saudacao'      => $saudacao,
                'cafe_da_manha' => $cafe,
                'almoco'        => $almoco,
                'jantar'        => $jantar,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro em insightIngredientes', ['exception' => $e]);

            if (env('APP_DEBUG')) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return response()->json(['error' => 'IA indisponível'], 500);
        }
    }

    public function insightReceitas()
    {
        try {
            $user = Auth::user();
            $receitas = Receitas::where('user_id', $user->id)->get();

            if ($receitas->count() > 2) {
                // idem: lista mais amigável para o prompt
                $lista = $receitas->pluck('titulo')->implode(', ');
                $userPrompt = 'Baseado nas minhas receitas cadastradas: ' . $lista . ' retorne as refeições em JSON.';
            } else {
                $userPrompt = 'Retorne sugestões de refeições completas com ingredientes comuns no dia a dia em JSON.';
            }

            $dados = $this->gerarRefeicoesComGroq($userPrompt);

            $saudacao = $dados['saudacao'] ?? null;
            $cafe     = $dados['cafe_da_manha'] ?? null;
            $almoco   = $dados['almoco'] ?? null;
            $jantar   = $dados['jantar'] ?? null;

            return response()->json([
                'saudacao'      => $saudacao,
                'cafe_da_manha' => $cafe,
                'almoco'        => $almoco,
                'jantar'        => $jantar,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro em insightReceitas', ['exception' => $e]);

            if (env('APP_DEBUG')) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return response()->json(['error' => 'IA indisponível'], 500);
        }
    }
}
