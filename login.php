<?php
session_start();
require_once 'index.php'; // conexiunea PDO

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $parola = $_POST['parola'] ?? '';

    $sql = "SELECT * FROM utilizatori WHERE username = :username AND parola = :parola";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username, 'parola' => $parola]);
    $utilizator = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilizator) {
        $_SESSION['utilizator_id'] = $utilizator['id'];
        $_SESSION['rol'] = $utilizator['rol'];
        $_SESSION['username'] = $utilizator['username'];

        // Redirecționare în funcție de rol
        if ($utilizator['rol'] === 'elev') {
            header('Location: elev/note_mele.php');
        } elseif ($utilizator['rol'] === 'profesor') {
            header('Location: profesor/clase_mele.php');
        } elseif ($utilizator['rol'] === 'secretar') {
            header('Location: admin/utilizatori.php');
        }
        exit;
    } else {
        $mesaj = 'Nume utilizator sau parolă greșite!';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Autentificare</title>
</head>
<body>
    <h2>Autentificare</h2>
    <?php if ($mesaj): ?>
        <p style="color: red;"><?php echo $mesaj; ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Parola:</label><br>
        <input type="password" name="parola" required><br><br>
        <button type="submit">Autentificare</button>
    </form>
</body>
</html>
