<?php
// Definicao de rotas
return [
    // Perfis
    // Administrador, Almoxarifado, Consulta

    ['metodo' => 'GET', 'caminho' => '/', 'acao' => ['AuthController', 'formLogin'], 'opcoes' => []],
    ['metodo' => 'GET', 'caminho' => '/login', 'acao' => ['AuthController', 'formLogin'], 'opcoes' => []],
    ['metodo' => 'POST', 'caminho' => '/login', 'acao' => ['AuthController', 'login'], 'opcoes' => []],
    ['metodo' => 'POST', 'caminho' => '/logout', 'acao' => ['AuthController', 'logout'], 'opcoes' => ['auth' => true]],

    ['metodo' => 'GET', 'caminho' => '/dashboard', 'acao' => ['DashboardController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/estoque', 'acao' => ['EstoqueController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/busca', 'acao' => ['BuscaController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],

    ['metodo' => 'GET', 'caminho' => '/produtos', 'acao' => ['ProdutosController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/produtos/ver/{id}', 'acao' => ['ProdutosController', 'ver'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/produtos/criar', 'acao' => ['ProdutosController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/produtos', 'acao' => ['ProdutosController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'GET', 'caminho' => '/produtos/editar/{id}', 'acao' => ['ProdutosController', 'editar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/produtos/editar/{id}', 'acao' => ['ProdutosController', 'atualizar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/produtos/excluir/{id}', 'acao' => ['ProdutosController', 'excluir'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],

    ['metodo' => 'GET', 'caminho' => '/categorias', 'acao' => ['CategoriasController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/categorias/criar', 'acao' => ['CategoriasController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'POST', 'caminho' => '/categorias', 'acao' => ['CategoriasController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'GET', 'caminho' => '/categorias/editar/{id}', 'acao' => ['CategoriasController', 'editar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'POST', 'caminho' => '/categorias/editar/{id}', 'acao' => ['CategoriasController', 'atualizar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'POST', 'caminho' => '/categorias/excluir/{id}', 'acao' => ['CategoriasController', 'excluir'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],

    ['metodo' => 'GET', 'caminho' => '/movimentacoes', 'acao' => ['MovimentacoesController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],

    ['metodo' => 'GET', 'caminho' => '/entradas', 'acao' => ['EntradasController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/entradas/criar', 'acao' => ['EntradasController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/entradas', 'acao' => ['EntradasController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],

    ['metodo' => 'GET', 'caminho' => '/saidas', 'acao' => ['SaidasController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/saidas/criar', 'acao' => ['SaidasController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/saidas', 'acao' => ['SaidasController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],

    ['metodo' => 'GET', 'caminho' => '/descartes', 'acao' => ['DescartesController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/descartes/criar', 'acao' => ['DescartesController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/descartes', 'acao' => ['DescartesController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],

    ['metodo' => 'GET', 'caminho' => '/ferramentas', 'acao' => ['FerramentasController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/ferramentas/criar', 'acao' => ['FerramentasController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/ferramentas', 'acao' => ['FerramentasController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],

    ['metodo' => 'GET', 'caminho' => '/emprestimos', 'acao' => ['EmprestimosController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/emprestimos/criar', 'acao' => ['EmprestimosController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/emprestimos', 'acao' => ['EmprestimosController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/emprestimos/devolver/{id}', 'acao' => ['EmprestimosController', 'devolver'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],

    ['metodo' => 'GET', 'caminho' => '/inventarios', 'acao' => ['InventariosController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/inventarios/criar', 'acao' => ['InventariosController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],
    ['metodo' => 'POST', 'caminho' => '/inventarios', 'acao' => ['InventariosController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado']]],

    ['metodo' => 'GET', 'caminho' => '/relatorios', 'acao' => ['RelatoriosController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/relatorios/excel', 'acao' => ['RelatoriosController', 'exportarExcel'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],
    ['metodo' => 'GET', 'caminho' => '/relatorios/pdf', 'acao' => ['RelatoriosController', 'exportarPdf'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador', 'Almoxarifado', 'Consulta']]],

    ['metodo' => 'GET', 'caminho' => '/usuarios', 'acao' => ['UsuariosController', 'index'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'GET', 'caminho' => '/usuarios/criar', 'acao' => ['UsuariosController', 'criar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'POST', 'caminho' => '/usuarios', 'acao' => ['UsuariosController', 'armazenar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'GET', 'caminho' => '/usuarios/editar/{id}', 'acao' => ['UsuariosController', 'editar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'POST', 'caminho' => '/usuarios/editar/{id}', 'acao' => ['UsuariosController', 'atualizar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]],
    ['metodo' => 'POST', 'caminho' => '/usuarios/desativar/{id}', 'acao' => ['UsuariosController', 'desativar'], 'opcoes' => ['auth' => true, 'tipos' => ['Administrador']]]
];
