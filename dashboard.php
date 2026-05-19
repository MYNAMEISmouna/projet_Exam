<?php
session_start();
include("db.php");

// protection: only logged users
if(!isset($_SESSION['id']) || $_SESSION['role'] != 'etudiant'){
    header("Location: login.php");
    exit();
}

// get student files
$sql = "SELECT * FROM files WHERE user_id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['id']]);
$files = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiant</title>
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
            padding: 40px 20px;
        }

        /* Main container */
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
        }

        /* Header section */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 40px;
            color: white;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Content section */
        .content {
            padding: 40px;
        }

        /* Welcome card */
        .welcome-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .welcome-card p {
            font-size: 18px;
            color: #334155;
            font-weight: 500;
        }

        /* Action buttons */
        .actions {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 28px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        }

        /* Section title */
        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #e2e8f0;
        }

        /* Table styling */
        .files-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .files-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .files-table td {
            padding: 15px 20px;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        .files-table tr:last-child td {
            border-bottom: none;
        }

        .files-table tr:hover {
            background: #f8fafc;
            transition: background 0.3s ease;
        }

        /* File icon styling */
        .file-icon {
            font-size: 20px;
            margin-right: 10px;
        }

        .filename {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #f8fafc;
            border-radius: 16px;
        }

        .empty-state .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state p {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e2e8f0;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        /* Logout link */
        .logout-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #f56565;
            text-decoration: none;
            font-weight: 600;
            margin-top: 30px;
            transition: color 0.3s ease;
        }

        .logout-link:hover {
            color: #e53e3e;
            text-decoration: underline;
        }

        /* Hide old <br> tags */
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

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                border-radius: 20px;
            }
            
            .header {
                padding: 20px 25px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 25px;
            }
            
            .welcome-card {
                padding: 18px 22px;
            }
            
            .welcome-card p {
                font-size: 16px;
            }
            
            .section-title {
                font-size: 20px;
            }
            
            .files-table th,
            .files-table td {
                padding: 12px 15px;
                font-size: 14px;
            }
            
            .actions {
                gap: 15px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .files-table {
                display: block;
                overflow-x: auto;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="header">
        <h1>
            <span>👨‍🎓</span> 
            Dashboard Étudiant
        </h1>
        <p>Gérez vos documents et suivez vos uploads</p>
    </div>
    
    <div class="content">
        <div class="welcome-card">
            <p>✨ Bienvenue dans votre espace personnel !</p>
        </div>
        
        <div class="actions">
            <a href="upload.php" class="btn btn-primary">
                ➕ Uploader un fichier
            </a>
        </div>
        
        <div class="section-title">
            <span>📁</span>
            <span>Mes fichiers</span>
            <span class="badge"><?= count($files) ?> fichier(s)</span>
        </div>
        
        <?php if(count($files) > 0): ?>
            <table class="files-table">
                <thead>
                    <tr>
                        <th>Nom du fichier</th>
                        <th>Date d'upload</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($files as $file): ?>
                        <tr>
                            <td>
                                <div class="filename">
                                    <span class="file-icon">
                                        <?php 
                                            $ext = pathinfo($file['filename'], PATHINFO_EXTENSION);
                                            if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) echo "🖼️";
                                            elseif($ext == 'pdf') echo "📄";
                                            elseif(in_array($ext, ['doc', 'docx'])) echo "📝";
                                            elseif(in_array($ext, ['xls', 'xlsx'])) echo "📊";
                                            else echo "📎";
                                        ?>
                                    </span>
                                    <?= htmlspecialchars($file['filename']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($file['upload_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>Aucun fichier uploadé pour le moment</p>
                <a href="upload.php" class="btn btn-primary" style="display: inline-flex;">
                    ➕ Uploader mon premier fichier
                </a>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 40px; text-align: center;">
            <a href="logout.php" class="logout-link">
                🔓 Déconnexion
            </a>
        </div>
    </div>
</div>

</body>
</html>