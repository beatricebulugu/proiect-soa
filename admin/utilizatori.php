<?php
require_once '..\session.php';
require_once '..\index.php';

// verifică dacă e admin
if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

// preia utilizatorii
$stmt = $pdo->query("SELECT * FROM utilizatori ORDER BY rol, username");
$utilizatori = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Administrare utilizatori</title>
</head>
<body>
    <h2>Lista utilizatorilor</h2>
    <p>Bun venit, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="../logout.php">Logout</a></p>
    <p><a href="elevi.php">📚 Vezi lista elevilor</a></p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Rol</th>
            <th>Elev ID</th>
            <th>Profesor ID</th>
            <th>Acțiuni</th>
        </tr>
        <?php foreach ($utilizatori as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td><?php echo $u['rol']; ?></td>
                <td><?php echo $u['elev_id'] ?? '-'; ?></td>
                <td><?php echo $u['profesor_id'] ?? '-'; ?></td>
                <td>
                    <a href="editare_utilizator.php?id=<?php echo $u['id']; ?>">Editează</a> |
                    <a href="sterge_utilizator.php?id=<?php echo $u['id']; ?>" onclick="return confirm('Ești sigur că vrei să ștergi acest utilizator?')">Șterge</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a href="adauga_utilizator.php">Adaugă utilizator nou</a>
</body>
</html>
