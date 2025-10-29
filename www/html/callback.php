<?php
session_start();

require_once 'load-env.php';

$client_id     = $_ENV['DISCORD_CLIENT_ID'];
$client_secret = $_ENV['DISCORD_CLIENT_SECRET'];
$redirect_uri  = $_ENV['DISCORD_REDIRECT_URI'];

if (!isset($_GET['code'])) {
    die("No code provided by Discord.");
}

$code = $_GET['code'];

$token_url = 'https://discord.com/api/oauth2/token';
$post_data = http_build_query([
    'client_id'     => $client_id,
    'client_secret' => $client_secret,
    'grant_type'    => 'authorization_code',
    'code'          => $code,
    'redirect_uri'  => $redirect_uri,
    'scope'         => 'identify guilds'
]);

$opts = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $post_data
    ]
];

$response = file_get_contents($token_url, false, stream_context_create($opts));
if ($response === false) {
    die("Failed to exchange code for token.");
}

$token = json_decode($response, true);
if (!isset($token['access_token'])) {
    die("OAuth failed: " . htmlspecialchars($response));
}

$access_token = $token['access_token'];

$user_api_url = 'https://discord.com/api/users/@me';
$opts_user = [
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer {$access_token}\r\n"
    ]
];

$user_response = file_get_contents($user_api_url, false, stream_context_create($opts_user));
if ($user_response === false) {
    die("Failed to fetch user data.");
}

$userData = json_decode($user_response, true);
if (!isset($userData['id'])) {
    die("Invalid user data response: " . htmlspecialchars($user_response));
}

$guilds_api_url = 'https://discord.com/api/users/@me/guilds';
$opts_guilds = [
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer {$access_token}\r\n"
    ]
];

$guilds_response = file_get_contents($guilds_api_url, false, stream_context_create($opts_guilds));
if ($guilds_response === false) {
    die("Failed to fetch guilds data.");
}

$guildsData = json_decode($guilds_response, true);
if (!is_array($guildsData)) {
    die("Invalid guilds data response: " . htmlspecialchars($guilds_response));
}

$_SESSION['discord_user'] = [
    'id'       => $userData['id'],
    'username' => $userData['username'] . '#' . $userData['discriminator'],
    'avatar'   => $userData['avatar'] ?? null,
    'guilds'   => $guildsData
];

header('Location: /');
exit;
