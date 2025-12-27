<?php
require_once __DIR__ . '/libs/fpdf/fpdf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['scanData'])) {
    http_response_code(400);
    echo "Datos insuficientes.";
    exit;
}

$scan = json_decode($_POST['scanData'], true);
if (!$scan) {
    file_put_contents(__DIR__ . '/debug_scanData.txt', $_POST['scanData']);
    http_response_code(400);
    echo "Datos de escaneo inválidos. Error JSON: " . json_last_error_msg();
    exit;
}
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFillColor(38, 92, 75); // #265C4B
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 14, utf8_decode('Fortify AI - Resultado del Escaneo'), 0, 1, 'C', true);
        $this->Ln(2);
    }
    function Footer()
    {
        $this->SetY(-18);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, utf8_decode('Generado por Fortify AI | Universidad de las Fuerzas Armadas ESPE Sede Santo Domingo'), 0, 0, 'C');
    }

    // Método para calcular el número de líneas que ocupará un texto en una celda (necesario para MultiCell)
    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// Procesar datos
$fecha_impresion = date('Y-m-d H:i:s');
$ip = isset($scan['ip']) ? $scan['ip'] : '-';
$fecha_escaneo = isset($scan['timestamp']) ? $scan['timestamp'] : '-';
$puertos = isset($scan['ports']) ? $scan['ports'] : [];
$total_puertos = count($puertos);
$abiertos = 0;
$cerrados = 0;
$total_cves = 0;
foreach ($puertos as $p) {
    if (isset($p['state']) && strtolower($p['state']) === 'open') $abiertos++;
    if (isset($p['state']) && strtolower($p['state']) === 'closed') $cerrados++;
    if (!empty($p['cves'])) $total_cves += count($p['cves']);
}

// Resumen inicial alineado
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(38, 92, 75);
$pdf->Cell(0, 8, utf8_decode('Resumen del Escaneo'), 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(40, 40, 40);

$labelWidth = 65; // ancho fijo para la etiqueta
$valueWidth = 100; // ancho fijo para el valor

$pdf->Cell($labelWidth, 7, utf8_decode("IP del host:"), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($valueWidth, 7, utf8_decode($ip), 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell($labelWidth, 7, utf8_decode("Fecha del escaneo:"), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($valueWidth, 7, utf8_decode($fecha_escaneo), 0, 1, 'L');


$pdf->SetFont('Arial', '', 10);
$pdf->Cell($labelWidth, 7, utf8_decode("Total de puertos detectados:"), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($valueWidth, 7, utf8_decode($total_puertos), 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell($labelWidth, 7, utf8_decode("Puertos abiertos:"), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($valueWidth, 7, utf8_decode($abiertos), 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell($labelWidth, 7, utf8_decode("Puertos cerrados:"), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($valueWidth, 7, utf8_decode($cerrados), 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell($labelWidth, 7, utf8_decode("Total de CVEs detectados:"), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($valueWidth, 7, utf8_decode($total_cves), 0, 1, 'L');

$pdf->Ln(2);

// Tabla de puertos y servicios (con columna de total de CVEs)
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(38, 92, 75);
$pdf->Cell(0, 8, utf8_decode('Puertos y Servicios Detectados'), 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(30, 30, 30);

if (!empty($puertos)) {
    $pdf->SetFillColor(20, 101, 81); // #146551
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(16, 7, 'Puerto', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Protocolo', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Estado', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Servicio', 1, 0, 'C', true);
    $pdf->Cell(45, 7, 'Producto', 1, 0, 'C', true);
    $pdf->Cell(30, 7, utf8_decode('Versión'), 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Total CVEs', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(30, 30, 30);
    foreach ($puertos as $p) {
    $num_cves = !empty($p['cves']) ? count($p['cves']) : 0;

    // Prepara los textos
    $row = [
        $p['portid'],
        $p['protocol'],
        $p['state'],
        utf8_decode($p['service']),
        utf8_decode($p['product']),
        utf8_decode($p['version']),
        $num_cves
    ];
    // Anchos de cada columna
    $widths = [16, 20, 25, 30, 45, 30, 25];

    // Calcula la altura máxima de la fila
    $maxLines = 1;
    for ($i = 0; $i < count($row); $i++) {
        $nb = $pdf->NbLines($widths[$i], $row[$i]);
        if ($nb > $maxLines) $maxLines = $nb;
    }
    $rowHeight = 7 * $maxLines;

    // Guarda la posición inicial
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Dibuja cada celda de la fila con la misma altura
    for ($i = 0; $i < count($row); $i++) {
        $align = ($i == 0 || $i == 1 || $i == 2 || $i == 6) ? 'C' : 'L';
        // Guarda la posición antes de escribir
        $cellX = $pdf->GetX();
        $cellY = $pdf->GetY();

        // Dibuja el borde de la celda
        $pdf->Rect($cellX, $cellY, $widths[$i], $rowHeight);

        // Escribe el texto usando MultiCell, pero sin salto de línea automático
        $pdf->MultiCell($widths[$i], 7, $row[$i], 0, $align);

        // Mueve el cursor a la derecha de la celda actual
        $pdf->SetXY($cellX + $widths[$i], $cellY);
    }
    // Salto de línea para la siguiente fila
    $pdf->SetXY($x, $y + $rowHeight);
}

    
} else {
    $pdf->Cell(0, 8, 'No se detectaron puertos abiertos.', 0, 1);
}
$pdf->Ln(2);

// CVEs agrupados por puerto
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(38, 92, 75);
$pdf->Cell(0, 8, utf8_decode('CVEs Detectados por Puerto'), 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(183, 28, 28);

$hay_cves = false;
foreach ($puertos as $p) {
    if (!empty($p['cves'])) {
        $hay_cves = true;
        $num_cves = count($p['cves']);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, utf8_decode("Puerto {$p['portid']} ({$p['service']}): $num_cves CVE(s)"), 0, 1);
        $pdf->SetFont('Arial', '', 9);
        foreach ($p['cves'] as $cve) {
            $pdf->Cell(0, 6, utf8_decode(" - $cve"), 0, 1);
        }
        $pdf->Ln(1);
    }
}
if (!$hay_cves) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 7, utf8_decode('No se detectaron CVEs asociados a los puertos.'), 0, 1);
}
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(38, 92, 75);
$pdf->Cell(0, 8, utf8_decode("Total de CVEs detectados: $total_cves"), 0, 1);
$pdf->Ln(2);

$pdf->Output('D', 'resultado_escaneo.pdf');
exit;
