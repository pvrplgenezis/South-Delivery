<?php
require_once __DIR__ . '/../inc/common.php';
$data = get_json_input();
$login = trim((string)($data['login'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$full = trim((string)($data['name'] ?? ''));
$pass = (string)($data['password'] ?? '');
if ($login === '' || $email === '' || $pass === '') json_out(['ok'=>false,'error'=>'Заполните логин, email и пароль.'],422);
if (!preg_match('/^[a-zA-Z0-9_\-]{3,30}$/', $login)) json_out(['ok'=>false,'error'=>'Логин: 3–30 символов (латиница, цифры, _, -).'],422);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_out(['ok'=>false,'error'=>'Некорректный email.'],422);
if (strlen($pass) < 6) json_out(['ok'=>false,'error'=>'Пароль должен быть не короче 6 символов.'],422);
if ($full === '') $full = $login;
try {
  $hash = password_hash($pass, PASSWORD_DEFAULT);
  db()->prepare('INSERT INTO users (login, email, full_name, password_hash, role) VALUES (?,?,?,?,?)')->execute([$login,$email,$full,$hash,'user']);
  $_SESSION['user_id'] = (int)db()->lastInsertId();
  json_out(['ok'=>true,'user'=>current_user()]);
} catch (PDOException $e) {
  $msg = $e->getMessage();
  if (str_contains($msg, 'UNIQUE')) json_out(['ok'=>false,'error'=>'Логин или email уже заняты.'],409);
  json_out(['ok'=>false,'error'=>'Ошибка сервера: '.$msg],500);
}
