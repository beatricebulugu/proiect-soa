<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'profesor') {
    header('Location: ../login.php');
    exit;
}

$profesor_id = $_SESSION['utilizator_id'];

// Preia clasele la care predă profesorul curent
$sql = "SELECT c.id, c.nume
        FROM profesor_clasa pc
        JOIN clasa c ON pc.clasa_id = c.id
        WHERE pc.profesor_id = (
            SELECT profesor_id FROM utilizatori WHERE id = :uid
        )";

$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $profesor_id]);
$clase = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Clasele mele</title>
</head>
<body>
    <h2>👨‍🏫 Clasele la care predau</h2>
    <p><a href="../logout.php">Logout</a></p>

    <?php if (empty($clase)): ?>
        <p>Nu aveți clase asociate momentan.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($clase as $clasa): ?>
                <li>
                    <a href="elevi_clasa.php?id=<?= $clasa['id'] ?>">
                        <?= htmlspecialchars($clasa['nume']) ?>
                    </a>
                    <p>
  <a href="raport_note.php">📋 Vezi raport note elevi</a>
</p>

                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
