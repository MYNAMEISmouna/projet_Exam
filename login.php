<?php
session_start();
include("db.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM etudiants WHERE email=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){

        $_SESSION['id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == "admin"){
            header("Location: admin_dashboard.php");
            exit();
        } else {
            header("Location: dashboard.php");
            exit();
        }

    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Espace Étudiant</title>
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
            position: relative;
        }

        /* Animated background bubbles */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        /* Form container */
        form {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding: 45px 40px;
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 480px;
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            z-index: 1;
        }

        form:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
        }

        /* Title */
        .form-title {
            text-align: center;
            margin-bottom: 35px;
        }

        .form-title h2 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 8px;
        }

        .form-title p {
            color: #64748b;
            font-size: 14px;
            font-weight: 400;
        }

        /* Input fields */
        .input-group {
            margin-bottom: 24px;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f8fafc;
            color: #1e293b;
            font-weight: 500;
            outline: none;
        }

        .input-group input:focus {
            border-color: #667eea;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .input-group input::placeholder {
            color: #94a3b8;
            font-weight: 400;
            font-size: 14px;
        }

        /* Button styling */
        button[type="submit"] {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 8px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
            background: linear-gradient(135deg, #5a67d8 0%, #6b46a0 100%);
        }

        button[type="submit"]:active {
            transform: translateY(1px);
        }

        /* Error message styling */
        .error-message {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
            padding: 14px 18px;
            border-radius: 16px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            border-left: 4px solid #dc2626;
            animation: shake 0.4s ease-in-out;
        }

        /* Links section */
        .links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
            margin: 0 10px;
        }

        .links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* Hide <br> tags */
        br {
            display: none;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* Responsive design */
        @media (max-width: 540px) {
            form {
                padding: 35px 25px;
            }
            
            .form-title h2 {
                font-size: 24px;
            }
            
            .input-group input {
                padding: 12px 16px;
                font-size: 14px;
            }
            
            button[type="submit"] {
                padding: 13px 20px;
                font-size: 15px;
            }
            
            .links a {
                font-size: 12px;
                margin: 0 6px;
            }
        }

        /* Optional: Add icon styling for inputs */
        .input-group input[type="email"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="%2394a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: 18px center;
            padding-left: 48px;
        }

        .input-group input[type="password"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="%2394a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>');
            background-repeat: no-repeat;
            background-position: 18px center;
            padding-left: 48px;
        }
    </style>
</head>
<body>

<form method="POST">
    <div class="form-title">
        <h2>🔐 Connexion</h2>
        <p>Accédez à votre espace étudiant</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="error-message">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="input-group">
        <label>📧 Adresse email</label>
        <input type="email" name="email" placeholder="exemple@universite.ma" required>
    </div>

    <div class="input-group">
        <label>🔒 Mot de passe</label>
        <input type="password" name="password" placeholder="Entrez votre mot de passe" required>
    </div>

    <button type="submit" name="login">🚀 Se connecter</button>

    <div class="links">
        <a href="forgot_password.php">Mot de passe oublié ?</a>
        <span style="color:#cbd5e1">|</span>
        <a href="register.php">Créer un compte</a>
    </div>
</form>

</body>
</html>