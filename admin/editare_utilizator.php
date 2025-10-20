<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: utilizatori.php');
    exit;
}

// Preluare utilizator
$stmt = $pdo->prepare("SELECT * FROM utilizatori WHERE id = ?");
$stmt->execute([$id]);
$utilizator = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$utilizator) {
    echo "Utilizatorul nu a fost găsit.";
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
        $sql = "UPDATE utilizatori SET username = :username, parola = :parola, rol = :rol, elev_id = :elev_id, profesor_id = :profesor_id WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                'username' => $username,
                'parola' => $parola,
                'rol' => $rol,
                'elev_id' => $rol === 'elev' ? $elev_id : null,
                'profesor_id' => $rol === 'profesor' ? $profesor_id : null,
                'id' => $id
            ]);
            $mesaj = "✅ Utilizator actualizat cu succes!";
        } catch (PDOException $e) {
            $mesaj = "❌ Eroare: " . $e->getMessage();
        }
    } else {
        $mesaj = "❌ Completează toate câmpurile obligatorii.";
    }
}

// Preia elevi și profesori pentru dropdown
$elevi = $pdo->query("SELECT id, nume, prenume FROM elev")->fetchAll(PDO::FETCH_ASSOC);
$profesori = $pdo->query("SELECT id, nume, prenume FROM profesor")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Editare utilizator</title>
    <script>
    function afiseazaCampuriRol() {
        const rol = document.getElementById('rol').value;
        document.getElementById('elev_select').style.display = rol === 'elev' ? 'block' : 'none';
        document.getElementById('profesor_select').style.display = rol === 'profesor' ? 'block' : 'none';
    }
    </script>
</head>
<body onload="afiseazaCampuriRol()">

<h2>Editare utilizator</h2>
<p><a href="utilizatori.php">← Înapoi la listă</a></p>

<?php if ($mesaj): ?>
    <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>;"><?php echo $mesaj; ?></p>
<?php endif; ?>

<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?= htmlspecialchars($utilizator['username']) ?>" required><br><br>

    <label>Parolă:</label><br>
    <input type="text" name="parola" value="<?= htmlspecialchars($utilizator['parola']) ?>" required><br><br>

    <label>Rol:</label><br>
    <select name="rol" id="rol" onchange="afiseazaCampuriRol()" required>
        <option value="elev" <?= $utilizator['rol'] === 'elev' ? 'selected' : '' ?>>Elev</option>
        <option value="profesor" <?= $utilizator['rol'] === 'profesor' ? 'selected' : '' ?>>Profesor</option>
        <option value="secretar" <?= $utilizator['rol'] === 'secretar' ? 'selected' : '' ?>>Admin</option>
    </select><br><br>

    <div id="elev_select" style="display:none;">
        <label>Elev asociat:</label><br>
        <select name="elev_id">
            <option value="">-- Alege elev --</option>
            <?php foreach ($elevi as $e): ?>
                <option value="<?= $e['id'] ?>" <?= $utilizator['elev_id'] == $e['id'] ? 'selected' : '' ?>>
                    <?= $e['nume'] . ' ' . $e['prenume'] ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
    </div>

    <div id="profesor_select" style="display:none;">
        <label>Profesor asociat:</label><br>
        <select name="profesor_id">
            <option value="">-- Alege profesor --</option>
            <?php foreach ($profesori as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $utilizator['profesor_id'] == $p['id'] ? 'selected' : '' ?>>
                    <?= $p['nume'] . ' ' . $p['prenume'] ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
    </div>

    <button type="submit">Salvează modificările</button>
</form>

</body>
</html>
