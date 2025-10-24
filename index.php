<?php
$host = 'localhost';
$db = 'studentmanagement';
$user = 'root';
$pass = 'Doarprinhar123!'; // parola NOUĂ

try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexiunea la baza de date a reușit!";
} catch (PDOException $e) {
    echo "❌ Eroare conexiune: " . $e->getMessage();
}
?>
