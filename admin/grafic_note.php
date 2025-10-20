<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

// Preluăm materiile
$stmt = $pdo->query("SELECT id, denumire FROM materie");
$materii = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preluăm clasele
$stmt = $pdo->query("SELECT id, nume FROM clasa");
$clase = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preluăm datele pentru grafic
$data_start = $_GET['start'] ?? '2024-01-01';
$data_end = $_GET['end'] ?? '2025-01-01';
$materie_id = $_GET['materie_id'] ?? null;
$clasa_id = $_GET['clasa_id'] ?? null;

// Filtru pe materie și clasă
$where = [];
$parametri = ['start' => $data_start, 'end' => $data_end];

if ($materie_id) {
    $where[] = "n.materie_id = :materie_id";
    $parametri['materie_id'] = $materie_id;
}

if ($clasa_id) {
    $where[] = "e.clasa_id = :clasa_id";
    $parametri['clasa_id'] = $clasa_id;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Preluare date pentru grafic
$sql = "SELECT m.denumire AS materie, ROUND(AVG(n.valoare), 2) AS media
        FROM nota n
        JOIN materie m ON n.materie_id = m.id
        JOIN elev e ON n.elev_id = e.id
        $whereClause
        AND n.data BETWEEN :start AND :end
        GROUP BY m.denumire
        ORDER BY m.denumire";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametri);
$rezultate = $stmt->fetchAll(PDO::FETCH_ASSOC);

$materii_grafic = [];
$medii = [];
foreach ($rezultate as $r) {
    $materii_grafic[] = $r['materie'];
    $medii[] = $r['media'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grafic note</title>
    <link rel="stylesheet" href="../assets/assets_style.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2>📊 Grafice note elevi</h2>
    <p><a href="elevi.php">← Înapoi la lista elevilor</a></p>

    <form method="get">
        <label>Perioadă:</label><br>
        <input type="date" name="start" value="<?= htmlspecialchars($data_start) ?>" required>
        <input type="date" name="end" value="<?= htmlspecialchars($data_end) ?>" required><br><br>

        <label>Materie:</label><br>
        <select name="materie_id">
            <option value="">-- Toate materiile --</option>
            <?php foreach ($materii as $m): ?>
                <option value="<?= $m['id'] ?>" <?= $m['id'] == $materie_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['denumire']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Clasa:</label><br>
        <select name="clasa_id">
            <option value="">-- Toate clasele --</option>
            <?php foreach ($clase as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $clasa_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nume']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Generează grafic</button>
    </form>

    <?php if (!empty($materii_grafic)): ?>
        <canvas id="graficNote" width="400 height="300"></canvas>
        

        <script>
            const ctx = document.getElementById('graficNote').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($materii_grafic) ?>,
                    datasets: [{
                        label: 'Media notelor',
                        data: <?= json_encode($medii) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 10
                        }
                    }
                }
            });
        </script>
    <?php else: ?>
        <p>Nu există date pentru această selecție.</p>
    <?php endif; ?>
</body>
</html>
