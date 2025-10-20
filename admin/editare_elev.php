<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: elevi.php');
    exit;
}

// Preia elevul
$stmt = $pdo->prepare("SELECT * FROM elev WHERE id = ?");
$stmt->execute([$id]);
$elev = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$elev) {
    echo "Elevul nu a fost găsit.";
    exit;
}

// Preia clasele pentru dropdown
$clase = $pdo->query("SELECT id, nume FROM clasa ORDER BY nume")->fetchAll(PDO::FETCH_ASSOC);

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = $_POST['nume'] ?? '';
    $prenume = $_POST['prenume'] ?? '';
    $data_nasterii = $_POST['data_nasterii'] ?? '';
    $clasa_id = $_POST['clasa_id'] ?? '';
    $adresa = $_POST['adresa'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $cnp = $_POST['cnp'] ?? '';

    if ($nume && $prenume && $data_nasterii && $clasa_id && $telefon && $cnp) {
        $sql = "UPDATE elev SET nume = :nume, prenume = :prenume, data_nasterii = :data_nasterii,
                clasa_id = :clasa_id, adresa = :adresa, telefon = :telefon, cnp = :cnp WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                ':nume' => $nume,
                ':prenume' => $prenume,
                ':data_nasterii' => $data_nasterii,
                ':clasa_id' => $clasa_id,
                ':adresa' => $adresa,
                ':telefon' => $telefon,
                ':cnp' => $cnp,
                ':id' => $id
            ]);
            $mesaj = "✅ Elev actualizat cu succes!";
            // Refresh datele
            $stmt = $pdo->prepare("SELECT * FROM elev WHERE id = ?");
            $stmt->execute([$id]);
            $elev = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $mesaj = "❌ Eroare: " . $e->getMessage();
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

    <title>Editare elev</title>
</head>
<body>
    <h2>Editare elev</h2>
    <p><a href="elevi.php">← Înapoi la listă</a></p>

    <?php if ($mesaj): ?>
        <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>;"><?php echo $mesaj; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Nume:</label><br>
        <input type="text" name="nume" value="<?= htmlspecialchars($elev['nume']) ?>" required><br><br>

        <label>Prenume:</label><br>
        <input type="text" name="prenume" value="<?= htmlspecialchars($elev['prenume']) ?>" required><br><br>

        <label>Data nașterii:</label><br>
        <input type="date" name="data_nasterii" value="<?= $elev['data_nasterii'] ?>" required><br><br>

        <label>Clasa:</label><br>
        <select name="clasa_id" required>
            <option value="">-- Alege clasa --</option>
            <?php foreach ($clase as $clasa): ?>
                <option value="<?= $clasa['id'] ?>" <?= $elev['clasa_id'] == $clasa['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($clasa['nume']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Adresă:</label><br>
        <input type="text" name="adresa" value="<?= htmlspecialchars($elev['adresa']) ?>"><br><br>

        <label>Telefon:</label><br>
        <input type="text" name="telefon" value="<?= $elev['telefon'] ?>" required><br><br>

        <label>CNP:</label><br>
        <input type="text" name="cnp" value="<?= $elev['cnp'] ?>" required><br><br>

        <button type="submit">Salvează modificările</button>
    </form>
</body>
</html>
