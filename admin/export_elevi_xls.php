<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

// Include PhpSpreadsheet
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Preluare elevi
$sql = "SELECT e.id, e.nume, e.prenume, e.data_nasterii, c.nume AS clasa, e.telefon, e.cnp
        FROM elev e
        LEFT JOIN clasa c ON e.clasa_id = c.id
        ORDER BY e.nume ASC";

$stmt = $pdo->query($sql);
$elevi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inițializare fișier Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Lista Elevilor');

// Headere
$sheet->fromArray(['ID', 'Nume', 'Prenume', 'Clasa', 'Data nașterii', 'Telefon', 'CNP'], NULL, 'A1');

// Date
$rand = 2;
foreach ($elevi as $e) {
    $sheet->setCellValue("A$rand", $e['id']);
    $sheet->setCellValue("B$rand", $e['nume']);
    $sheet->setCellValue("C$rand", $e['prenume']);
    $sheet->setCellValue("D$rand", $e['clasa']);
    $sheet->setCellValue("E$rand", $e['data_nasterii']);
    $sheet->setCellValue("F$rand", $e['telefon']);
    $sheet->setCellValue("G$rand", $e['cnp']);
    $rand++;
}

// Headers pentru descărcare
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lista_elevi.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
