<?php
require_once 'load-env.php';

$client_id = env('DISCORD_CLIENT_ID');
$redirect_uri = env('DISCORD_REDIRECT_URI');
$scope = 'identify guilds';

header('Location: https://discord.com/api/oauth2/authorize?' . http_build_query([
  'client_id' => $client_id,
  'redirect_uri' => $redirect_uri,
  'response_type' => 'code',
  'scope' => $scope
]));
exit;
