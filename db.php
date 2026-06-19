<?php
require_once __DIR__ . '/../inc/common.php';
$u = current_user();
json_out(['ok'=>true,'user'=>$u]);
