<?php
session_start();

// protection: only admin
if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
</head>
<body>

<h1>Bienvenue Administrateur 👨‍💼</h1>

<p>Gestion du système</p>

<ul>
    <li>Gérer étudiants</li>
    <li>Voir statistiques</li>
    <li>Paramètres</li>
</ul>

<a href="logout.php">Déconnexion</a>

</body>
</html>