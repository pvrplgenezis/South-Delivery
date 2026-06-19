<?php
require_once __DIR__ . '/../inc/common.php';
$data = get_json_input();
$login = trim((string)($data['login'] ?? ''));
$pass = (string)($data['password'] ?? '');
if ($login === '' || $pass === '') json_out(['ok'=>false,'error'=>'Введите логин/email и пароль.'],422);
$stmt = db()->prepare('SELECT id, login, email, full_name, role, password_hash FROM users WHERE lower(login)=lower(?) OR lower(email)=lower(?) LIMIT 1');
$stmt->execute([$login, $login]);
$user = $stmt->fetch();
if (!$user || !password_verify($pass, $user['password_hash'])) json_out(['ok'=>false,'error'=>'Неверный логин/email или пароль.'],401);
$_SESSION['user_id'] = (int)$user['id'];
json_out(['ok'=>true,'user'=>current_user()]);
