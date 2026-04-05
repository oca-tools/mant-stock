<?php
// Validacoes rapidas de seguranca/codificacao para CI.

$raiz = realpath(__DIR__ . '/..');
if ($raiz === false) {
    fwrite(STDERR, "Nao foi possivel localizar a raiz do projeto.\n");
    exit(1);
}

$diretorios = [
    $raiz . DIRECTORY_SEPARATOR . 'app',
    $raiz . DIRECTORY_SEPARATOR . 'public',
    $raiz . DIRECTORY_SEPARATOR . 'routes',
    $raiz . DIRECTORY_SEPARATOR . 'database',
    $raiz . DIRECTORY_SEPARATOR . 'scripts'
];

$extensoesTexto = ['php', 'sql', 'js', 'css', 'md', 'html'];
$arquivosTexto = [];
foreach ($diretorios as $diretorio) {
    if (!is_dir($diretorio)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($diretorio, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $arquivo) {
        if (!$arquivo->isFile()) {
            continue;
        }
        $ext = strtolower(pathinfo($arquivo->getFilename(), PATHINFO_EXTENSION));
        if (in_array($ext, $extensoesTexto, true)) {
            $arquivosTexto[] = $arquivo->getPathname();
        }
    }
}

$erros = [];

foreach ($arquivosTexto as $arquivo) {
    $conteudo = @file_get_contents($arquivo);
    if ($conteudo === false) {
        $erros[] = 'Falha ao ler arquivo: ' . $arquivo;
        continue;
    }

    if (strncmp($conteudo, "\xEF\xBB\xBF", 3) === 0) {
        $erros[] = 'BOM UTF-8 detectado em: ' . $arquivo;
    }

    if (basename($arquivo) !== 'validar_guardrails.php' && stripos($conteudo, 'admin123') !== false) {
        $erros[] = 'Senha padrao insegura detectada em: ' . $arquivo;
    }

    $linhas = preg_split('/\R/', $conteudo);
    foreach ($linhas as $linha) {
        $linhaNormalizada = trim((string)$linha);
        if ($linhaNormalizada === '' || strpos($linhaNormalizada, '--') === 0 || strpos($linhaNormalizada, '#') === 0) {
            continue;
        }
        if (preg_match('/ADD\s+COLUMN\s+IF\s+NOT\s+EXISTS/i', $linhaNormalizada) === 1) {
            $erros[] = 'Sintaxe nao compativel com parte dos ambientes MySQL detectada em: ' . $arquivo;
            break;
        }
    }
}

if (!empty($erros)) {
    fwrite(STDERR, "Falhas de guardrail encontradas:\n");
    foreach ($erros as $erro) {
        fwrite(STDERR, ' - ' . $erro . "\n");
    }
    exit(1);
}

echo "Guardrails validados com sucesso.\n";
