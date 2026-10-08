<?php
require 'vendor/autoload.php';

try {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Test');
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('test.xlsx');
    echo "Success";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
} catch (\Throwable $e) {
    echo "Fatal Error: " . $e->getMessage();
}
