<?php
include("db.php");

if(isset($_POST['save'])){

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // check email
    $check = $pdo->prepare("SELECT * FROM etudiants WHERE email=?");
    $check->execute([$email]);

    if($check->rowCount() > 0){

        // update password
        $sql = "UPDATE etudiants SET password=? WHERE email=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$password, $email]);

        header("Location: login.php");
        exit();
    }
    else{
        echo "Email introuvable";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <style>
        /* Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Form container */
        form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px 35px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 480px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 0.5s ease-out;
        }

        form:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        /* Title inside form (optional) */
        form::before {
            content: "🔐 Réinitialisation";
            display: block;
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: -0.5px;
        }

        /* Input fields */
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e4e8;
            border-radius: 14px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f8fafc;
            color: #1e293b;
            font-weight: 500;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #667eea;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #94a3b8;
            font-weight: 400;
            font-size: 14px;
        }

        /* Spacing between inputs */
        input[type="email"] {
            margin-bottom: 20px;
        }

        input[type="password"] {
            margin-bottom: 28px;
        }

        /* Button styling */
        button[type="submit"] {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #5a67d8 0%, #6b46a0 100%);
        }

        button[type="submit"]:active {
            transform: translateY(1px);
        }

        /* Error message styling */
        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            border-left: 4px solid #dc2626;
            animation: shake 0.3s ease-in-out;
        }

        /* Hide <br> tags (using CSS margin instead) */
        br {
            display: none;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Responsive */
        @media (max-width: 540px) {
            form {
                padding: 30px 22px;
            }
            
            form::before {
                font-size: 20px;
                margin-bottom: 25px;
            }
            
            input[type="email"],
            input[type="password"] {
                padding: 12px 16px;
                font-size: 14px;
            }
            
            button[type="submit"] {
                padding: 12px 20px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>

<?php if(isset($error)): ?>
    <div class="error-message"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" placeholder="📧 Email académique" required>
    <input type="password" name="password" placeholder="🔒 Nouveau mot de passe" required>
    <button type="submit" name="save">💾 Enregistrer</button>
</form>

</body>
</html>