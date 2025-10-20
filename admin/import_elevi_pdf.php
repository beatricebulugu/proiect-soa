<?php
require_once '../session.php';
require_once '../index.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Smalot\PdfParser\Parser;

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fisier'])) {
    $fisier = $_FILES['fisier']['tmp_name'];

    try {
        // Creăm instanța PdfParser
        $parser = new Parser();
        $pdf = $parser->parseFile($fisier);
        $text = $pdf->getText();

        // Exemplu simplu de procesare a textului extras din PDF
        // Presupunem că datele elevilor sunt pe linii, cu un separator specific (ex: 'Nume Prenume Clasa Telefon CNP')
        $linii = explode("\n", $text);

        // Procesăm fiecare linie
        foreach ($linii as $linie) {
            // Extraire datele din linie (ex: cu un separator specific)
            $date = explode(' ', trim($linie));

            if (count($date) >= 7) { // Verificăm dacă sunt suficiente date
                $nume = $date[0];
                $prenume = $date[1];
                $clasa_id = $date[2]; // presupunem că Clasa este primul număr
                $telefon = $date[3];
                $cnp = $date[4];

                // Inserăm datele în baza de date
                $stmt = $pdo->prepare("INSERT INTO elev (nume, prenume, clasa_id, telefon, cnp) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nume, $prenume, $clasa_id, $telefon, $cnp]);
            }
        }

        $mesaj = "✅ Importul a fost realizat cu succes!";
    } catch (Exception $e) {
        $mesaj = "❌ Eroare la citirea fișierului PDF: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">
    <title>Import elevi din PDF</title>
</head>
<body>
    <h2>Importă elevi din PDF</h2>
    <p><a href="elevi.php">← Înapoi la lista elevilor</a></p>

    <?php if ($mesaj): ?>
        <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>;"><?= $mesaj ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Selectează fișier PDF:</label><br><br>
        <input type="file" name="fisier" accept=".pdf" required><br><br>
        <button type="submit">Importă</button>
    </form>
</body>
</html>
