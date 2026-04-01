<?php
// Roteador simples do sistema
class Router
{
    private $rotas = [];

    public function adicionar($metodo, $caminho, $acao, $opcoes = [])
    {
        $this->rotas[] = [
            'metodo' => strtoupper($metodo),
            'caminho' => $caminho,
            'acao' => $acao,
            'opcoes' => $opcoes
        ];
    }

    public function executar($metodo, $uri)
    {
        foreach ($this->rotas as $rota) {
            if ($rota['metodo'] !== strtoupper($metodo)) {
                continue;
            }

            $padrao = preg_replace('#\{[a-zA-Z_]+\}#', '([0-9]+)', $rota['caminho']);
            $padrao = '#^' . $padrao . '$#';

            if (preg_match($padrao, $uri, $matches)) {
                array_shift($matches);

                if (!empty($rota['opcoes']['auth'])) {
                    AuthMiddleware::verificar();
                }

                if (!empty($rota['opcoes']['admin'])) {
                    AuthMiddleware::verificarAdmin();
                }

                if (!empty($rota['opcoes']['tipos'])) {
                    AuthMiddleware::verificarTipos($rota['opcoes']['tipos']);
                }

                [$controller, $metodoController] = $rota['acao'];
                $instancia = new $controller();
                call_user_func_array([$instancia, $metodoController], $matches);
                return;
            }
        }

        http_response_code(404);
        echo 'Rota nao encontrada.';
    }
}
