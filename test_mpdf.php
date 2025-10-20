<?php
require_once __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

$mpdf = new Mpdf();
$mpdf->WriteHTML('<h1>Test PDF</h1>');
$mpdf->Output();
