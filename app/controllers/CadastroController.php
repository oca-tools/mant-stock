<?php
// Controller para cadastro por convite
class CadastroController extends ControllerBase
{
    public function formAceite()
    {
        $token = trim((string)($_GET['token'] ?? ''));
        $convite = $this->buscarConviteValidoOuNulo($token);

        $this->render('auth/aceitar_convite', [
            'token' => $token,
            'convite' => $convite,
            'erro' => null,
            'sucesso' => null,
            'dadosForm' => [
                'nome' => $convite['nome_sugerido'] ?? '',
                'senha' => '',
                'confirmar_senha' => ''
            ]
        ]);
    }

    public function concluirCadastro()
    {
        $this->exigirCsrf();

        $token = trim((string)($_POST['token'] ?? ''));
        $convite = $this->buscarConviteValidoOuNulo($token);
        if (!$convite) {
            $this->render('auth/aceitar_convite', [
                'token' => $token,
                'convite' => null,
                'erro' => 'Convite invalido, expirado ou ja utilizado.',
                'sucesso' => null,
                'dadosForm' => ['nome' => '', 'senha' => '', 'confirmar_senha' => '']
            ]);
            return;
        }

        $nome = trim((string)($_POST['nome'] ?? ''));
        $senha = (string)($_POST['senha'] ?? '');
        $confirmarSenha = (string)($_POST['confirmar_senha'] ?? '');
        $dadosForm = [
            'nome' => $nome,
            'senha' => '',
            'confirmar_senha' => ''
        ];

        if ($nome === '' || $senha === '' || $confirmarSenha === '') {
            $this->render('auth/aceitar_convite', [
                'token' => $token,
                'convite' => $convite,
                'erro' => 'Preencha todos os campos obrigatorios.',
                'sucesso' => null,
                'dadosForm' => $dadosForm
            ]);
            return;
        }

        if (strlen($senha) < 6) {
            $this->render('auth/aceitar_convite', [
                'token' => $token,
                'convite' => $convite,
                'erro' => 'A senha deve conter ao menos 6 caracteres.',
                'sucesso' => null,
                'dadosForm' => $dadosForm
            ]);
            return;
        }

        if ($senha !== $confirmarSenha) {
            $this->render('auth/aceitar_convite', [
                'token' => $token,
                'convite' => $convite,
                'erro' => 'A confirmacao de senha nao confere.',
                'sucesso' => null,
                'dadosForm' => $dadosForm
            ]);
            return;
        }

        $usuarioModel = new Usuario();
        if ($usuarioModel->senhaJaUtilizadaNoEmail($convite['email'], $senha)) {
            $this->render('auth/aceitar_convite', [
                'token' => $token,
                'convite' => $convite,
                'erro' => 'Ja existe conta com este e-mail usando essa mesma senha. Escolha outra senha.',
                'sucesso' => null,
                'dadosForm' => $dadosForm
            ]);
            return;
        }

        $conviteModel = new ConviteUsuario();
        $dadosUsuario = [
            'nome' => $nome,
            'email' => $convite['email'],
            'senha_hash' => password_hash($senha, PASSWORD_BCRYPT),
            'tipo_usuario' => $convite['tipo_usuario'],
            'ativo' => 1
        ];

        $db = Conexao::obter();
        try {
            $db->beginTransaction();
            $usuarioId = (int)$usuarioModel->criar($dadosUsuario);
            $conviteModel->marcarComoAceito((int)$convite['id'], $usuarioId);
            $db->commit();

            LogService::registrar(
                $usuarioId,
                'criacao',
                'Conta criada via convite por e-mail',
                'usuarios',
                $usuarioId,
                null,
                ['email' => $convite['email'], 'tipo_usuario' => $convite['tipo_usuario']]
            );

            $this->render('auth/aceitar_convite', [
                'token' => '',
                'convite' => null,
                'erro' => null,
                'sucesso' => 'Conta criada com sucesso. Agora voce ja pode acessar a tela de login.',
                'dadosForm' => ['nome' => '', 'senha' => '', 'confirmar_senha' => '']
            ]);
        } catch (Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->render('auth/aceitar_convite', [
                'token' => $token,
                'convite' => $convite,
                'erro' => 'Nao foi possivel concluir o cadastro no momento. Tente novamente.',
                'sucesso' => null,
                'dadosForm' => $dadosForm
            ]);
        }
    }

    private function buscarConviteValidoOuNulo($token)
    {
        if ($token === '') {
            return null;
        }
        $conviteModel = new ConviteUsuario();
        return $conviteModel->buscarValidoPorToken($token);
    }
}
