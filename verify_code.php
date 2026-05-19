<?php
include("db.php");

if (!isset($_POST['email']) || !isset($_POST['code'])) {
    echo "error";
    exit();
}

$email = trim($_POST['email']);
$code  = trim($_POST['code']);

try {

    // check user
    $sql = "SELECT * FROM etudiants WHERE email=? AND code_activation=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $code]);

    $user = $stmt->fetch();

    if ($user) {

        // activate account
        $update = "UPDATE etudiants SET status='active' WHERE email=?";
        $stmt2 = $pdo->prepare($update);
        $stmt2->execute([$email]);

        echo "success";   // IMPORTANT
    }
    else {
        echo "error";
    }

} catch (Exception $e) {
    echo "error";
}
?>