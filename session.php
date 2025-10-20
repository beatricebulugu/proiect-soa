<?php
session_start();

if (!isset($_SESSION['utilizator_id'])) {
    header('Location: ../login.php');
    exit;
}
