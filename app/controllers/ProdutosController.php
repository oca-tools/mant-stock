<?php
// Controller de produtos
class ProdutosController extends ControllerBase
{
    public function index()
    {
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $busca = trim($_GET['busca'] ?? '');
        $limite = 10;
        $offset = ($pagina - 1) * $limite;

        $model = new Produto();
        $produtos = $model->listar($limite, $offset, $busca);
        $total = $model->contar($busca);
        $totalPaginas = (int)ceil($total / $limite);

        $this->render('produtos/index', [
            'produtos' => $produtos,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'busca' => $busca
        ]);
    }

    public function criar()
    {
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->listar();
        $config = require __DIR__ . '/../config/config.php';
        $this->render('produtos/criar', ['categorias' => $categorias, 'erro' => null, 'unidades' => $config['listas']['unidades_medida']]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $dados = $this->validarDados();
        if ($dados['erro']) {
            $categoriaModel = new Categoria();
            $categorias = $categoriaModel->listar();
            $this->render('produtos/criar', ['categorias' => $categorias, 'erro' => $dados['mensagem']]);
            return;
        }

        $imagem = $this->processarUpload();
        $dados['imagem'] = $imagem;

        $model = new Produto();
        $id = $model->criar($dados);

        LogService::registrar($_SESSION['usuario']['id'], 'criacao', 'Produto criado', 'produtos', $id, null, $dados);

        redirect(url('produtos'));
    }

    public function editar($id)
    {
        $model = new Produto();
        $produto = $model->buscarPorId($id);
        if (!$produto) {
            http_response_code(404);
            echo 'Produto nao encontrado.';
            return;
        }

        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->listar();
        $config = require __DIR__ . '/../config/config.php';
        $this->render('produtos/editar', [
            'produto' => $produto,
            'categorias' => $categorias,
            'erro' => null,
            'unidades' => $config['listas']['unidades_medida']
        ]);
    }

    public function atualizar($id)
    {
        $this->exigirCsrf();
        $model = new Produto();
        $produto = $model->buscarPorId($id);
        if (!$produto) {
            http_response_code(404);
            echo 'Produto nao encontrado.';
            return;
        }

        $dados = $this->validarDados();
        if ($dados['erro']) {
            $categoriaModel = new Categoria();
            $categorias = $categoriaModel->listar();
            $config = require __DIR__ . '/../config/config.php';
            $this->render('produtos/editar', ['produto' => $produto, 'categorias' => $categorias, 'erro' => $dados['mensagem'], 'unidades' => $config['listas']['unidades_medida']]);
            return;
        }

        $imagem = $produto['imagem'];
        $novaImagem = $this->processarUpload();
        if ($novaImagem !== null) {
            $imagem = $novaImagem;
        }
        $dados['imagem'] = $imagem;

        $antes = $produto;
        $model->atualizar($id, $dados);

        LogService::registrar($_SESSION['usuario']['id'], 'edicao', 'Produto atualizado', 'produtos', $id, $antes, $dados);

        redirect(url('produtos'));
    }

    public function excluir($id)
    {
        $this->exigirCsrf();
        $model = new Produto();
        $movModel = new Movimentacao();
        if ($movModel->contarPorProduto($id) > 0) {
            flash_set('produtos', 'Produto possui movimentacoes e nao pode ser excluido. Use inativacao futura.', 'warning');
            redirect(url('produtos'));
        }
        $produto = $model->buscarPorId($id);
        $model->excluir($id);
        LogService::registrar($_SESSION['usuario']['id'], 'exclusao', 'Produto excluido', 'produtos', $id, $produto, null);
        redirect(url('produtos'));
    }

    public function ver($id)
    {
        $db = Conexao::obter();
        $stmt = $db->prepare('SELECT p.*, c.nome AS categoria_nome FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.id = :id');
        $stmt->execute([':id' => $id]);
        $produto = $stmt->fetch();
        if (!$produto) {
            http_response_code(404);
            echo 'Produto nao encontrado.';
            return;
        }
        $movModel = new Movimentacao();
        $movimentacoes = $movModel->listarPorProduto($id, 20);
        $this->render('produtos/ver', [
            'produto' => $produto,
            'movimentacoes' => $movimentacoes
        ]);
    }

    private function validarDados()
    {
        $nome = trim($_POST['nome'] ?? '');
        if ($nome === '') {
            return ['erro' => true, 'mensagem' => 'Nome do produto e obrigatorio.'];
        }

        $config = require __DIR__ . '/../config/config.php';
        $unidades = $config['listas']['unidades_medida'];
        $unidade = trim($_POST['unidade_medida'] ?? '');
        if ($unidade !== '' && !in_array($unidade, $unidades, true)) {
            return ['erro' => true, 'mensagem' => 'Unidade de medida invalida.'];
        }

        return [
            'erro' => false,
            'mensagem' => '',
            'nome' => $nome,
            'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
            'codigo_interno' => trim($_POST['codigo_interno'] ?? ''),
            'unidade_medida' => $unidade,
            'estoque_atual' => (float)($_POST['estoque_atual'] ?? 0),
            'estoque_minimo' => (float)($_POST['estoque_minimo'] ?? 0),
            'localizacao' => trim($_POST['localizacao'] ?? ''),
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ];
    }

    private function processarUpload()
    {
        if (empty($_FILES['imagem']['name'])) {
            return null;
        }

        $arquivo = $_FILES['imagem'];
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $config = require __DIR__ . '/../config/config.php';
        $maxBytes = (int)$config['app']['upload_max_mb'] * 1024 * 1024;
        if ($arquivo['size'] > $maxBytes) {
            return null;
        }

        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $permitidos, true)) {
            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($arquivo['tmp_name']);
        $mimesPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $mimesPermitidos, true)) {
            return null;
        }

        $nomeArquivo = uniqid('prod_', true) . '.' . $ext;
        $destino = __DIR__ . '/../../public/uploads/' . $nomeArquivo;
        if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
            return $nomeArquivo;
        }

        return null;
    }
}
