<?php
$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta[1]==""){
header("location:".BASE_URL."movimientos");
}
$curl = curl_init(); //inicia la sesión cURL
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER."src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token']."&data=".$ruta[1], //url a la que se conecta
    CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
    CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
    CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
    CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
    CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
    CURLOPT_CUSTOMREQUEST => "GET", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: ".BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ), //configura las cabeceras enviadas al servicio
)); //curl_setopt_array configura las opciones para una transferencia cURL

$response = curl_exec($curl); // respuesta generada
$err = curl_error($curl); // muestra errores en caso de existir

curl_close($curl); // termina la sesión 

if ($err) {
    echo "cURL Error #:" . $err; // mostramos el error
} else {
      // en caso de funcionar correctamente
   /* echo $_SESSION['sesion_sigi_id'];
    echo $_SESSION['sesion_sigi_token'];*/

    $respuesta= json_decode($response);
   // print_r($respuesta);
 $contenido_pdf = '';

 $contenido_pdf .='
 
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Papeleta de Rotación de Bienes</title>

  <style>
    body{
      font-family: Arial, sans-serif;
      margin: 40px;
    }

    h1{
      text-align: center;
      text-transform: uppercase;
      font-size: 15px;
      margin-bottom: 30px;
    }

    /* Línea de datos (Entidad, Área, etc.) */
    .field{
      margin: 6px 0;
      font-weight: bold;
    }
    .field span{
      font-weight: normal;
      border-bottom: 1px dotted #000;
      padding: 0 50px 2px 4px;   /* espacio para escribir */
      display: inline-block;
      min-width: 250px;
    }

    /* Tabla de bienes */
    table{
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td{
      border: 1px solid #000;
    }
    th, td{
      text-align: center;
      padding: 6px;
      font-size: 10px;
    }

    /* Columnas con anchos aproximados */
    .item-col   { width: 40px;  }
    .code-col   { width: 120px; }
    .name-col   { width: 200px; }
    .marca-col  { width: 100px; }
    .color-col  { width: 80px;  }
    .modelo-col { width: 100px; }
    .estado-col { width: 80px;  }

    /* Línea de fecha */
    .date-line{
      text-align: right;
      margin-top: 30px;
      font-size: 14px;
    }

    /* Firmas */
    .signatures{
      margin-top: 60px;
      width: 100%;
      overflow: hidden;          /* para limpiar flotados */
    }
    .sig{
      width: 50%;
      float: left;
      text-align: center;
    }
    .sig hr{
      width: 60%;
      border: 0;
      border-top: 1px solid #000;
      margin-bottom: 4px;
    }
  </style>
</head>

<body>

  <h1>Papeleta de Rotación de Bienes</h1>

  <div class="field">ENTIDAD&nbsp;&nbsp;: <span>DIRECCIÓN REGIONAL DE EDUCACIÓN – AYACUCHO</span></div>
  <div class="field">ÁREA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span>OFICINA DE ADMINISTRACIÓN</span></div>

  <div class="field">ORIGEN&nbsp;&nbsp;: <span class="underline">'. $respuesta->amb_origen->codigo.' - '.$respuesta->amb_origen->detalle .'</span></div>
  <div class="field">DESTINO&nbsp;: <span class="underline">'. $respuesta->amb_destino->codigo.' - '.$respuesta->amb_destino->detalle.'</span></div>
  <div class="field">MOTIVO&nbsp;(*) : <span class="underline"> '. $respuesta->movimiento->descripcion.' </span></div>

  <table>
    <thead>
      <tr>
        <th class="item-col">ITEM</th>
        <th class="code-col">CÓDIGO<br />PATRIMONIAL</th>
        <th class="name-col">NOMBRE DEL BIEN</th>
        <th class="marca-col">MARCA</th>
        <th class="color-col">COLOR</th>
        <th class="modelo-col">MODELO</th>
        <th class="estado-col">ESTADO</th>
      </tr>
    </thead>
    <tbody>
 ';

?>


        <?php
        $contador= 1;
        foreach($respuesta->detalle as $bien){
            $contenido_pdf.= "<tr>";
            $contenido_pdf.= "<td>".$contador."</td>";
            $contenido_pdf.= "<td>".$bien->cod_patrimonial."</td>";
            $contenido_pdf.= "<td>".$bien->denominacion."</td>";
            $contenido_pdf.= "<td>".$bien->marca."</td>";
            $contenido_pdf.= "<td>".$bien->modelo."</td>";
            $contenido_pdf.= "<td>".$bien->color."</td>";
            $contenido_pdf.= "<td>".$bien->estado_conservacion."</td>";
            $contenido_pdf.= "</tr>";
           $contador+=1;
        }
        

     
 $contenido_pdf .='
    </tbody>
  </table>

  <div class="date-line">Ayacucho, _____ de _____ del 2024</div>

  <div class="signatures">
    <div class="sig">
      <hr />
      <span>ENTREGUÉ CONFORME</span>
    </div>
    <div class="sig">
      <hr />
      <span>RECIBÍ CONFORME</span>
    </div>
  </div>

</body>
</html>
    ';
    ?>


<?php
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

$pdf= new TCPDF();
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Alex');
$pdf->SetTitle('Reporte de Movimientos');

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// salto de paginab automatico
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set font
$pdf->SetFont('helvetica', 'B', 8);

// add a page
$pdf->AddPage();

// output the HTML content
$pdf->writeHTML($contenido_pdf);
ob_clean();
//Close and output PDF document
$pdf->Output('sd', 'I');

}



