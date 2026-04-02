<?php
// Servico de PDF simples para relatorios
class PdfService
{
    public function gerarTabela($titulo, $periodo, $cabecalho, $linhas, $totais = null)
    {
        require_once __DIR__ . '/Fpdf.php';
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->Cell(0, 8, $titulo, 0, 1);
        $pdf->SetFont('Helvetica', '', 11);
        $pdf->Cell(0, 6, $periodo, 0, 1);
        $pdf->Ln(4);

        $larguraPagina = 190;
        $colunas = count($cabecalho);
        $larguraColuna = $colunas > 0 ? $larguraPagina / $colunas : 190;

        $pdf->SetFont('Helvetica', 'B', 10);
        foreach ($cabecalho as $col) {
            $pdf->Cell($larguraColuna, 7, $this->limitar($col), 1, 0);
        }
        $pdf->Ln(7);

        $pdf->SetFont('Helvetica', '', 9);
        foreach ($linhas as $linha) {
            foreach ($linha as $valor) {
                $pdf->Cell($larguraColuna, 6, $this->limitar($valor), 1, 0);
            }
            $pdf->Ln(6);
        }

        if (!empty($totais)) {
            $pdf->SetFont('Helvetica', 'B', 10);
            foreach ($totais as $valor) {
                $pdf->Cell($larguraColuna, 7, $this->limitar($valor), 1, 0);
            }
            $pdf->Ln(7);
        }

        $pdf->Ln(10);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 6, 'Assinatura do Responsavel: ____________________________', 0, 1);

        $pdf->Output('I', 'relatorio.pdf');
        exit;
    }

    private function limitar($texto, $limite = 32)
    {
        $texto = (string)$texto;
        $texto = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
        if (strlen($texto) > $limite) {
            return substr($texto, 0, $limite - 3) . '...';
        }
        return $texto;
    }
}
