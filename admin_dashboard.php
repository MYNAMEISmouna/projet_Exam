<?php
session_start();
include("db.php");

// protection: only admin
if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Get statistics for dashboard
$stats = [];

// Total students
$stmt = $pdo->query("SELECT COUNT(*) as total FROM etudiants WHERE role = 'etudiant' OR role IS NULL");
$stats['students'] = $stmt->fetch()['total'];

// Total files
$stmt = $pdo->query("SELECT COUNT(*) as total FROM files");
$stats['files'] = $stmt->fetch()['total'];

// Active students
$stmt = $pdo->query("SELECT COUNT(*) as total FROM etudiants WHERE est_active = 1 OR status = 'active'");
$stats['active'] = $stmt->fetch()['total'];

// Pending students (inactive)
$stmt = $pdo->query("SELECT COUNT(*) as total FROM etudiants WHERE est_active = 0 OR status = 'inactive'");
$stats['pending'] = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <a href="generate_pdf.php" class="btn btn-primary">
    📄 Générer rapport PDF
</a>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        /* Main container */
        .admin-dashboard {
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Header Section */
        .header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 30px 35px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            animation: fadeInDown 0.5s ease-out;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-title h1 {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title p {
            color: #64748b;
            font-size: 14px;
            margin-top: 8px;
        }

        .logout-btn {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(229, 62, 62, 0.4);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
            animation: fadeInUp 0.5s ease-out;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .stat-info h3 {
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .stat-icon {
            font-size: 48px;
            opacity: 0.7;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            padding: 30px 35px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            animation: fadeInUp 0.5s ease-out 0.1s both;
        }

        .welcome-banner h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .welcome-banner p {
            opacity: 0.9;
            font-size: 14px;
        }

        /* Menu Grid */
        .menu-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.5s ease-out 0.2s both;
        }

        .menu-section h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 3px solid #e2e8f0;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .menu-card {
            background: #f8fafc;
            border-radius: 20px;
            padding: 28px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid #e2e8f0;
            display: block;
            text-align: center;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            border-color: #667eea;
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.15);
            background: white;
        }

        .menu-icon {
            font-size: 52px;
            margin-bottom: 18px;
        }

        .menu-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .menu-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }

        /* Recent Activity Section */
        .recent-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.5s ease-out 0.3s both;
        }

        .recent-section h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-list {
            list-style: none;
        }

        .activity-list li {
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #475569;
            font-size: 14px;
        }

        .activity-list li:last-child {
            border-bottom: none;
        }

        .activity-icon {
            font-size: 24px;
        }

        .activity-text {
            flex: 1;
        }

        .activity-time {
            color: #94a3b8;
            font-size: 12px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 25px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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
            .header {
                padding: 20px 25px;
            }
            
            .header-title h1 {
                font-size: 22px;
            }
            
            .stat-number {
                font-size: 32px;
            }
            
            .stat-icon {
                font-size: 38px;
            }
            
            .welcome-banner h2 {
                font-size: 20px;
            }
            
            .menu-section {
                padding: 25px;
            }
            
            .menu-icon {
                font-size: 42px;
            }
            
            .recent-section {
                padding: 25px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="admin-dashboard">
    <div class="header">
        <div class="header-content">
            <div class="header-title">
                <h1>
                    <span>👨‍💼</span>
                    Administration
                </h1>
                <p>Plateforme de gestion des étudiants et documents</p>
            </div>
            <a href="logout.php" class="logout-btn">
                🔓 Déconnexion
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>👨‍🎓 Étudiants inscrits</h3>
                <div class="stat-number"><?= $stats['students'] ?? 0 ?></div>
            </div>
            <div class="stat-icon">📊</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>📁 Fichiers déposés</h3>
                <div class="stat-number"><?= $stats['files'] ?? 0 ?></div>
            </div>
            <div class="stat-icon">📎</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>✅ Comptes actifs</h3>
                <div class="stat-number"><?= $stats['active'] ?? 0 ?></div>
            </div>
            <div class="stat-icon">🔓</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>⏳ En attente</h3>
                <div class="stat-number"><?= $stats['pending'] ?? 0 ?></div>
            </div>
            <div class="stat-icon">⏰</div>
        </div>
    </div>

    <div class="welcome-banner">
        <h2>👋 Bonjour, Administrateur <?= htmlspecialchars($_SESSION['nom'] ?? '') ?>!</h2>
        <p>Bienvenue sur votre espace d'administration. Gérez les étudiants, consultez les statistiques et administrez la plateforme.</p>
    </div>

    <div class="menu-section">
        <h2>
            <span>⚙️</span>
            Menu d'administration
        </h2>
        <div class="menu-grid">
            <a href="admin_students_list.php" class="menu-card">
                <div class="menu-icon">📋</div>
                <div class="menu-title">Gérer les étudiants</div>
                <div class="menu-desc">Consulter la liste complète des étudiants, voir leurs fichiers et exporter en PDF</div>
            </a>
            <a href="statistiques.php" class="menu-card">
                <div class="menu-icon">📊</div>
                <div class="menu-title">Statistiques</div>
                <div class="menu-desc">Visualiser les graphiques et rapports détaillés de la plateforme</div>
            </a>
            <a href="parametres.php" class="menu-card">
                <div class="menu-icon">⚙️</div>
                <div class="menu-title">Paramètres</div>
                <div class="menu-desc">Configurer les options de la plateforme et les préférences</div>
            </a>
            <a href="gestion_fichiers.php" class="menu-card">
                <div class="menu-icon">🗂️</div>
                <div class="menu-title">Gestion des fichiers</div>
                <div class="menu-desc">Administrer tous les fichiers déposés par les étudiants</div>
            </a>
        </div>
    </div>

    <div class="recent-section">
        <h2>
            <span>🕐</span>
            Activités récentes
        </h2>
        <ul class="activity-list">
            <li>
                <span class="activity-icon">📝</span>
                <span class="activity-text">Nouvel étudiant inscrit</span>
                <span class="activity-time">Aujourd'hui</span>
            </li>
            <li>
                <span class="activity-icon">📎</span>
                <span class="activity-text">Fichier uploadé - Rapport PDF</span>
                <span class="activity-time">Hier</span>
            </li>
            <li>
                <span class="activity-icon">✅</span>
                <span class="activity-text">Compte activé - email@exemple.com</span>
                <span class="activity-time">Il y a 2 jours</span>
            </li>
            <li>
                <span class="activity-icon">📊</span>
                <span class="activity-text">Rapport PDF généré par l'admin</span>
                <span class="activity-time">Il y a 3 jours</span>
            </li>
        </ul>
    </div>

    <div class="footer">
        © <?= date('Y') ?> Plateforme Étudiante - Administration. Tous droits réservés.
    </div>
</div>
</body>
</html>