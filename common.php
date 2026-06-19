<?php
require_once __DIR__ . '/../inc/common.php';
$u = require_auth('user');
$d = get_json_input();
$required = ['sender_name','sender_phone','sender_city','recipient_name','recipient_station','cargo_type','weight_kg','size_cm','declared_value','delivery_type'];
foreach ($required as $f) { if (trim((string)($d[$f] ?? '')) === '') json_out(['ok'=>false,'error'=>'Не заполнено поле: '.$f],422); }
$weight = (float)$d['weight_kg']; if ($weight <= 0) json_out(['ok'=>false,'error'=>'Некорректный вес.'],422);
$deliveryType = (string)$d['delivery_type']; $total = isset($d['estimated_price']) ? (float)$d['estimated_price'] : null;
for ($tries=0; $tries<5; $tries++) {
  $orderNo = 'SD-' . date('Y') . '-' . str_pad((string)random_int(1, 999999), 6, '0', STR_PAD_LEFT);
  try {
    $sql='INSERT INTO orders (order_no,user_id,sender_name,sender_phone,sender_city,recipient_name,recipient_station,cargo_type,weight_kg,size_cm,declared_value,delivery_type,estimated_price,status,comment_admin) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    db()->prepare($sql)->execute([$orderNo,$u['id'],$d['sender_name'],$d['sender_phone'],$d['sender_city'],$d['recipient_name'],$d['recipient_station'],$d['cargo_type'],$weight,$d['size_cm'],$d['declared_value'],normalize_delivery_type($deliveryType),$total,'Принят','']);
    json_out(['ok'=>true,'order_id'=>(int)db()->lastInsertId(),'order_no'=>$orderNo]);
  } catch (PDOException $e) {
    if (str_contains($e->getMessage(),'UNIQUE')) continue;
    json_out(['ok'=>false,'error'=>'Ошибка сервера: '.$e->getMessage()],500);
  }
}
json_out(['ok'=>false,'error'=>'Не удалось создать номер заказа, попробуйте ещё раз.'],500);
