<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

$typeString = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
//$sheet->setCellValue('A1', 'Hello World !');

$headerColsJSON = '{
  "1": [
    {
      "index": "1",
      "row": "1",
      "value": "Titulo"
    }
  ],
  "2": [
    {
      "index": "1",
      "row": "2",
      "value": "Descripcion"
    }
  ],
  "3": [
    {
      "index": "1",
      "row": "3",
      "value": "Categoria"
    }
  ],
  "4": [
    {
      "index": "1",
      "row": "4",
      "value": "Precio"
    }
  ],
  "5": [
    {
      "index": "1",
      "row": "5",
      "value": "Moneda"
    }
  ],
  "6": [
    {
      "index": "1",
      "row": "6",
      "value": "Condicion"
    }
  ]
}';
//$headerCols = json_decode($headerColsJSON, null, 512, JSON_OBJECT_AS_ARRAY);
//var_dump($headerCols);
$headerCols = json_decode($headerColsJSON, true);

foreach ($headerCols as $key => $value) {
  $col = $value[0];
  //$sheet->setCellValueByColumnAndRow($col['index'],$col['row'],$col['value']);
  //$sheet->setCellValue('A'.$col['row'], $col['value']);
  //$sheet->setCellValueExplicit('A'.$col['row'], $col['value'], $typeString);
  //var_dump($col['index'],$col['row'],$col['value'], 'A'.$col['row']);
  unset($col);
}
unset($headerCols);

$rowArray = ['Titulo', 'Descripcion', 'Categoria', 'Precio', 'Moneda', 'Condicion'];
$spreadsheet->getActiveSheet()
    ->fromArray(
        $rowArray,   // The data to set
        NULL,        // Array values with this value will not be set
        'A1'         // Top left coordinate of the worksheet range where
                     //    we want to set these values (default is A1)
    );

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Meli-Demo-Publicador.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

