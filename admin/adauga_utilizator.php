<?php
require_once '../session.php';
require_once '../index.php';

// Verifică dacă utilizatorul e secretar/admin
if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $parola = $_POST['parola'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $elev_id = isset($_POST['elev_id']) && $_POST['elev_id'] !== '' ? (int)$_POST['elev_id'] : null;
$profesor_id = isset($_POST['profesor_id']) && $_POST['profesor_id'] !== '' ? (int)$_POST['profesor_id'] : null;


    if ($username && $parola && $rol) {
        $sql = "INSERT INTO utilizatori (username, parola, rol, elev_id, profesor_id)
                VALUES (:username, :parola, :rol, :elev_id, :profesor_id)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                'username' => $username,
                'parola' => $parola,
                'rol' => $rol,
                'elev_id' => $rol === 'elev' ? $elev_id : null,
                'profesor_id' => $rol === 'profesor' ? $profesor_id : null
            ]);
            $mesaj = "✅ Utilizator adăugat cu succes!";
        } catch (PDOException $e) {
            $mesaj = "❌ Eroare: " . $e->getMessage();
        }
    } else {
        $mesaj = "❌ Completează toate câmpurile obligatorii.";
    }
}

// Preia elevi și profesori pentru dropdown-uri
$elevi = $pdo->query("SELECT id, nume, prenume FROM elev")->fetchAll(PDO::FETCH_ASSOC);
$profesori = $pdo->query("SELECT id, nume, prenume FROM profesor")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adaugă utilizator</title>
    <link rel="stylesheet" href="../assets/assets_style.css">

    <script>
    function afiseazaCampuriRol() {
        const rol = document.getElementById('rol').value;
        document.getElementById('elev_select').style.display = rol === 'elev' ? 'block' : 'none';
        document.getElementById('profesor_select').style.display = rol === 'profesor' ? 'block' : 'none';
    }
    </script>
</head>
<body onload="afiseazaCampuriRol()">

<h2>Adaugă utilizator nou</h2>
<p><a href="utilizatori.php">← Înapoi la listă</a></p>

<?php if ($mesaj): ?>
    <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>;"><?php echo $mesaj; ?></p>
<?php endif; ?>

<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Parolă:</label><br>
    <input type="text" name="parola" required><br><br>

    <label>Rol:</label><br>
    <select name="rol" id="rol" onchange="afiseazaCampuriRol()" required>
        <option value="">-- Alege rol --</option>
        <option value="elev">Elev</option>
        <option value="profesor">Profesor</option>
        <option value="secretar">Admin</option>
    </select><br><br>

    <div id="elev_select" style="display:none;">
        <label>Elev asociat:</label><br>
        <select name="elev_id">
            <option value="">-- Alege elev --</option>
            <?php foreach ($elevi as $e): ?>
                <option value="<?= $e['id'] ?>"><?= $e['nume'] . ' ' . $e['prenume'] ?></option>
            <?php endforeach; ?>
        </select><br><br>
    </div>

    <div id="profesor_select" style="display:none;">
        <label>Profesor asociat:</label><br>
        <select name="profesor_id">
            <option value="">-- Alege profesor --</option>
            <?php foreach ($profesori as $p): ?>
                <option value="<?= $p['id'] ?>"><?= $p['nume'] . ' ' . $p['prenume'] ?></option>
            <?php endforeach; ?>
        </select><br><br>
    </div>

    <button type="submit">Adaugă</button>
</form>

</body>
</html>
