<?php
include("db.php");

$sql = "SELECT etudiants.nom, etudiants.email,
               files.filename, files.upload_date
        FROM etudiants
        LEFT JOIN files ON etudiants.id = files.user_id";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll();
?>

<table border="1">
<tr>
    <th>Nom</th>
    <th>Email</th>
    <th>Fichier</th>
    <th>Date</th>
</tr>

<?php foreach($data as $row){ ?>
<tr>
    <td><?= $row['nom'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['filename'] ?></td>
    <td><?= $row['upload_date'] ?></td>
</tr>
<?php } ?>

</table>

<br>

<a href="generate_pdf.php">Exporter en PDF</a>