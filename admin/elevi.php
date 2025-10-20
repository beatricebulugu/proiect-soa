<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

// Preia clasele pentru filtrare
$clase = $pdo->query("SELECT id, nume FROM clasa")->fetchAll(PDO::FETCH_ASSOC);

// Filtrare
$nume = $_GET['nume'] ?? '';
$clasa_id = $_GET['clasa_id'] ?? '';
$ordonare = $_GET['sort'] ?? 'id_asc';

$conditii = [];
$parametri = [];

if ($nume) {
   $conditii[] = "(e.nume LIKE :nume OR e.prenume LIKE :nume)";
    $parametri[':nume'] = "%$nume%";
}
if ($clasa_id) {
    $conditii[] = "clasa_id = :clasa_id";
    $parametri[':clasa_id'] = $clasa_id;
}

// Ordonare
$ordineSQL = match($ordonare) {
    'nume_asc' => 'nume ASC',
    'nume_desc' => 'nume DESC',
    default => 'id ASC'
};

$sql = "SELECT e.*, c.nume AS nume_clasa
        FROM elev e
        LEFT JOIN clasa c ON e.clasa_id = c.id";
if ($conditii) {
    $sql .= " WHERE " . implode(" AND ", $conditii);
}
$sql .= " ORDER BY $ordineSQL";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametri);
$elevi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestiune elevi</title>
    <link rel="stylesheet" href="../assets/assets_style.css">

</head>
<body>
    <h2>Lista elevilor</h2>
   <div style="margin-bottom: 20px;">
    <p><a href="adauga_elev.php">➕ Adaugă elev nou</a></p>
    <p><a href="export_elevi_pdf.php" target="_blank">📄 Exportă lista elevilor în PDF</a></p>
    <p><a href="export_elevi_xls.php">📥 Exportă lista elevilor în Excel</a></p>
    <p><a href="import_elevi.php">📥 Importă elevi din Excel</a></p>
    <p><a href="import_elevi_pdf.php">📥 Importă elevi din PDF</a></p>


    <p><a href="grafic_note.php">📊 Vezi grafic note pe materii</a></p>
    <p><a href="utilizatori.php">← Înapoi la utilizatori</a></p>
</div>



    <form method="get">
        <input type="text" name="nume" placeholder="Caută după nume..." value="<?= htmlspecialchars($nume) ?>">
        <select name="clasa_id">
            <option value="">-- Toate clasele --</option>
            <?php foreach ($clase as $clasa): ?>
                <option value="<?= $clasa['id'] ?>" <?= $clasa['id'] == $clasa_id ? 'selected' : '' ?>>
                    <?= $clasa['nume'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="sort">
            <option value="id_asc" <?= $ordonare === 'id_asc' ? 'selected' : '' ?>>ID crescător</option>
            <option value="nume_asc" <?= $ordonare === 'nume_asc' ? 'selected' : '' ?>>Nume A-Z</option>
            <option value="nume_desc" <?= $ordonare === 'nume_desc' ? 'selected' : '' ?>>Nume Z-A</option>
        </select>
        <button type="submit">Filtrează</button>
    </form>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nume</th>
            <th>Prenume</th>
            <th>Clasa</th>
            <th>Telefon</th>
            <th>CNP</th>
            <th>Acțiuni</th>
        </tr>
        <?php foreach ($elevi as $e): ?>
            <tr>
                <td><?= $e['id'] ?></td>
                <td><?= htmlspecialchars($e['nume']) ?></td>
                <td><?= htmlspecialchars($e['prenume']) ?></td>
                <td><?= $e['nume_clasa'] ?></td>
                <td><?= $e['telefon'] ?></td>
                <td><?= $e['cnp'] ?></td>
                <td>
                    <a href="editare_elev.php?id=<?= $e['id'] ?>">Editează</a> |
                    <a href="sterge_elev.php?id=<?= $e['id'] ?>" onclick="return confirm('Ștergi elevul?')">Șterge</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
