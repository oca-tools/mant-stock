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

## Deploy no subdominio stock.oca-tools.com.br
- Guia completo: `DEPLOY_STOCK_SUBDOMINIO.md`.
- O sistema agora aceita variaveis de ambiente no `app/config/config.php`.
- Em producao, use:
  - `APP_URL_PUBLICA=https://stock.oca-tools.com.br`
  - `APP_URL_BASE=/`
  - `APP_FORCAR_HTTPS=true`

## Inventario mensal
- O inventario opera por competencia (`AAAA-MM`), com ciclo `aberto` e `fechado`.
- A contagem manual nao ajusta estoque no momento do registro.
- O ajuste de estoque acontece apenas no fechamento do ciclo mensal.
- Para base antiga, execute a migracao: `database/migrations/20260401_inventario_mensal.sql`.

## Cadastro por convite de e-mail
- O administrador pode enviar convite em `Usuarios > Enviar Convite`.
- O colaborador recebe link para `cadastro/aceitar` e cria a propria conta.
- O sistema permite mais de uma conta por e-mail, desde que a senha seja diferente.
- Para base antiga, execute a migracao: `database/migrations/20260401_convites_usuarios.sql`.
- Se o envio automatico falhar, o sistema grava o conteudo em `logs/emails.log`.

## Configuracao de e-mail
- Ajuste `mail.remetente_email` e `mail.remetente_nome` em `app/config/config.php`.
- `modo_teste = true` grava os e-mails em log sem tentar envio real.
- `modo_teste = false` usa a funcao nativa `mail()` do PHP (depende da configuracao de SMTP no servidor).

## Adequacao LGPD
- O sistema possui aceite obrigatorio de politica de privacidade por usuario autenticado.
- Cada aceite registra data/hora, IP e versao da politica aplicada.
- Ha modulo administrativo para registrar e acompanhar solicitacoes de titulares em `/lgpd/solicitacoes`.
- Para base antiga, execute: `database/migrations/20260403_lgpd_base.sql`.
- Parametros de configuracao em `app/config/config.php`:
  - `lgpd.versao_politica`
  - `lgpd.exigir_aceite`
  - `lgpd.email_encarregado`
  - `lgpd.retencao_logs_dias`
  - `lgpd.anonimizacao_logs_dias`
- Script de rotina para minimizacao/retencao:
  - `php scripts/rotina_lgpd_retencao.php`

## Auditoria operacional e comprovante de saida
- Entradas, saidas, cadastro de ferramentas e emprestimos/devolucoes exigem senha de confirmacao do usuario logado.
- Toda saida gera comprovante imprimivel em `/saidas/comprovante/{id}` com campo de assinatura do solicitante.
- As listagens de entradas, saidas, movimentacoes, ferramentas e emprestimos usam paginacao de 20 registros por pagina.
- Para base antiga, execute a migracao: `database/migrations/20260401_auditoria_operacoes.sql`.

## Observacoes
- O exportador de Excel gera um CSV.
- O exportador de PDF gera HTML pronto para impressao.
