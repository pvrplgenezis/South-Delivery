<?php
require_once __DIR__ . '/../inc/common.php';
require_auth('admin');
$q = trim((string)($_GET['q'] ?? ''));
$status = trim((string)($_GET['status'] ?? 'all'));
$sql = 'SELECT o.*, u.login AS user_login, u.email AS user_email, u.full_name AS user_name FROM orders o JOIN users u ON u.id=o.user_id WHERE 1=1';
$params=[];
if ($q !== '') { $sql .= ' AND (o.order_no LIKE ? OR u.login LIKE ? OR o.recipient_station LIKE ?)'; $like="%$q%"; array_push($params,$like,$like,$like); }
if ($status !== '' && $status !== 'all') { $sql .= ' AND o.status=?'; $params[]=$status; }
$sql .= ' ORDER BY o.id DESC';
$stmt = db()->prepare($sql); $stmt->execute($params);
json_out(['ok'=>true,'statuses'=>order_statuses(),'orders'=>$stmt->fetchAll()]);
