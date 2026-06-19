<?php
require_once __DIR__ . '/../inc/common.php';
$u = require_auth('user');
$stmt = db()->prepare('SELECT id, order_no, recipient_station, delivery_type, status, estimated_price, comment_admin, created_at, updated_at FROM orders WHERE user_id=? ORDER BY id DESC');
$stmt->execute([$u['id']]);
json_out(['ok'=>true,'orders'=>$stmt->fetchAll()]);
