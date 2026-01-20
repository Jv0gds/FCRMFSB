<?php
 session_start();
 include '../db.php';
 
 if (!isset($_SESSION['user_id'])) {
     die('请先登录');
 }
 
 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     $lead_id = $_POST['lead_id'];
     $owner_id = $_POST['owner_id'];
     $reason = $_POST['reason'];
     $user_id = $_SESSION['user_id'];
 
     // 将合作请求存入新的表 'cooperation_requests'
     $stmt = $pdo->prepare('INSERT INTO cooperation_requests (lead_id, user_id, owner_id, reason, status) VALUES (?, ?, ?, ?, ?)');
     $stmt->execute([$lead_id, $user_id, $owner_id, $reason, 'pending']);
 
     // 创建一个通知
     $message = "您有一个新的合作请求，请及时处理。";
     $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message, is_read, type, related_id) VALUES (?, ?, ?, ?, ?)');
     $stmt->execute([$owner_id, $message, 0, 'cooperation_request', $pdo->lastInsertId()]);
 
     // 创建一个站内信
     $conversation_stmt = $pdo->prepare('INSERT INTO conversations (subject, lead_id) VALUES (?,?)');
     $conversation_stmt->execute(['合作请求', $lead_id]);
     $conversation_id = $pdo->lastInsertId();
 
     $participant_stmt = $pdo->prepare('INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?,?)');
     $participant_stmt->execute([$conversation_id, $user_id]);
     $participant_stmt->execute([$conversation_id, $owner_id]);
 
     $message_stmt = $pdo->prepare('INSERT INTO messages (conversation_id, sender_id, content) VALUES (?, ?, ?)');
     $message_stmt->execute([$conversation_id, $user_id, $reason]);
 
     header('Location: ../public_detail_view.php?id=' . $lead_id . '&status=requested');
     exit;
 }
