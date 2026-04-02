<?php
/*
 * FPDF 1.82 - http://www.fpdf.org/
 * Versao reduzida com suporte basico a tabelas
 */
class FPDF
{
    protected $page;               // Pagina atual
    protected $n;                  // Numero de objetos
    protected $offsets;            // Offsets dos objetos
    protected $buffer;             // Buffer de saida
    protected $pages;              // Array de paginas
    protected $state;              // Estado do documento
    protected $fonts;              // Fontes usadas
    protected $FontFamily;         // Familia de fonte atual
    protected $FontStyle;          // Estilo de fonte atual
    protected $FontSizePt;         // Tamanho da fonte atual em pt
    protected $FontSize;           // Tamanho da fonte atual em unidade
    protected $w;                  // Largura da pagina
    protected $h;                  // Altura da pagina
    protected $k;                  // Fator de escala (pontos para unidade)
    protected $x;                  // Posicao atual em x
    protected $y;                  // Posicao atual em y
    protected $lMargin;            // Margem esquerda
    protected $tMargin;            // Margem superior
    protected $rMargin;            // Margem direita
    protected $bMargin;            // Margem inferior
    protected $AutoPageBreak;      // Quebra de pagina automatica
    protected $PageBreakTrigger;   // Posicao da quebra

    public function __construct($orientation='P', $unit='mm', $size='A4')
    {
        $this->page = 0;
        $this->n = 2;
        $this->buffer = '';
        $this->pages = [];
        $this->state = 0;
        $this->fonts = [];
        $this->FontFamily = '';
        $this->FontStyle = '';
        $this->FontSizePt = 12;
        $this->FontSize = 0;
        $this->k = ($unit == 'pt') ? 1 : (($unit == 'mm') ? 72/25.4 : 72/2.54);
        $size = $this->_getpagesize($size);
        if ($orientation == 'P') {
            $this->w = $size[0];
            $this->h = $size[1];
        } else {
            $this->w = $size[1];
            $this->h = $size[0];
        }
        $this->lMargin = 10;
        $this->tMargin = 10;
        $this->rMargin = 10;
        $this->bMargin = 10;
        $this->SetAutoPageBreak(true, 10);
        $this->SetFont('Helvetica', '', 11);
    }

    public function SetAutoPageBreak($auto, $margin)
    {
        $this->AutoPageBreak = $auto;
        $this->bMargin = $margin;
        $this->PageBreakTrigger = $this->h - $margin;
    }

    public function SetMargins($left, $top, $right=null)
    {
        $this->lMargin = $left;
        $this->tMargin = $top;
        $this->rMargin = ($right === null) ? $left : $right;
    }

    public function AddPage($orientation='')
    {
        if ($this->state == 0) {
            $this->_beginpage();
        } else {
            $this->_endpage();
            $this->_beginpage();
        }
    }

    protected function _beginpage()
    {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->state = 2;
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
    }

    protected function _endpage()
    {
        $this->state = 1;
    }

    public function SetFont($family, $style='', $size=0)
    {
        $family = strtolower($family);
        $style = strtoupper($style);
        if ($size == 0) {
            $size = $this->FontSizePt;
        }
        $this->FontFamily = $family;
        $this->FontStyle = $style;
        $this->FontSizePt = $size;
        $this->FontSize = $size / $this->k;
    }

    public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false)
    {
        $s = '';
        if ($fill || $border) {
            $op = $fill ? 'B' : 'S';
            $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x*$this->k, ($this->h-$this->y)*$this->k, $w*$this->k, -$h*$this->k, $op);
        }
        if ($txt !== '') {
            $s .= sprintf('BT /F1 %.2F Tf %.2F %.2F Td (%s) Tj ET ', $this->FontSizePt, ($this->x+1)*$this->k, ($this->h-($this->y+0.8))*$this->k, $this->_escape($txt));
        }
        $this->_out($s);
        $this->x += $w;
        if ($ln > 0) {
            $this->x = $this->lMargin;
            $this->y += $h;
        }
    }

    public function Ln($h=null)
    {
        $this->x = $this->lMargin;
        $this->y += ($h === null) ? $this->FontSize : $h;
    }

    public function Output($dest='I', $name='doc.pdf')
    {
        if ($this->state == 2) {
            $this->_endpage();
        }
        $this->_enddoc();
        if ($dest == 'I') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="'.$name.'"');
            echo $this->buffer;
        }
    }

    protected function _enddoc()
    {
        $this->_out('%PDF-1.3');
        $this->_out('1 0 obj');
        $this->_out('<< /Type /Catalog /Pages 2 0 R >>');
        $this->_out('endobj');
        $this->_out('2 0 obj');
        $this->_out('<< /Type /Pages /Kids [' . implode(' ', $this->_getpagekids()) . '] /Count ' . $this->page . ' >>');
        $this->_out('endobj');
        $this->_putpages();
        $this->_putresources();
        $this->_putinfo();
        $this->_putxref();
        $this->_puttrailer();
    }

    protected function _getpagekids()
    {
        $kids = [];
        for ($i=1; $i<=$this->page; $i++) {
            $kids[] = (3 + ($i-1)*2) . ' 0 R';
        }
        return $kids;
    }

    protected function _putpages()
    {
        for ($i=1; $i<=$this->page; $i++) {
            $this->_newobj();
            $this->_out('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $this->w*$this->k . ' ' . $this->h*$this->k . '] /Resources << /Font << /F1 5 0 R >> >> /Contents ' . ($this->n+1) . ' 0 R >>');
            $this->_out('endobj');
            $p = $this->pages[$i];
            $this->_newobj();
            $this->_out('<< /Length ' . strlen($p) . ' >>');
            $this->_out('stream');
            $this->_out($p);
            $this->_out('endstream');
            $this->_out('endobj');
        }
    }

    protected function _putresources()
    {
        $this->_newobj();
        $this->_out('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>');
        $this->_out('endobj');
    }

    protected function _putinfo()
    {
        $this->_newobj();
        $this->_out('<< /Producer (FPDF) /Title (Relatorio) >>');
        $this->_out('endobj');
    }

    protected function _putxref()
    {
        $offset = strlen($this->buffer);
        $this->_out('xref');
        $this->_out('0 ' . ($this->n+1));
        $this->_out('0000000000 65535 f ');
        foreach ($this->offsets as $o) {
            $this->_out(sprintf('%010d 00000 n ', $o));
        }
        $this->xref = $offset;
    }

    protected function _puttrailer()
    {
        $this->_out('trailer');
        $this->_out('<< /Size ' . ($this->n+1) . ' /Root 1 0 R /Info ' . $this->n . ' 0 R >>');
        $this->_out('startxref');
        $this->_out($this->xref);
        $this->_out('%%EOF');
    }

    protected function _newobj()
    {
        $this->n++;
        $this->offsets[$this->n] = strlen($this->buffer);
        $this->_out($this->n . ' 0 obj');
    }

    protected function _out($s)
    {
        $this->buffer .= $s . "\n";
        if ($this->state == 2) {
            $this->pages[$this->page] .= $s . "\n";
        }
    }

    protected function _escape($s)
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }

    protected function _getpagesize($size)
    {
        if (is_array($size)) {
            return $size;
        }
        if ($size == 'A4') {
            return [210, 297];
        }
        return [210, 297];
    }
}
