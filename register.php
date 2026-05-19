<?php
include("db.php");

require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';
require 'lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['submit'])){

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $apogee = $_POST['apogee'];
    $email = $_POST['email'];

    // code 8 chiffres
    $code = random_int(10000000, 99999999);

    // insert DB
    $sql = "INSERT INTO etudiants(nom, prenom, apogee, email, code_activation, status)
            VALUES(?,?,?,?,?,?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom,$prenom,$apogee,$email,$code,'inactive']);

    // EMAIL
    $mail = new PHPMailer(true);

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'maimouna.elhamdaoui2003@gmail.com';
    $mail->Password = 'ddtp ztlz wuxz oqjy';

    // 🔥 IMPORTANT (manquant chez toi)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('maimouna.elhamdaoui2003@gmail.com', 'Administration');
    $mail->addAddress($email, $prenom);

    $mail->Subject = "Code d'activation";
    $mail->Body = "Bonjour $prenom,\nVotre code est : $code";

    $mail->send();
header("Location: activate.php");
exit();

} catch (Exception $e) {
    echo "Inscription OK mais email non envoyé: {$mail->ErrorInfo}";
}
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="stylee.css">
    <title>Inscription</title>
</head>
<body>

<h2 style="text-align:center;">Inscription Étudiant</h2>

<form method="POST">

    <input type="text" name="nom" placeholder="Nom" required><br><br>
    <input type="text" name="prenom" placeholder="Prénom" required><br><br>
    <input type="text" name="apogee" placeholder="Apogée" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>

    <button type="submit" name="submit">S'inscrire</button>

</form>

</body>
</html>