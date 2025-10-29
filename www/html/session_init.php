<?php
// Secure defaults and error-disclosure controls for all pages that include this file.

// Detect HTTPS (including when behind a proxy/load balancer)
$isHttps = (
    (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
);

// Error disclosure: default to production-safe unless APP_DEBUG is truthy
$appDebug = getenv('APP_DEBUG');
$debugEnabled = false;
if ($appDebug !== false) {
    $val = strtolower((string)$appDebug);
    $debugEnabled = in_array($val, ['1','true','yes','on'], true);
}

if ($debugEnabled) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}

set_exception_handler(function ($ex) use ($debugEnabled) {
    error_log('[Unhandled Exception] ' . get_class($ex) . ': ' . $ex->getMessage() . " in {$ex->getFile()}:{$ex->getLine()}\n" . $ex->getTraceAsString());
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }
    echo $debugEnabled ? '<pre>' . htmlspecialchars((string)$ex, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>'
                       : '<h1>Unexpected error</h1><p>Something went wrong. Please try again later.</p>';
    exit;
});

// Harden PHP session behavior before starting the session
@ini_set('session.use_strict_mode', '1');
@ini_set('session.use_only_cookies', '1');
@ini_set('session.cookie_httponly', '1');
@ini_set('session.cookie_secure', $isHttps ? '1' : '0');
// samesite via ini may not be supported on older PHP; cookie params below also set it.

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Strict',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Prevent session fixation & hijacking
// 1) Periodically regenerate session ID
if (!isset($_SESSION['regenerated_at']) || (time() - (int)$_SESSION['regenerated_at']) > 300) {
    session_regenerate_id(true);
    $_SESSION['regenerated_at'] = time();
}

// 2) Lightweight client fingerprint (UA + IP prefix)
function __session_fingerprint(): string {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    // Use first two octets for IPv4; for IPv6, just take a short prefix to reduce false positives
    if (preg_match('/^(\d+\.\d+)/', $ip, $m)) {
        $ipPrefix = $m[1];
    } else {
        $ipPrefix = substr($ip, 0, 8);
    }
    return hash('sha256', $ua . '|' . $ipPrefix);
}

if (empty($_SESSION['__fp'])) {
    $_SESSION['__fp'] = __session_fingerprint();
} elseif (!hash_equals($_SESSION['__fp'], __session_fingerprint())) {
    // Fingerprint changed: treat as potential hijack -> reset session
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_regenerate_id(true);
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['__fp'] = __session_fingerprint();
}

// CSRF token provisioning and check helper
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_check_post(string $key = 'csrf_token'): bool {
    return isset($_POST[$key], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$_POST[$key]);
}

?>
