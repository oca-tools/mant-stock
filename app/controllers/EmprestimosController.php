<?php
// Controller de emprestimos de ferramentas
class EmprestimosController extends ControllerBase
{
    public function index()
    {
        $limite = 20;
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $offset = ($pagina - 1) * $limite;

        $model = new EmprestimoFerramenta();
        $totalRegistros = $model->contar();
        $totalPaginas = max(1, (int)ceil($totalRegistros / $limite));
        if ($pagina > $totalPaginas) {
            $pagina = $totalPaginas;
            $offset = ($pagina - 1) * $limite;
        }

        $emprestimos = $model->listar($limite, $offset);
        $this->render('emprestimos/index', [
            'emprestimos' => $emprestimos,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros
        ]);
    }

    public function criar()
    {
        $ferramentaModel = new Ferramenta();
        $ferramentas = $ferramentaModel->listar(null);
        $this->render('emprestimos/criar', ['ferramentas' => $ferramentas, 'erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $ferramentaId = (int)($_POST['ferramenta_id'] ?? 0);
        $usuarioResp = trim($_POST['usuario_responsavel'] ?? '');
        $senhaConfirmacao = $_POST['senha_confirmacao'] ?? '';

        if ($ferramentaId <= 0 || $usuarioResp === '') {
            $ferramentaModel = new Ferramenta();
            $ferramentas = $ferramentaModel->listar(null);
            $this->render('emprestimos/criar', ['ferramentas' => $ferramentas, 'erro' => 'Informe ferramenta e responsavel.']);
            return;
        }

        $erroSenha = null;
        if (!$this->validarSenhaOperacional($senhaConfirmacao, $erroSenha)) {
            $ferramentaModel = new Ferramenta();
            $ferramentas = $ferramentaModel->listar(null);
            $this->render('emprestimos/criar', ['ferramentas' => $ferramentas, 'erro' => $erroSenha]);
            return;
        }

        $ferramentaModel = new Ferramenta();
        $model = new EmprestimoFerramenta();
        $dados = [
            'ferramenta_id' => $ferramentaId,
            'usuario_responsavel' => $usuarioResp,
            'usuario_executor_id' => (int)$_SESSION['usuario']['id'],
            'data_devolucao' => null,
            'status' => 'Emprestada'
        ];

        $db = Conexao::obter();
        try {
            $db->beginTransaction();
            $ferramenta = $ferramentaModel->buscarPorIdParaAtualizacao($ferramentaId);
            if (!$ferramenta || $ferramenta['status'] !== 'Disponivel') {
                $db->rollBack();
                $ferramentas = $ferramentaModel->listar(null);
                $this->render('emprestimos/criar', ['ferramentas' => $ferramentas, 'erro' => 'Ferramenta indisponivel.']);
                return;
            }

            $id = $model->criar($dados);
            $ferramentaModel->atualizarStatus($ferramentaId, 'Emprestada');
            $db->commit();
        } catch (Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $ferramentas = $ferramentaModel->listar(null);
            $this->render('emprestimos/criar', ['ferramentas' => $ferramentas, 'erro' => 'Falha ao registrar emprestimo. Tente novamente.']);
            return;
        }

        LogService::registrar(
            $_SESSION['usuario']['id'],
            'movimentacao',
            'Emprestimo de ferramenta com validacao de senha',
            'emprestimos_ferramentas',
            $id,
            null,
            array_merge($dados, ['validacao_senha' => true])
        );
        redirect(url('emprestimos'));
    }

    public function devolver($id)
    {
        $this->exigirCsrf();
        $senhaConfirmacao = $_POST['senha_confirmacao'] ?? '';
        $erroSenha = null;
        if (!$this->validarSenhaOperacional($senhaConfirmacao, $erroSenha)) {
            flash_set('emprestimos', $erroSenha, 'danger');
            redirect(url('emprestimos'));
        }

        $model = new EmprestimoFerramenta();
        $emprestimo = $model->buscarPorId((int)$id);
        if (!$emprestimo) {
            flash_set('emprestimos', 'Emprestimo nao encontrado.', 'danger');
            redirect(url('emprestimos'));
        }

        $ferramentaModel = new Ferramenta();
        $db = Conexao::obter();
        try {
            $db->beginTransaction();
            $ferramenta = $ferramentaModel->buscarPorIdParaAtualizacao((int)$emprestimo['ferramenta_id']);
            if (!$ferramenta) {
                throw new RuntimeException('Ferramenta nao encontrada para devolucao.');
            }

            $model->registrarDevolucao((int)$id, (int)$_SESSION['usuario']['id']);
            $ferramentaModel->atualizarStatus((int)$emprestimo['ferramenta_id'], 'Disponivel');
            $db->commit();
        } catch (Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            flash_set('emprestimos', 'Falha ao registrar devolucao. Tente novamente.', 'danger');
            redirect(url('emprestimos'));
        }

        LogService::registrar(
            $_SESSION['usuario']['id'],
            'movimentacao',
            'Devolucao de ferramenta com validacao de senha',
            'emprestimos_ferramentas',
            (int)$id,
            null,
            ['validacao_senha' => true]
        );
        flash_set('emprestimos', 'Devolucao registrada com sucesso.', 'success');
        redirect(url('emprestimos'));
    }
}
