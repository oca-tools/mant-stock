<?php
// Controller de usuarios
class UsuariosController extends ControllerBase
{
    public function index()
    {
        $usuarioModel = new Usuario();
        $conviteModel = new ConviteUsuario();
        $usuarios = $usuarioModel->listar();
        $convites = $conviteModel->listarRecentes(15);
        $this->render('usuarios/index', [
            'usuarios' => $usuarios,
            'convites' => $convites
        ]);
    }

    public function criar()
    {
        $this->render('usuarios/criar', ['erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $nome = trim((string)($_POST['nome'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $senha = (string)($_POST['senha'] ?? '');
        $tipo = (string)($_POST['tipo_usuario'] ?? '');
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        if ($nome === '' || $email === '' || $senha === '' || $tipo === '') {
            $this->render('usuarios/criar', ['erro' => 'Preencha todos os campos obrigatorios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('usuarios/criar', ['erro' => 'Informe um e-mail valido.']);
            return;
        }

        $usuarioModel = new Usuario();
        if ($usuarioModel->senhaJaUtilizadaNoEmail($email, $senha)) {
            $this->render('usuarios/criar', ['erro' => 'Ja existe conta com este e-mail usando essa mesma senha. Defina outra senha.']);
            return;
        }

        $dados = [
            'nome' => $nome,
            'email' => $email,
            'senha_hash' => password_hash($senha, PASSWORD_BCRYPT),
            'tipo_usuario' => $tipo,
            'ativo' => $ativo
        ];
        $id = $usuarioModel->criar($dados);
        LogService::registrar($_SESSION['usuario']['id'], 'criacao', 'Usuario criado', 'usuarios', $id, null, $dados);
        flash_set('usuarios', 'Usuario criado com sucesso.', 'success');
        redirect(url('usuarios'));
    }

    public function editar($id)
    {
        $model = new Usuario();
        $usuario = $model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }
        $this->render('usuarios/editar', ['usuario' => $usuario, 'erro' => null]);
    }

    public function atualizar($id)
    {
        $this->exigirCsrf();
        $model = new Usuario();
        $usuario = $model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }

        $nome = trim((string)($_POST['nome'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $tipo = (string)($_POST['tipo_usuario'] ?? '');
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        if ($nome === '' || $email === '' || $tipo === '') {
            $this->render('usuarios/editar', ['usuario' => $usuario, 'erro' => 'Preencha todos os campos obrigatorios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('usuarios/editar', ['usuario' => $usuario, 'erro' => 'Informe um e-mail valido.']);
            return;
        }

        $senhaNova = (string)($_POST['senha'] ?? '');
        $temOutroComMesmoEmail = $model->contarPorEmail($email, $id) > 0;
        if ($temOutroComMesmoEmail && $senhaNova === '') {
            $this->render(
                'usuarios/editar',
                [
                    'usuario' => $usuario,
                    'erro' => 'Ao usar um e-mail ja cadastrado em outra conta, informe uma nova senha para manter as contas distintas.'
                ]
            );
            return;
        }

        if ($senhaNova !== '' && $model->senhaJaUtilizadaNoEmail($email, $senhaNova, $id)) {
            $this->render('usuarios/editar', ['usuario' => $usuario, 'erro' => 'Ja existe conta com este e-mail usando essa senha. Defina outra senha.']);
            return;
        }

        $dados = [
            'nome' => $nome,
            'email' => $email,
            'tipo_usuario' => $tipo,
            'ativo' => $ativo
        ];
        if ($senhaNova !== '') {
            $dados['senha_hash'] = password_hash($senhaNova, PASSWORD_BCRYPT);
        }

        $model->atualizar($id, $dados);
        LogService::registrar($_SESSION['usuario']['id'], 'edicao', 'Usuario atualizado', 'usuarios', $id, $usuario, $dados);
        flash_set('usuarios', 'Usuario atualizado com sucesso.', 'success');
        redirect(url('usuarios'));
    }

    public function desativar($id)
    {
        $this->exigirCsrf();
        $model = new Usuario();
        $usuario = $model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }

        $model->atualizar($id, ['ativo' => 0]);
        LogService::registrar($_SESSION['usuario']['id'], 'edicao', 'Usuario desativado', 'usuarios', $id, $usuario, ['ativo' => 0]);
        flash_set('usuarios', 'Usuario desativado com sucesso.', 'success');
        redirect(url('usuarios'));
    }

    public function formConvite()
    {
        $this->render('usuarios/convite', ['erro' => null]);
    }

    public function enviarConvite()
    {
        $this->exigirCsrf();

        $email = trim((string)($_POST['email'] ?? ''));
        $nomeSugerido = trim((string)($_POST['nome_sugerido'] ?? ''));
        $tipoUsuario = (string)($_POST['tipo_usuario'] ?? '');
        $validadeHoras = (int)($_POST['validade_horas'] ?? 72);
        $validadeHoras = max(1, min(720, $validadeHoras));

        if ($email === '' || $tipoUsuario === '') {
            $this->render('usuarios/convite', ['erro' => 'Preencha os campos obrigatorios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('usuarios/convite', ['erro' => 'Informe um e-mail valido.']);
            return;
        }

        if (!in_array($tipoUsuario, ['Administrador', 'Almoxarifado', 'Consulta'], true)) {
            $this->render('usuarios/convite', ['erro' => 'Perfil de acesso invalido.']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiraEm = date('Y-m-d H:i:s', time() + ($validadeHoras * 3600));

        $conviteModel = new ConviteUsuario();
        $conviteId = $conviteModel->criar([
            'email' => $email,
            'nome_sugerido' => $nomeSugerido,
            'tipo_usuario' => $tipoUsuario,
            'token_hash' => $tokenHash,
            'usuario_convite_id' => (int)$_SESSION['usuario']['id'],
            'expira_em' => $expiraEm
        ]);

        $linkAceite = url_absoluta('cadastro/aceitar?token=' . urlencode($token));
        $resultadoEnvio = EmailService::enviarConviteCadastro($email, $nomeSugerido, $tipoUsuario, $linkAceite);

        LogService::registrar(
            $_SESSION['usuario']['id'],
            'convite',
            'Convite de cadastro enviado',
            'convites_usuarios',
            $conviteId,
            null,
            [
                'email' => $email,
                'tipo_usuario' => $tipoUsuario,
                'expira_em' => $expiraEm
            ]
        );

        if (!empty($resultadoEnvio['sucesso'])) {
            flash_set('usuarios', 'Convite gerado e envio concluido para: ' . $email . '.', 'success');
        } else {
            flash_set(
                'usuarios',
                'Convite gerado, mas o envio automatico falhou. Ajuste o e-mail de saida e gere um novo convite para este usuario.',
                'warning'
            );
        }

        redirect(url('usuarios'));
    }
}
