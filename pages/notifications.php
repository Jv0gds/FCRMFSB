<?php
 session_start();
 include '../db.php';
 
 if (!isset($_SESSION['user_id'])) {
     header('Location: login.html');
     exit;
 }
 
 $user_id = $_SESSION['user_id'];
 
 // Fetch unread notifications
 $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
 $stmt->execute([$user_id]);
 $notifications = $stmt->fetchAll();
 
 // Mark notifications as read
 $update_stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
 $update_stmt->execute([$user_id]);
 
 ?>
 <!DOCTYPE html>
 <html lang="zh-CN">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>通知</title>
     <link rel="stylesheet" href="../style.css">
     <style>
         .notification {
             padding: 10px;
             margin-bottom: 10px;
             border-left: 5px solid #ccc;
         }
         .notification-unread {
             border-left-color: #3498db;
         }
         .notification p {
             margin: 0 0 5px;
         }
         .notification-time {
             font-size: 0.8em;
             color: #777;
         }
     </style>
 </head>
 <body>
     <div class="container">
         <h1>通知</h1>
         <div class="notification-list">
             <?php if (empty($notifications)): ?>
                 <p>没有新的通知。</p>
             <?php else: ?>
                 <?php foreach ($notifications as $notification): ?>
                     <div class="notification <?= $notification['is_read'] ? '' : 'notification-unread' ?>">
                         <p><?= htmlspecialchars($notification['message']) ?></p>
                         <span class="notification-time"><?= date('Y-m-d H:i', strtotime($notification['created_at'])) ?></span>
                         <?php if ($notification['type'] == 'cooperation_request'): ?>
                             <a href="pages/messages.php" class="btn btn-primary">查看详情</a>
                         <?php endif; ?>
                     </div>
                 <?php endforeach; ?>
             <?php endif; ?>
         </div>
         <a href="../index.php" class="btn btn-secondary">返回首页</a>
     </div>
 </body>
 </html>
