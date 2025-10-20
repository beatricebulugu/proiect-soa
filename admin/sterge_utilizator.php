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
        $stmt = $pdo->prepare("DELETE FROM utilizatori WHERE id = :id");
        $stmt->execute(['id' => $id]);

        header('Location: utilizatori.php');
        exit;
    } catch (PDOException $e) {
        echo "❌ Eroare la ștergere: " . $e->getMessage();
    }
} else {
    echo "ID lipsă sau invalid.";
}
