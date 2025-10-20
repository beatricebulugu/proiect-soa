<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'profesor') {
    header('Location: ../login.php');
    exit;
}

$utilizator_id = $_SESSION['utilizator_id'];
$elev_id = $_GET['elev'] ?? null;

if (!$elev_id) {
    die("ID elev lipsă.");
}

// Verifică ce profesor e logat
$stmt = $pdo->prepare("SELECT profesor_id FROM utilizatori WHERE id = :id");
$stmt->execute(['id' => $utilizator_id]);
$profesor_id = $stmt->fetchColumn();

if (!$profesor_id) {
    die("Profesor invalid.");
}

// Preia materiile predate de profesor
$stmt = $pdo->prepare("SELECT m.id, m.denumire
                       FROM profesor_materie pm
                       JOIN materie m ON pm.materie_id = m.id
                       WHERE pm.profesor_id = :id");
$stmt->execute(['id' => $profesor_id]);
$materii = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materie_id = $_POST['materie_id'] ?? '';
    $valoare = $_POST['valoare'] ?? '';
    $data = $_POST['data'] ?? date('Y-m-d');

    if ($materie_id && $valoare && $data) {
        $stmt = $pdo->prepare("INSERT INTO nota (elev_id, materie_id, valoare, data)
                               VALUES (:elev_id, :materie_id, :valoare, :data)");
        $stmt->execute([
            'elev_id' => $elev_id,
            'materie_id' => $materie_id,
            'valoare' => $valoare,
            'data' => $data
        ]);
        $mesaj = "✅ Nota a fost adăugată cu succes.";
    } else {
        $mesaj = "❌ Completează toate câmpurile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Adaugă notă</title>
</head>
<body>
    <h2>➕ Adaugă notă pentru elevul #<?= $elev_id ?></h2>
    <p><a href="clase_mele.php">← Înapoi la clase</a></p>

    <?php if ($mesaj): ?>
        <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>;"><?= $mesaj ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Materie:</label><br>
        <select name="materie_id" required>
            <option value="">-- Alege materia --</option>
            <?php foreach ($materii as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['denumire']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Notă:</label><br>
        <input type="number" name="valoare" min="1" max="10" step="0.25" required><br><br>

        <label>Data:</label><br>
        <input type="date" name="data" value="<?= date('Y-m-d') ?>" required><br><br>

        <button type="submit">Adaugă nota</button>
    </form>
</body>
</html>
