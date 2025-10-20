<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'profesor') {
    header('Location: ../login.php');
    exit;
}

$utilizator_id = $_SESSION['utilizator_id'];

// Preia profesor_id din utilizatori
$stmt = $pdo->prepare("SELECT profesor_id FROM utilizatori WHERE id = :id");
$stmt->execute(['id' => $utilizator_id]);
$profesor_id = $stmt->fetchColumn();

if (!$profesor_id) {
    die("Profesor invalid.");
}

// Preia clasele profesorului
$stmt = $pdo->prepare("SELECT clasa_id FROM profesor_clasa WHERE profesor_id = :pid");
$stmt->execute(['pid' => $profesor_id]);
$clase_profesor = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Pregătește lista clase ID-uri pentru SQL
$clase_in = implode(',', array_map('intval', $clase_profesor));

if (empty($clase_in)) {
    die("Nu aveți clase asociate.");
}

// Preia notele pentru elevii din clasele profesorului
$sql = "SELECT e.nume AS nume_elev, e.prenume, m.denumire AS materie, n.valoare, n.data
        FROM nota n
        JOIN elev e ON n.elev_id = e.id
        JOIN materie m ON n.materie_id = m.id
        WHERE e.clasa_id IN ($clase_in)
        ORDER BY e.nume, e.prenume, n.data DESC";

$stmt = $pdo->query($sql);
$note = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Raport note elevi</title>
</head>
<body>
    <h2>📋 Notele elevilor din clasele mele</h2>
    <p><a href="clase_mele.php">← Înapoi la clasele mele</a></p>

    <?php if (empty($note)): ?>
        <p>Nu există note introduse încă.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Elev</th>
                <th>Materie</th>
                <th>Notă</th>
                <th>Data</th>
            </tr>
            <?php foreach ($note as $n): ?>
                <tr>
                    <td><?= htmlspecialchars($n['nume_elev'] . ' ' . $n['prenume']) ?></td>
                    <td><?= htmlspecialchars($n['materie']) ?></td>
                    <td><?= $n['valoare'] ?></td>
                    <td><?= $n['data'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
