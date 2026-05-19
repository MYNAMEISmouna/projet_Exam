<?php
require_once __DIR__ . '/vendor/autoload.php';
include("db.php");

$mpdf = new \Mpdf\Mpdf();

$sql = "SELECT 
            etudiants.nom,
            etudiants.email,
            files.filename,
            files.upload_date
        FROM etudiants
        LEFT JOIN files 
        ON etudiants.id = files.user_id";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll();

$html = "<h2>Liste des étudiants et fichiers</h2>";
$html .= "<table border='1' width='100%'>
<tr>
<th>Nom</th>
<th>Email</th>
<th>Fichier</th>
<th>Date</th>
</tr>";

foreach($data as $row){
    $html .= "<tr>
        <td>{$row['nom']}</td>
        <td>{$row['email']}</td>
        <td>{$row['filename']}</td>
        <td>{$row['upload_date']}</td>
    </tr>";
}

$html .= "</table>";

$mpdf->WriteHTML($html);
$mpdf->Output("rapport.pdf", "D");
?>