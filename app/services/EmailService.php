<?php
// Servico simples de envio de e-mail
class EmailService
{
    public static function enviarConviteCadastro($emailDestino, $nomeSugerido, $tipoUsuario, $linkAceite)
    {
        $config = require __DIR__ . '/../config/config.php';
        $mailConfig = $config['mail'] ?? [];

        $remetenteEmail = (string)($mailConfig['remetente_email'] ?? 'nao-responda@localhost');
        $remetenteNome = (string)($mailConfig['remetente_nome'] ?? 'Sistema de Estoque');
        $modoTeste = !empty($mailConfig['modo_teste']);

        $assunto = 'Convite para cadastro no OCA MantStock';
        $nomeExibicao = $nomeSugerido !== '' ? $nomeSugerido : 'colaborador(a)';

        $corpoHtml = '<html><body style="font-family:Arial,sans-serif;color:#1f2937;">'
            . '<h2>Cadastro de usuário - OCA MantStock</h2>'
            . '<p>Olá, ' . e($nomeExibicao) . '.</p>'
            . '<p>Você recebeu um convite para criar sua conta no sistema de estoque da manutenção.</p>'
            . '<p><strong>Perfil de acesso:</strong> ' . e($tipoUsuario) . '</p>'
            . '<p><a href="' . e($linkAceite) . '" style="display:inline-block;padding:10px 16px;background:#1d5ca7;color:#fff;text-decoration:none;border-radius:8px;">Criar minha conta</a></p>'
            . '<p>Se o botão não abrir, copie este link no navegador:</p>'
            . '<p>' . e($linkAceite) . '</p>'
            . '<p style="color:#6b7280;">Mensagem automática. Não responda este e-mail.</p>'
            . '</body></html>';

        $cabecalhos = [];
        $cabecalhos[] = 'MIME-Version: 1.0';
        $cabecalhos[] = 'Content-type: text/html; charset=UTF-8';
        $cabecalhos[] = 'From: ' . $remetenteNome . ' <' . $remetenteEmail . '>';

        if ($modoTeste) {
            self::registrarEmLog($emailDestino, $assunto, $corpoHtml);
            return [
                'sucesso' => true,
                'mensagem' => 'Modo teste ativo: e-mail gravado em logs/emails.log.'
            ];
        }

        $enviado = @mail($emailDestino, $assunto, $corpoHtml, implode("\r\n", $cabecalhos));
        if ($enviado) {
            return [
                'sucesso' => true,
                'mensagem' => 'Convite enviado para o e-mail informado.'
            ];
        }

        self::registrarEmLog($emailDestino, $assunto, $corpoHtml);
        return [
            'sucesso' => false,
            'mensagem' => 'Falha no envio automático. O conteúdo foi salvo em logs/emails.log.'
        ];
    }

    private static function registrarEmLog($emailDestino, $assunto, $corpoHtml)
    {
        $linha = '[' . date('Y-m-d H:i:s') . ']'
            . ' PARA=' . $emailDestino
            . ' | ASSUNTO=' . $assunto
            . PHP_EOL
            . $corpoHtml
            . PHP_EOL
            . str_repeat('-', 80)
            . PHP_EOL;

        $arquivo = __DIR__ . '/../../logs/emails.log';
        @file_put_contents($arquivo, $linha, FILE_APPEND);
    }
}
