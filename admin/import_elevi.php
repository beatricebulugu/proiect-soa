<?php
require_once '../session.php';
require_once '../index.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once __DIR__ . '/../vendor/autoload.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fisier'])) {
    $fisier = $_FILES['fisier']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($fisier);
        $sheet = $spreadsheet->getActiveSheet();
        $randuri = $sheet->toArray();

        // Se presupune că prima linie este header
        for ($i = 1; $i < count($randuri); $i++) {
            $r = $randuri[$i];
            // A: Nume, B: Prenume, C: Data nașterii, D: Clasa ID, E: Adresa, F: Telefon, G: CNP

            $sql = "INSERT INTO elev (nume, prenume, data_nasterii, clasa_id, adresa, telefon, cnp)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$r[0], $r[1], $r[2], $r[3], $r[4], $r[5], $r[6]]);
        }

        $mesaj = "✅ Importul a fost realizat cu succes!";
    } catch (Exception $e) {
        $mesaj = "❌ Eroare la citirea fișierului: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Import elevi</title>
</head>
<body>
    <h2>📥 Import elevi din Excel</h2>
    <p><a href="elevi.php">← Înapoi la lista elevilor</a></p>

    <?php if ($mesaj): ?>
        <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>;"><?= $mesaj ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Selectează fișier Excel (.xlsx):</label><br><br>
        <input type="file" name="fisier" accept=".xlsx" required><br><br>
        <button type="submit">Importă</button>
    </form>
</body>
</html>
