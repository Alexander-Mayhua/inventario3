<?php

require 'vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
//agregar
$spreadsheet->getProperties()->setCreator("yp")->setLastModifiedBy("yo")->setTitle("yo")->setDescription("yo");

$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setCellValue('A1', 'Hola Mundo !');
//agregar
$activeWorksheet->setCellValue("A1","hello word");
$activeWorksheet->setCellValue("A2","DNI");
//$activeWorksheet->setCellValue("B2","71771181");
// Llenar columna A (filas 1 a 10)
// Escribir del 1 al 30 horizontalmente en la fila 1


for ($i = 1; $i <= 10; $i++) {
    $activeWorksheet->setCellValue("A" . $i, $i); // Escribe 1, 2, ..., 10 en A1:A10
}


for ($i = 1; $i <= 30; $i++) {
    // Convertir número a letra de columna: 1 = A, 2 = B, etc.
    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    $activeWorksheet->setCellValue($columna . '1', $i);
}
$numero = 1;
for ($i = 1; $i <= 12; $i++) {
    $sheet->setCellValue("B$i", $numero);         // Número fijo (1)
    $sheet->setCellValue("C$i", "x");             // Símbolo de multiplicar
    $sheet->setCellValue("D$i", "=");             // Igual
    $sheet->setCellValue("E$i", $numero * $i);    // Resultado
}

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');
