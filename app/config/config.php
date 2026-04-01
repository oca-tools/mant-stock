<?php
// Configuracoes gerais do sistema
return [
    'app' => [
        'nome' => 'Controle de Estoque - Manutencao',
        'url_base' => '/',
        'forcar_https' => false,
        'upload_max_mb' => 2
    ],
    'db' => [
        'host' => 'localhost',
        'banco' => 'estoque_manutencao',
        'usuario' => 'root',
        'senha' => '',
        'charset' => 'utf8mb4'
    ],
    'sessao' => [
        'nome' => 'sessao_estoque',
        'expiracao' => 7200
    ],
    'listas' => [
        'unidades_medida' => ['un', 'cx', 'kg', 'g', 'l', 'ml', 'm', 'm2', 'm3', 'kit', 'pca']
    ]
];
