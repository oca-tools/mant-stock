<?php
// Controller de emprestimos de ferramentas
class EmprestimosController extends ControllerBase
{
    public function index()
    {
        $model = new EmprestimoFerramenta();
        $emprestimos = $model->listar();
        $this->render('emprestimos/index', ['emprestimos' => $emprestimos]);
    }

    public function criar()
    {
        $ferramentaModel = new Ferramenta();
        $ferramentas = $ferramentaModel->listar();
        $this->render('emprestimos/criar', ['ferramentas' => $ferramentas, 'erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $ferramentaId = (int)($_POST['ferramenta_id'] ?? 0);
        $usuarioResp = trim($_POST['usuario_responsavel'] ?? '');
        if ($ferramentaId <= 0 || $usuarioResp === '') {
            $ferramentaModel = new Ferramenta();
            $ferramentas = $ferramentaModel->listar();
            $this->render('emprestimos/criar', ['ferramentas' => $ferramentas, 'erro' => 'Informe ferramenta e responsavel.']);
            return;
        }

        $ferramentaModel = new Ferramenta();
        $ferramenta = $ferramentaModel->buscarPorId($ferramentaId);
        if (!$ferramenta || $ferramenta['status'] !== 'Disponivel') {
            $ferramentas = $ferramentaModel->listar();
            $this->render('emprestimos/criar', ['ferramentas' => $ferramentas, 'erro' => 'Ferramenta indisponivel.']);
            return;
        }

        $model = new EmprestimoFerramenta();
        $dados = [
            'ferramenta_id' => $ferramentaId,
            'usuario_responsavel' => $usuarioResp,
            'data_devolucao' => null,
            'status' => 'Emprestada'
        ];
        $id = $model->criar($dados);

        $ferramentaModel->atualizarStatus($ferramentaId, 'Emprestada');

        LogService::registrar($_SESSION['usuario']['id'], 'movimentacao', 'Emprestimo de ferramenta', 'emprestimos_ferramentas', $id, null, $dados);
        redirect(url('emprestimos'));
    }

    public function devolver($id)
    {
        $this->exigirCsrf();
        $model = new EmprestimoFerramenta();
        $model->registrarDevolucao($id);

        $db = Conexao::obter();
        $stmt = $db->prepare('SELECT ferramenta_id FROM emprestimos_ferramentas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();
        if ($linha) {
            $ferramentaModel = new Ferramenta();
            $ferramentaModel->atualizarStatus($linha['ferramenta_id'], 'Disponivel');
        }

        LogService::registrar($_SESSION['usuario']['id'], 'movimentacao', 'Devolucao de ferramenta', 'emprestimos_ferramentas', $id, null, null);
        redirect(url('emprestimos'));
    }
}
