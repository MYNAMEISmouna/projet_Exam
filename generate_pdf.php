<?php
session_start();
include("db.php");

// Only admin can access
if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Try multiple possible paths for TCPDF
$tcpdf_paths = [
    'lib/tcpdf/tcpdf.php',
    'lib/TCPDF/tcpdf.php',
    'lib/tcpdf/TCPDF/tcpdf.php',
    'tcpdf/tcpdf.php',
    'lib/tcpdf-6.7.5/tcpdf.php',
    'lib/tcpdf-6.7.5/tcpdf/tcpdf.php'
];

$loaded = false;
foreach($tcpdf_paths as $path) {
    if(file_exists($path)) {
        require_once($path);
        $loaded = true;
        break;
    }
}

if(!$loaded) {
    die("TCPDF not found! Please check that TCPDF is installed in lib/tcpdf/ folder.");
}

// ==============================================
// MODIFIED QUERY - WITHOUT date_inscription
// ==============================================

// First, check what columns exist in your etudiants table
// Uncomment this to debug:
/*
$check = $pdo->query("SHOW COLUMNS FROM etudiants");
echo "<pre>";
print_r($check->fetchAll());
echo "</pre>";
die();
*/

// Modified query - removed date_inscription
$sql = "SELECT e.id, e.nom, e.prenom, e.apogee, e.email,
               f.id as file_id, f.filename, f.upload_date
        FROM etudiants e
        LEFT JOIN files f ON e.id = f.user_id
        ORDER BY e.id, f.upload_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize data
$students = [];
foreach($results as $row){
    $student_id = $row['id'];
    if(!isset($students[$student_id])){
        $students[$student_id] = [
            'id' => $row['id'],
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'apogee' => $row['apogee'],
            'email' => $row['email'],
            // 'date_inscription' => $row['date_inscription'] ?? date('Y-m-d'), // Use current date as fallback
            'files' => []
        ];
    }
    if($row['file_id']){
        $students[$student_id]['files'][] = [
            'id' => $row['file_id'],
            'filename' => $row['filename'],
            'upload_date' => $row['upload_date']
        ];
    }
}

// Handle PDF export
if(isset($_GET['export_pdf'])){
    // Create new PDF document (L = Landscape)
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Administration');
    $pdf->SetAuthor('Plateforme Gestion');
    $pdf->SetTitle('Rapport Étudiants');
    $pdf->SetSubject('Liste des étudiants et leurs fichiers');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 18);
    
    // Title
    $pdf->Cell(0, 10, 'RAPPORT RECAPITULATIF DES ETUDIANTS', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Genere le ' . date('d/m/Y a H:i'), 0, 1, 'C');
    $pdf->Ln(8);
    
    // Statistics
    $totalStudents = count($students);
    $totalFiles = array_sum(array_map(function($s) { return count($s['files']); }, $students));
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 8, 'Statistiques:', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 7, '- Total etudiants: ' . $totalStudents, 0, 1);
    $pdf->Cell(0, 7, '- Total fichiers: ' . $totalFiles, 0, 1);
    $pdf->Ln(8);
    
    // Loop through students
    foreach($students as $student){
        $fullname = strtoupper($student['prenom'] . ' ' . $student['nom']);
        
        // Student header with background
        $pdf->SetFillColor(102, 126, 234);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, ' ETUDIANT: ' . $fullname . ' (ID: ' . $student['id'] . ')', 0, 1, 'L', true);
        
        // Student info
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(248, 250, 252);
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Cell(35, 7, ' Email:', 0, 0, 'L', true);
        $pdf->Cell(0, 7, ' ' . $student['email'], 0, 1, 'L', true);
        
        $pdf->Cell(35, 7, ' Apogee:', 0, 0, 'L', true);
        $pdf->Cell(0, 7, ' ' . $student['apogee'], 0, 1, 'L', true);
        
        // Date inscription commented out if column doesn't exist
        // $pdf->Cell(35, 7, ' Inscription:', 0, 0, 'L', true);
        // $pdf->Cell(0, 7, ' ' . $student['date_inscription'], 0, 1, 'L', true);
        
        $pdf->Ln(5);
        
        // Files section
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 8, 'FICHIERS DEPOSES (' . count($student['files']) . ')', 0, 1);
        
        if(count($student['files']) > 0){
            // Table header
            $pdf->SetFillColor(200, 200, 200);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(120, 7, 'Nom du fichier', 1, 0, 'L', true);
            $pdf->Cell(80, 7, "Date d'upload", 1, 1, 'L', true);
            
            // Table rows
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(255, 255, 255);
            foreach($student['files'] as $file){
                $filename = strlen($file['filename']) > 50 ? substr($file['filename'], 0, 47) . '...' : $file['filename'];
                $pdf->Cell(120, 6, ' - ' . $filename, 1, 0, 'L');
                $pdf->Cell(80, 6, date('d/m/Y H:i', strtotime($file['upload_date'])), 1, 1, 'L');
            }
        } else {
            $pdf->SetFont('helvetica', 'I', 9);
            $pdf->Cell(0, 6, ' Aucun fichier depose', 0, 1, 'L');
        }
        
        $pdf->Ln(8);
        
        // Add page break if needed
        if($pdf->GetY() > 250){
            $pdf->AddPage();
        }
    }
    
    // Output PDF
    $pdf->Output('rapport_etudiants_' . date('Y-m-d') . '.pdf', 'D');
    exit();
}
?>

<!-- Rest of your HTML code remains the same -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Générer Rapport PDF - Administration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .pdf-container {
            max-width: 800px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
            text-align: center;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            color: white;
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            justify-content: center;
        }
        
        .stat-card {
            background: #f8fafc;
            padding: 20px 30px;
            border-radius: 16px;
            flex: 1;
            border-left: 4px solid #667eea;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 14px;
            color: #64748b;
            margin-top: 5px;
        }
        
        .btn-pdf {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #dc2626;
            color: white;
            padding: 16px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            margin-bottom: 30px;
        }
        
        .btn-pdf:hover {
            background: #b91c1c;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #64748b;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #475569;
            transform: translateY(-2px);
        }
        
        .info-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-top: 30px;
            text-align: left;
        }
        
        .info-box h3 {
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-box ul {
            list-style: none;
            padding-left: 0;
        }
        
        .info-box li {
            padding: 8px 0;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-box li:last-child {
            border-bottom: none;
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
        
        @media (max-width: 640px) {
            .header h1 { font-size: 24px; }
            .stats { flex-direction: column; }
            .btn-pdf { font-size: 16px; padding: 14px 28px; }
        }
    </style>
</head>
<body>

<div class="pdf-container">
    <div class="header">
        <h1>📄 Générateur de Rapport PDF</h1>
        <p>Exportez la liste complète des étudiants et leurs fichiers</p>
    </div>
    
    <div class="content">
        <?php 
        $totalStudents = count($students);
        $totalFiles = array_sum(array_map(function($s) { return count($s['files']); }, $students));
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $totalStudents ?></div>
                <div class="stat-label">📚 Étudiants inscrits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalFiles ?></div>
                <div class="stat-label">📎 Fichiers déposés</div>
            </div>
        </div>
        
        <a href="?export_pdf=1" class="btn-pdf">
            📄 Générer et télécharger le PDF
        </a>
        
        <div class="info-box">
            <h3>
                <span>📋</span>
                Contenu du rapport :
            </h3>
            <ul>
                <li>👨‍🎓 Liste complète des étudiants (Nom, Prénom, Apogée, Email)</li>
                <li>📁 Liste des fichiers déposés par chaque étudiant</li>
                <li>📅 Date d'inscription et dates d'upload</li>
                <li>📊 Statistiques globales</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="admin_dashboard.php" class="btn-back">
                ← Retour au tableau de bord
            </a>
        </div>
    </div>
</div>

</body>
</html>