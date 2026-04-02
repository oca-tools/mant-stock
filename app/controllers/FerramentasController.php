<?php
// Controller de ferramentas
class FerramentasController extends ControllerBase
{
    public function index()
    {
        $limite = 20;
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $offset = ($pagina - 1) * $limite;

        $model = new Ferramenta();
        $totalRegistros = $model->contar();
        $totalPaginas = max(1, (int)ceil($totalRegistros / $limite));
        if ($pagina > $totalPaginas) {
            $pagina = $totalPaginas;
            $offset = ($pagina - 1) * $limite;
        }

        $ferramentas = $model->listar($limite, $offset);
        $this->render('ferramentas/index', [
            'ferramentas' => $ferramentas,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros
        ]);
    }

    public function criar()
    {
        $this->render('ferramentas/criar', ['erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $nome = trim($_POST['nome'] ?? '');
        $senhaConfirmacao = $_POST['senha_confirmacao'] ?? '';
        if ($nome === '') {
            $this->render('ferramentas/criar', ['erro' => 'Nome da ferramenta e obrigatorio.']);
            return;
        }

        $erroSenha = null;
        if (!$this->validarSenhaOperacional($senhaConfirmacao, $erroSenha)) {
            $this->render('ferramentas/criar', ['erro' => $erroSenha]);
            return;
        }

        $model = new Ferramenta();
        $dados = [
            'nome' => $nome,
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => 'Disponivel',
            'usuario_cadastro_id' => (int)$_SESSION['usuario']['id']
        ];
        $id = $model->criar($dados);
        LogService::registrar(
            $_SESSION['usuario']['id'],
            'criacao',
            'Ferramenta criada com validacao de senha',
            'ferramentas',
            $id,
            null,
            array_merge($dados, ['validacao_senha' => true])
        );
        redirect(url('ferramentas'));
    }
}
