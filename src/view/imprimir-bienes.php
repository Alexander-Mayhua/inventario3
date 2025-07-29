<?php
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');
require_once('./src/library/conexionn.php');
session_start();

// Parámetros de filtro
$codigo = $_POST['busqueda_tabla_codigo'] ?? '';
$ambiente = $_POST['busqueda_tabla_ambiente'] ?? '';
$denominacion = $_POST['busqueda_tabla_denominacion'] ?? '';

// Conexión DB
$conexion = Conexion::connect();

// Consulta SQL con filtros
$sql = "SELECT id, id_ingreso_bienes, id_ambiente, cod_patrimonial, denominacion,marca,modelo,tipo,color,serie FROM bienes
        WHERE cod_patrimonial LIKE '$codigo%' AND id_ambiente LIKE '$ambiente%' AND denominacion LIKE '$denominacion%'
        ORDER BY id ASC";
$resultado = $conexion->query($sql);

// Contenido HTML del PDF
$contenido_pdf = '
<h1 style="text-align: center; font-size:14px;">REPORTE DE BIENES</h1>
<div class="field"><strong>ENTIDAD</strong>&nbsp;&nbsp;: <span>DIRECCIÓN REGIONAL DE EDUCACIÓN – AYACUCHO</span></div>
<div class="field"><strong>ÁREA</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span>OFICINA DE ADMINISTRACIÓN</span></div>
<br>
</br>
<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr style="background-color:#f2f2f2; font-size:10px;">
            <th>ID</th>
            <th>Id Ingreso</th>
            <th>Id Ambiente</th>
            <th>Codigo patrimonial</th>
            <th>denominacion</th>
            <th>marca</th>
            <th>modelo</th>
            <th>tipo</th>
            <th>color</th>
            <th>serie</th>
         
        </tr>
    </thead>
    <tbody>';

while ($fila = $resultado->fetch_assoc()) {
    $contenido_pdf .= '<tr style="font-size:9px;">
        <td>' . $fila['id'] . '</td>
        <td>' . $fila['id_ingreso_bienes'] . '</td>
        <td>' . $fila['id_ambiente'] . '</td>
        <td>' . $fila['cod_patrimonial'] . '</td>
        <td>' . $fila['denominacion'] . '</td>
        <td>' . $fila['marca'] . '</td>
        <td>' . $fila['modelo'] . '</td>
        <td>' . $fila['tipo'] . '</td>
        <td>' . $fila['color'] . '</td>
        <td>' . $fila['serie'] . '</td>

    </tr>';
}
$contenido_pdf .= '</tbody></table>';

// Fecha y firmas
$contenido_pdf .= '
<br>
</br>
<div style="text-align: right; margin-top: 30px; font-size: 12px;">
    Ayacucho, _____ de _____ del 2025
</div>

<div style="margin-top: 100px; text-align: center;">
    <div style="display: inline-block; width: 40%; margin-right: 8%; text-align: center;">
        <div style="border-top: 1px solid #000; width: 100%; margin-bottom: 5px;"></div>
        <span>ENTREGUÉ CONFORME</span>
    </div>
    <div style="display: inline-block; width: 40%; text-align: center;">
        <div style="border-top: 1px solid #000; width: 100%; margin-bottom: 5px;"></div>
        <span>RECIBÍ CONFORME</span>
    </div>
</div>';

// Clase personalizada con encabezado y pie
class MYPDF extends TCPDF {
    public function Header() {
        $this->Image('./src/view/pp/assets/images/logo_izquierdo.jpeg', 15, 4, 16.4);
        $this->Image('./src/view/pp/assets/images/logo_derecho.jpg', 170, 2, 25);
        $this->SetY(5);
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(0, 5, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');
        $this->Cell(0, 5, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, 1, 'C');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 5, 'DIRECCIÓN DE ADMINISTRACIÓN', 0, 1, 'C');
        $this->SetDrawColor(0, 64, 128);
        $this->SetLineWidth(0.4);
        $this->Line(15, 28, 195, 28);
        $this->SetLineWidth(0.2);
        $this->Line(15, 30, 195, 30);
        $this->SetY(30);
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(0, 5, 'ANEXO – 4 -', 0, 1, 'C');
        $this->Ln(5);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 7);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $html = '
        <table width="100%" style="font-size:7px; padding-top:3px;">
            <tr>
                <td width="33%"></td>
                <td width="34%" align="center">
                    <a href="https://www.dreaya.gob.pe" style="color: #0000EE; text-decoration: underline;">www.dreaya.gob.pe</a>
                </td>
                <td width="33%" align="right">
                    Jr. 28 de Julio N° 385 – Huamanga<br/>
                    ☎ (066) 31-2364<br/>
                    🏢 (066) 31-1395 Anexo 55001
                </td>
            </tr>
        </table>';
        $this->writeHTML($html, true, false, false, false, '');
    }
}

// Crear y mostrar el PDF
$pdf = new MYPDF();
$pdf->SetMargins(15, 40, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

ob_clean();
$pdf->Output('reporte_Bienes.pdf', 'I');