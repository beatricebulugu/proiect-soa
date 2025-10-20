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

// Preluăm notele elevului
$sql = "SELECT m.denumire AS materie, n.valoare, n.data
        FROM nota n
        JOIN materie m ON n.materie_id = m.id
        WHERE n.elev_id = :elev_id
        ORDER BY n.data DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['elev_id' => $elev_id]);
$note = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Notele mele</title>
</head>
<body>
    <h2>📋 Notele mele</h2>
    <p><a href="raport_note.php">📊 Vezi raportul grafic al notelor mele</a></p>
    <p><a href="profil.php">👤 Vezi și editează profilul meu</a></p>


    <p><a href="../logout.php">Logout</a></p>

    <?php if (empty($note)): ?>
        <p>Nu există note înregistrate pentru tine.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Materie</th>
                <th>Notă</th>
                <th>Data</th>
            </tr>
            <?php foreach ($note as $n): ?>
                <tr>
                    <td><?= htmlspecialchars($n['materie']) ?></td>
                    <td><?= $n['valoare'] ?></td>
                    <td><?= $n['data'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
