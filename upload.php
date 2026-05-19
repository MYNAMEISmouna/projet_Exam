<?php
session_start();
include("db.php");

// protection login
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['upload'])){

    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

    $target = "uploads/" . $file;

    if(move_uploaded_file($tmp, $target)){

        $sql = "INSERT INTO files (user_id, filename, upload_date)
                VALUES (?, ?, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['id'], $file]);

        echo "✔ Upload réussi";
    }
    else{
        echo "❌ Échec de l'upload";
    }
}
?>

<form method="POST" enctype="multipart/form-data">

    <input type="file" name="file" required>
    <br><br>

    <button type="submit" name="upload">Envoyer</button>

</form>