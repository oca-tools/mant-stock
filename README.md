# Sistema de Controle de Estoque - Manutencao

## Visao geral
Sistema web em PHP 8+ para controle de estoque do setor de manutencao de um resort.

## Requisitos
- PHP 8+
- MySQL 8+
- Servidor Apache/Nginx com DocumentRoot apontando para a pasta `public`

## Instalacao
1. Crie o banco e tabelas com `database/schema.sql`.
2. Ajuste as credenciais em `app/config/config.php`.
3. Gere o usuario administrador com o seed: `php database/seeds/seed_usuarios.php`.
4. Acesse `/login` e utilize o usuario criado.

## Observacoes
- O exportador de Excel gera um CSV.
- O exportador de PDF gera HTML pronto para impressao.
