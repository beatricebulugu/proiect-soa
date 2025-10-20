<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

$mesaj = '';

// Preluăm clasele existente
$clase = $pdo->query("SELECT id, nume FROM clasa ORDER BY nume")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = $_POST['nume'] ?? '';
    $prenume = $_POST['prenume'] ?? '';
    $data_nasterii = $_POST['data_nasterii'] ?? '';
    $clasa_id = $_POST['clasa_id'] ?? '';
    $adresa = $_POST['adresa'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $cnp = $_POST['cnp'] ?? '';

    if ($nume && $prenume && $data_nasterii && $clasa_id && $telefon && $cnp) {
        $sql = "INSERT INTO elev (nume, prenume, data_nasterii, clasa_id, adresa, telefon, cnp)
                VALUES (:nume, :prenume, :data_nasterii, :clasa_id, :adresa, :telefon, :cnp)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':nume' => $nume,
                ':prenume' => $prenume,
                ':data_nasterii' => $data_nasterii,
                ':clasa_id' => $clasa_id,
                ':adresa' => $adresa,
                ':telefon' => $telefon,
                ':cnp' => $cnp
            ]);
            $mesaj = "✅ Elev adăugat cu succes!";
        } catch (PDOException $e) {
            $mesaj = "❌ Eroare la adăugare: " . $e->getMessage();
        }
    } else {
        $mesaj = "❌ Completează toate câmpurile obligatorii.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Adaugă elev</title>
</head>
<body>
    <h2>Adaugă elev nou</h2>
    <p><a href="elevi.php">← Înapoi la listă</a></p>

    <?php if ($mesaj): ?>
        <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>;"><?php echo $mesaj; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Nume:</label><br>
        <input type="text" name="nume" required><br><br>

        <label>Prenume:</label><br>
        <input type="text" name="prenume" required><br><br>

        <label>Data nașterii:</label><br>
        <input type="date" name="data_nasterii" required><br><br>

        <label>Clasa:</label><br>
        <select name="clasa_id" required>
            <option value="">-- Alege clasa --</option>
            <?php foreach ($clase as $clasa): ?>
                <option value="<?= $clasa['id'] ?>"><?= htmlspecialchars($clasa['nume']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Adresă:</label><br>
        <input type="text" name="adresa"><br><br>

        <label>Telefon:</label><br>
        <input type="text" name="telefon" required><br><br>

        <label>CNP:</label><br>
        <input type="text" name="cnp" required><br><br>

        <button type="submit">Adaugă elev</button>
    </form>
</body>
</html>
