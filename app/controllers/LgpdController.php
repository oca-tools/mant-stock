<?php
// Controller para adequacao operacional a LGPD
class LgpdController extends ControllerBase
{
    public function politica()
    {
        $config = require __DIR__ . '/../config/config.php';
        $this->render('lgpd/politica', [
            'versaoPolitica' => (string)($config['lgpd']['versao_politica'] ?? '2026-04'),
            'emailEncarregado' => (string)($config['lgpd']['email_encarregado'] ?? 'privacidade@oca-tools.com.br')
        ]);
    }

    public function formAceite()
    {
        $config = require __DIR__ . '/../config/config.php';
        $versaoPolitica = (string)($config['lgpd']['versao_politica'] ?? '2026-04');

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorId((int)$_SESSION['usuario']['id']);

        if (!empty($usuario['lgpd_aceite_at']) && (string)($usuario['lgpd_aceite_versao'] ?? '') === $versaoPolitica) {
            redirect(url('dashboard'));
        }

        $this->render('lgpd/aceite', [
            'erro' => null,
            'versaoPolitica' => $versaoPolitica,
            'emailEncarregado' => (string)($config['lgpd']['email_encarregado'] ?? 'privacidade@oca-tools.com.br')
        ]);
    }

    public function aceitar()
    {
        $this->exigirCsrf();

        $aceite = (string)($_POST['aceite_lgpd'] ?? '');
        if ($aceite !== '1') {
            $config = require __DIR__ . '/../config/config.php';
            $this->render('lgpd/aceite', [
                'erro' => 'Voce precisa confirmar o aceite para continuar.',
                'versaoPolitica' => (string)($config['lgpd']['versao_politica'] ?? '2026-04'),
                'emailEncarregado' => (string)($config['lgpd']['email_encarregado'] ?? 'privacidade@oca-tools.com.br')
            ]);
            return;
        }

        $config = require __DIR__ . '/../config/config.php';
        $versaoPolitica = (string)($config['lgpd']['versao_politica'] ?? '2026-04');

        $usuarioId = (int)($_SESSION['usuario']['id'] ?? 0);
        $usuarioModel = new Usuario();
        $usuarioModel->registrarAceiteLgpd($usuarioId, $versaoPolitica, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

        $_SESSION['usuario']['lgpd_aceite_at'] = date('Y-m-d H:i:s');
        $_SESSION['usuario']['lgpd_aceite_versao'] = $versaoPolitica;

        LogService::registrar(
            $usuarioId,
            'lgpd_aceite',
            'Usuario aceitou a politica de privacidade',
            'usuarios',
            $usuarioId,
            null,
            ['versao_politica' => $versaoPolitica]
        );

        flash_set('lgpd', 'Aceite de privacidade registrado com sucesso.', 'success');
        redirect(url('dashboard'));
    }

    public function solicitacoes()
    {
        $model = new SolicitacaoLgpd();
        $flash = flash_get('lgpd_solicitacoes');
        $this->render('lgpd/solicitacoes', [
            'erro' => null,
            'flash' => $flash,
            'itens' => $model->listarRecentes(150)
        ]);
    }

    public function criarSolicitacao()
    {
        $this->exigirCsrf();

        $titularNome = trim((string)($_POST['titular_nome'] ?? ''));
        $titularEmail = trim((string)($_POST['titular_email'] ?? ''));
        $tipo = trim((string)($_POST['tipo_solicitacao'] ?? ''));
        $descricao = trim((string)($_POST['descricao'] ?? ''));

        $tiposPermitidos = ['acesso', 'correcao', 'anonimizacao', 'eliminacao', 'portabilidade', 'oposicao', 'revogacao'];
        if ($titularNome === '' || $titularEmail === '' || $descricao === '' || !in_array($tipo, $tiposPermitidos, true)) {
            $model = new SolicitacaoLgpd();
            $this->render('lgpd/solicitacoes', [
                'erro' => 'Preencha os campos obrigatorios da solicitacao LGPD.',
                'flash' => null,
                'itens' => $model->listarRecentes(150)
            ]);
            return;
        }

        if (!filter_var($titularEmail, FILTER_VALIDATE_EMAIL)) {
            $model = new SolicitacaoLgpd();
            $this->render('lgpd/solicitacoes', [
                'erro' => 'Informe um e-mail valido para o titular.',
                'flash' => null,
                'itens' => $model->listarRecentes(150)
            ]);
            return;
        }

        $model = new SolicitacaoLgpd();
        $dados = [
            'titular_nome' => $titularNome,
            'titular_email' => $titularEmail,
            'tipo_solicitacao' => $tipo,
            'descricao' => $descricao,
            'usuario_abertura_id' => (int)$_SESSION['usuario']['id']
        ];
        $resultado = $model->criar($dados);

        LogService::registrar(
            (int)$_SESSION['usuario']['id'],
            'lgpd_solicitacao',
            'Solicitacao LGPD registrada',
            'solicitacoes_lgpd',
            $resultado['id'],
            null,
            [
                'protocolo' => $resultado['protocolo'],
                'tipo_solicitacao' => $tipo,
                'titular_email' => $titularEmail
            ]
        );

        flash_set('lgpd_solicitacoes', 'Solicitacao registrada com protocolo ' . $resultado['protocolo'] . '.', 'success');
        redirect(url('lgpd/solicitacoes'));
    }

    public function atualizarSolicitacao($id)
    {
        $this->exigirCsrf();

        $status = trim((string)($_POST['status'] ?? ''));
        $resposta = trim((string)($_POST['resposta'] ?? ''));
        $statusPermitidos = ['aberta', 'em_analise', 'concluida', 'indeferida'];
        if (!in_array($status, $statusPermitidos, true)) {
            flash_set('lgpd_solicitacoes', 'Status de solicitacao invalido.', 'danger');
            redirect(url('lgpd/solicitacoes'));
            return;
        }

        $model = new SolicitacaoLgpd();
        $solicitacao = $model->buscarPorId((int)$id);
        if (!$solicitacao) {
            flash_set('lgpd_solicitacoes', 'Solicitacao nao encontrada.', 'danger');
            redirect(url('lgpd/solicitacoes'));
            return;
        }

        $model->atualizarStatus((int)$id, $status, $resposta, (int)$_SESSION['usuario']['id']);
        LogService::registrar(
            (int)$_SESSION['usuario']['id'],
            'lgpd_atualizacao',
            'Solicitacao LGPD atualizada',
            'solicitacoes_lgpd',
            (int)$id,
            ['status' => $solicitacao['status']],
            ['status' => $status]
        );

        flash_set('lgpd_solicitacoes', 'Solicitacao atualizada com sucesso.', 'success');
        redirect(url('lgpd/solicitacoes'));
    }
}
