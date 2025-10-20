<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

// Include mPDF (modern)
require_once __DIR__ . '/../vendor/autoload.php';
use Mpdf\Mpdf;

// Preia toți elevii
$sql = "SELECT e.id, e.nume, e.prenume, e.data_nasterii, e.telefon, e.cnp, c.nume AS clasa
        FROM elev e
        LEFT JOIN clasa c ON e.clasa_id = c.id
        ORDER BY e.nume ASC";
$stmt = $pdo->query($sql);
$elevi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Creare PDF
$mpdf = new Mpdf();
$mpdf->SetTitle('Lista Elevilor');

$html = '
<h2 style="text-align: center;">Lista Elevilor</h2>
<table border="1" width="100%" cellpadding="5" cellspacing="0">
<tr>
    <th>ID</th>
    <th>Nume</th>
    <th>Prenume</th>
    <th>Clasa</th>
    <th>Data nașterii</th>
    <th>Telefon</th>
    <th>CNP</th>
</tr>';

foreach ($elevi as $e) {
    $html .= '<tr>
        <td>' . $e['id'] . '</td>
        <td>' . htmlspecialchars($e['nume']) . '</td>
        <td>' . htmlspecialchars($e['prenume']) . '</td>
        <td>' . htmlspecialchars($e['clasa']) . '</td>
        <td>' . $e['data_nasterii'] . '</td>
        <td>' . $e['telefon'] . '</td>
        <td>' . $e['cnp'] . '</td>
    </tr>';
}

$html .= '</table>';

$mpdf->WriteHTML($html);
$mpdf->Output('lista_elevi.pdf', \Mpdf\Output\Destination::INLINE);
