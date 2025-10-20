<?php
require_once '../session.php';
require_once '../index.php';

if ($_SESSION['rol'] !== 'secretar') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // opțional: șterge notele acelui elev mai întâi dacă există FK
        $pdo->prepare("DELETE FROM nota WHERE elev_id = ?")->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM elev WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: elevi.php');
        exit;
    } catch (PDOException $e) {
        echo "❌ Eroare la ștergere: " . $e->getMessage();
    }
} else {
    echo "ID elev lipsă sau invalid.";
}
