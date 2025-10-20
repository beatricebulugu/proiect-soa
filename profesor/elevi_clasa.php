<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'profesor') {
    header('Location: ../login.php');
    exit;
}

$clasa_id = $_GET['id'] ?? null;
$utilizator_id = $_SESSION['utilizator_id'];

// Verifică dacă profesorul are acces la această clasă
$sql = "SELECT pc.clasa_id
        FROM profesor_clasa pc
        JOIN utilizatori u ON pc.profesor_id = u.profesor_id
        WHERE u.id = :uid AND pc.clasa_id = :clasa_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'uid' => $utilizator_id,
    'clasa_id' => $clasa_id
]);

if ($stmt->rowCount() === 0) {
    die("❌ Acces interzis la această clasă.");
}

// Preluare elevi din clasă
$sql = "SELECT * FROM elev WHERE clasa_id = :id ORDER BY nume, prenume";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $clasa_id]);
$elevi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Elevii din clasă</title>
</head>
<body>
    <h2>👩‍🎓 Elevii din această clasă</h2>
    <p><a href="clase_mele.php">← Înapoi la clasele mele</a></p>

    <?php if (empty($elevi)): ?>
        <p>Nu există elevi în această clasă.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Acțiuni</th>
            </tr>
            <?php foreach ($elevi as $e): ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['nume']) ?></td>
                    <td><?= htmlspecialchars($e['prenume']) ?></td>
                    <td>
                        <a href="adauga_nota.php?elev=<?= $e['id'] ?>">➕ Adaugă notă</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
