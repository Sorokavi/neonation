<?php
require_once __DIR__ . '/../session_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check_post()) {
	session_destroy();
}
header('Location: /');
exit;
