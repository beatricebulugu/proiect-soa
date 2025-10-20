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

// Preluăm mediile pe materii pentru elev
$sql = "SELECT m.denumire AS materie, ROUND(AVG(n.valoare), 2) AS media
        FROM nota n
        JOIN materie m ON n.materie_id = m.id
        WHERE n.elev_id = :elev_id
        GROUP BY m.denumire
        ORDER BY m.denumire";

$stmt = $pdo->prepare($sql);
$stmt->execute(['elev_id' => $elev_id]);
$rezultate = $stmt->fetchAll(PDO::FETCH_ASSOC);

$materii = [];
$medii = [];
foreach ($rezultate as $r) {
    $materii[] = $r['materie'];
    $medii[] = $r['media'];
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/assets_style.css">

    <title>Raport note</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2>📊 Evoluția mediilor pe materii</h2>
    <p><a href="note_mele.php">← Înapoi la note</a></p>

    <?php if (empty($materii)): ?>
        <p>Nu există note pentru a genera graficul.</p>
    <?php else: ?>
        <canvas id="graficNote" width="600" height="400"></canvas>
        <script>
            const ctx = document.getElementById('graficNote').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($materii) ?>,
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
    <?php endif; ?>
</body>
</html>
