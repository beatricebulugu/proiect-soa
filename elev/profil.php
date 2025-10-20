<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'elev') {
    header('Location: ../login.php');
    exit;
}

$utilizator_id = $_SESSION['utilizator_id'];

// Preluăm elev_id din utilizatori
$stmt = $pdo->prepare("SELECT elev_id FROM utilizatori WHERE id = :id");
$stmt->execute(['id' => $utilizator_id]);
$elev_id = $stmt->fetchColumn();

if (!$elev_id) {
    die("Elevul nu este asociat cu acest cont.");
}

$mesaj = '';

// Preluăm datele curente ale elevului
$stmt = $pdo->prepare("SELECT * FROM elev WHERE id = :id");
$stmt->execute(['id' => $elev_id]);
$elev = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresa = $_POST['adresa'] ?? '';
    $telefon = $_POST['telefon'] ?? '';

    if ($adresa && $telefon) {
        $stmt = $pdo->prepare("UPDATE elev SET adresa = :adresa, telefon = :telefon WHERE id = :id");
        $stmt->execute([
            'adresa' => $adresa,
            'telefon' => $telefon,
            'id' => $elev_id
        ]);
        $mesaj = "✅ Datele au fost actualizate cu succes.";
        // Reîncarcă datele
        $stmt = $pdo->prepare("SELECT * FROM elev WHERE id = :id");
        $stmt->execute(['id' => $elev_id]);
        $elev = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $mesaj = "❌ Completează toate câmpurile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Profil elev</title>
</head>
<body>
    <h2>Profilul meu</h2>
    <p><a href="note_mele.php">← Înapoi la note</a></p>

    <?php if ($mesaj): ?>
        <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>;"><?= $mesaj ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Adresă:</label><br>
        <input type="text" name="adresa" value="<?= htmlspecialchars($elev['adresa']) ?>" required><br><br>

        <label>Telefon:</label><br>
        <input type="text" name="telefon" value="<?= htmlspecialchars($elev['telefon']) ?>" required><br><br>

        <button type="submit">Salvează modificările</button>
    </form>
</body>
</html>
