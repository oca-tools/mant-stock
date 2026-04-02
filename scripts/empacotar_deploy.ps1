param(
    [string]$PastaSaida = "build",
    [string]$NomeArquivo = ""
)

$ErrorActionPreference = "Stop"

$raizProjeto = Resolve-Path (Join-Path $PSScriptRoot "..")
$dataHora = Get-Date -Format "yyyyMMdd-HHmm"

if ([string]::IsNullOrWhiteSpace($NomeArquivo)) {
    $NomeArquivo = "mant-stock-stock-$dataHora.zip"
}

$destinoFinal = Join-Path $raizProjeto $PastaSaida
if (!(Test-Path $destinoFinal)) {
    New-Item -ItemType Directory -Path $destinoFinal | Out-Null
}

$arquivoZip = Join-Path $destinoFinal $NomeArquivo
if (Test-Path $arquivoZip) {
    Remove-Item -LiteralPath $arquivoZip -Force
}

# Usa git archive para gerar pacote limpo, sem pasta .git e sem arquivos temporarios locais.
Push-Location $raizProjeto
try {
    git archive --format=zip --output="$arquivoZip" HEAD
} finally {
    Pop-Location
}

Write-Host "Pacote gerado com sucesso: $arquivoZip"
