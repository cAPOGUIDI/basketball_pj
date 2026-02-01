<?php
/**
 * PAGE ADMIN - Voir tous les messages de contact
 * À protéger avec un système d'authentification admin en production
 */

require_once 'config/database.php';

$db = getDBConnection();

// Récupérer tous les messages
$stmt = $db->query("
    SELECT c.*, u.email as user_email
    FROM contacts c
    LEFT JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
");
$messages = $stmt->fetchAll();

// Stats
$statsStmt = $db->query("
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN statut = 'nouveau' THEN 1 END) as nouveaux,
        COUNT(CASE WHEN type_message = 'bug' THEN 1 END) as bugs,
        COUNT(CASE WHEN type_message = 'suggestion' THEN 1 END) as suggestions
    FROM contacts
");
$stats = $statsStmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Messages de Contact</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container { padding: 2rem; background: #f5f5f5; min-height: 100vh; }
        .admin-stats { display: flex; gap: 1.5rem; margin-bottom: 2rem; }
        .stat-box { flex: 1; background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .stat-box h3 { font-size: 0.9rem; color: #777; margin-bottom: 0.5rem; }
        .stat-box .number { font-size: 2.5rem; font-weight: 900; color: #FF6B35; }
        
        .messages-table { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #FF6B35; color: white; padding: 1rem; text-align: left; font-weight: 700; }
        td { padding: 1rem; border-bottom: 1px solid #eee; }
        tr:hover { background: #f9f9f9; }
        
        .badge-type { padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .badge-avis { background: #d4edda; color: #155724; }
        .badge-suggestion { background: #fff3cd; color: #856404; }
        .badge-bug { background: #f8d7da; color: #721c24; }
        .badge-question { background: #d1ecf1; color: #0c5460; }
        .badge-autre { background: #e2e3e5; color: #383d41; }
        
        .badge-statut { padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; }
        .statut-nouveau { background: #FF6B35; color: white; font-weight: 700; }
        .statut-lu { background: #ffc107; color: white; }
        .statut-traite { background: #28a745; color: white; }
        
        .message-preview { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1 style="margin-bottom: 2rem;">📬 Messages de Contact</h1>
        
        <!-- Statistiques -->
        <div class="admin-stats">
            <div class="stat-box">
                <h3>Total messages</h3>
                <div class="number"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-box">
                <h3>Nouveaux</h3>
                <div class="number" style="color: #dc3545;"><?php echo $stats['nouveaux']; ?></div>
            </div>
            <div class="stat-box">
                <h3>Bugs signalés</h3>
                <div class="number" style="color: #ffc107;"><?php echo $stats['bugs']; ?></div>
            </div>
            <div class="stat-box">
                <h3>Suggestions</h3>
                <div class="number" style="color: #28a745;"><?php echo $stats['suggestions']; ?></div>
            </div>
        </div>
        
        <!-- Table des messages -->
        <div class="messages-table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Sujet</th>
                        <th>Message</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($msg['nom']); ?></strong></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td>
                                <span class="badge-type badge-<?php echo $msg['type_message']; ?>">
                                    <?php echo ucfirst($msg['type_message']); ?>
                                </span>
                            </td>
                            <td><strong><?php echo htmlspecialchars($msg['sujet']); ?></strong></td>
                            <td class="message-preview" title="<?php echo htmlspecialchars($msg['message']); ?>">
                                <?php echo htmlspecialchars($msg['message']); ?>
                            </td>
                            <td>
                                <span class="badge-statut statut-<?php echo $msg['statut']; ?>">
                                    <?php echo ucfirst($msg['statut']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #999;">
                                Aucun message pour le moment
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 2rem; text-align: center;">
            <a href="index.php" class="btn btn-primary">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>