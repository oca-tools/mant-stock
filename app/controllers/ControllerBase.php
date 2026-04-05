<?php
// Controller base com recursos comuns
abstract class ControllerBase
{
    protected function render($view, $dados = [])
    {
        view($view, $dados);
    }

    protected function exigirCsrf()
    {
        validar_csrf();
    }

    // Valida a senha do usuario logado para confirmar operacoes sensiveis
    protected function validarSenhaOperacional($senhaInformada, &$mensagemErro = null)
    {
        $senhaInformada = (string)$senhaInformada;
        if ($senhaInformada === '') {
            $mensagemErro = 'Informe sua senha para confirmar a operacao.';
            return false;
        }

        $usuarioId = (int)($_SESSION['usuario']['id'] ?? 0);
        if ($usuarioId <= 0) {
            $mensagemErro = 'Sessao de usuario invalida. Faca login novamente.';
            return false;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorId($usuarioId);
        if (!$usuario || !password_verify($senhaInformada, $usuario['senha_hash'])) {
            $mensagemErro = 'Senha de confirmacao invalida.';
            return false;
        }

        $mensagemErro = null;
        return true;
    }

    // Exporta dados tabulares em formato CSV
    protected function exportarCsv($nomeArquivo, array $cabecalho, array $linhas)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');

        $saida = fopen('php://output', 'w');
        // BOM UTF-8 para compatibilidade com Excel
        fwrite($saida, "\xEF\xBB\xBF");
        if (!empty($cabecalho)) {
            fputcsv($saida, array_map([$this, 'sanitizarCelulaCsv'], $cabecalho), ';');
        }
        foreach ($linhas as $linha) {
            $linhaSegura = array_map([$this, 'sanitizarCelulaCsv'], (array)$linha);
            fputcsv($saida, $linhaSegura, ';');
        }
        fclose($saida);
        exit;
    }

    protected function sanitizarCelulaCsv($valor)
    {
        $texto = (string)$valor;
        if ($texto === '') {
            return $texto;
        }

        // Evita execucao de formulas quando o CSV e aberto em planilhas.
        if (preg_match('/^[=\+\-@]/', $texto) === 1) {
            return "'" . $texto;
        }

        return $texto;
    }

    // Exporta uma tabela em HTML e aciona impressao para gerar PDF
    protected function exportarPdfHtmlTabela($titulo, $subtitulo, array $cabecalho, array $linhas)
    {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><title>' . e($titulo) . '</title>';
        echo '<style>';
        echo 'body{font-family:Arial,sans-serif;margin:24px;color:#1a1a1a}';
        echo 'h2{margin:0 0 6px}';
        echo 'p{margin:0 0 16px;color:#4d4d4d}';
        echo 'table{width:100%;border-collapse:collapse}';
        echo 'th,td{border:1px solid #d5d5d5;padding:7px 8px;font-size:12px;text-align:left}';
        echo 'th{background:#f3f3f3;font-weight:700}';
        echo 'tr:nth-child(even) td{background:#fafafa}';
        echo '</style></head><body>';
        echo '<h2>' . e($titulo) . '</h2>';
        if ($subtitulo !== '') {
            echo '<p>' . e($subtitulo) . '</p>';
        }
        echo '<table><thead><tr>';
        foreach ($cabecalho as $coluna) {
            echo '<th>' . e($coluna) . '</th>';
        }
        echo '</tr></thead><tbody>';
        if (empty($linhas)) {
            echo '<tr><td colspan="' . count($cabecalho) . '">Sem dados para exportar.</td></tr>';
        } else {
            foreach ($linhas as $linha) {
                echo '<tr>';
                foreach ($linha as $valor) {
                    echo '<td>' . e($valor) . '</td>';
                }
                echo '</tr>';
            }
        }
        echo '</tbody></table>';
        echo '<script>window.print();</script>';
        echo '</body></html>';
        exit;
    }
}
