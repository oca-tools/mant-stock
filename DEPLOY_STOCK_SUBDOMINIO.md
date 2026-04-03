# Deploy em VPS Compartilhada (stock.oca-tools.com.br)

Este guia prepara o sistema para rodar em paralelo com outros sites no mesmo servidor, usando o subdominio:

- `stock.oca-tools.com.br`

## 1) Estrutura recomendada no servidor

Opção recomendada (mais segura):

1. Criar pasta da aplicacao fora da pasta publica principal.
2. Definir o `DocumentRoot` do subdominio apontando para a pasta `public` do projeto.

Exemplo:

- Projeto completo: `/home/USUARIO/apps/mant-stock`
- Raiz publica do subdominio: `/home/USUARIO/apps/mant-stock/public`

Assim, pastas sensiveis (`app`, `database`, `logs`, `routes`) nao ficam expostas na web.

## 2) Upload dos arquivos

Enviar para o servidor:

- `app/`
- `database/`
- `logs/`
- `public/`
- `routes/`
- `README.md`
- `DEPLOY_STOCK_SUBDOMINIO.md`

Opcional (gerar pacote zip pronto para upload):

```powershell
powershell -ExecutionPolicy Bypass -File scripts/empacotar_deploy.ps1
```

O zip sera criado em `build/`.

## 3) Banco de dados

1. Criar banco MySQL para o subdominio.
2. Importar `database/schema.sql`.
3. Se necessario, aplicar migracoes em `database/migrations/`.
   - Para adequacao LGPD em base antiga, execute: `database/migrations/20260403_lgpd_base.sql`.
4. Criar usuario administrador:
   - executar `php database/seeds/seed_usuarios.php` (via SSH), ou criar manualmente no banco.

## 4) Configuracao de ambiente (recomendado)

O arquivo `app/config/config.php` ja aceita variaveis de ambiente.

Defina no painel/servidor (ou no VirtualHost):

- `APP_NOME=OCA MantStock`
- `APP_URL_BASE=/`
- `APP_URL_PUBLICA=https://stock.oca-tools.com.br`
- `APP_FORCAR_HTTPS=true`
- `DB_HOST=localhost`
- `DB_BANCO=SEU_BANCO`
- `DB_USUARIO=SEU_USUARIO`
- `DB_SENHA=SUA_SENHA`
- `DB_CHARSET=utf8mb4`
- `SESSAO_NOME=sessao_estoque_stock`
- `SESSAO_EXPIRACAO=7200`
- `MAIL_REMETENTE_EMAIL=nao-responda@stock.oca-tools.com.br`
- `MAIL_REMETENTE_NOME=OCA MantStock`
- `MAIL_MODO_TESTE=false`
- `LGPD_VERSAO_POLITICA=2026-04`
- `LGPD_EXIGIR_ACEITE=true`
- `LGPD_EMAIL_ENCARREGADO=privacidade@oca-tools.com.br`
- `LGPD_RETENCAO_LOGS_DIAS=365`
- `LGPD_ANONIMIZACAO_LOGS_DIAS=90`

Se nao puder usar variaveis de ambiente, edite `app/config/config.php` diretamente com os dados de producao.

## 5) Permissoes

Garantir escrita para:

- `logs/`
- `public/uploads/`

Permissao tipica:

- pastas: `755`
- arquivos: `644`

## 6) SSL e HTTPS

1. Emitir certificado SSL para `stock.oca-tools.com.br`.
2. Validar abertura em `https://stock.oca-tools.com.br`.
3. Com `APP_FORCAR_HTTPS=true`, o sistema redireciona HTTP para HTTPS.

## 7) Checklist final

1. Login abre normalmente.
2. Dashboard carrega sem erro.
3. Upload de imagem funciona.
4. Exportacoes (CSV/PDF) funcionam.
5. Impressao de comprovante de saida funciona em 1 pagina.
6. Favicon aparece no navegador.
7. Fluxo de aceite LGPD e solicitacoes de titulares funcionando.

Rotina recomendada (cron diario):

```bash
php /CAMINHO_DO_APP/scripts/rotina_lgpd_retencao.php
```

## 8) Deploy paralelo com outros subdominios

Como ja existe `fb.oca-tools.com.br`, basta manter cada subdominio com seu proprio `DocumentRoot`.

Exemplo:

- `fb.oca-tools.com.br` -> `/home/USUARIO/apps/fb/public`
- `stock.oca-tools.com.br` -> `/home/USUARIO/apps/mant-stock/public`

Nao ha conflito entre os dois se cada um tiver pasta e banco separados.
