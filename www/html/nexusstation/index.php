<?php
session_start();

require_once __DIR__ . '/../load-env.php';

function getCurrentPlayersFromSteamAPI($appId) {
    $url = "https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/?appid={$appId}";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'NeoNation Player Tracker'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['response']['player_count'])) {
        return (int)$data['response']['player_count'];
    }
    
    return null;
}

function getSteamPlayerData($appId) {
    $data = [
        'current' => null,
        'peak_today' => null,
        'all_time_peak' => null
    ];
    
    $currentPlayers = getCurrentPlayersFromSteamAPI($appId);
    if ($currentPlayers !== null) {
        $data['current'] = $currentPlayers;
    }
    
    return $data;
}

function getSteamChartsPeaks($appId) {
    $url = "https://steamcharts.com/app/{$appId}/chart-data.json";
    $context = stream_context_create([
        'http'=>['timeout'=>10, 'user_agent'=>'NeoNation Player Tracker']
    ]);
    $json = @file_get_contents($url, false, $context);
    if (!$json) return ['peak_today'=>null, 'all_time_peak'=>null];

    $points = json_decode($json, true);
    $now = time();
    $peakToday = $allPeak = 0;

    foreach ($points as $pt) {
        list($ms, $count) = $pt;
        $ts = intval($ms / 1000);
        if ($count > $allPeak) {
            $allPeak = $count;
        }
        // within last 24 hours
        if ($ts >= $now - 86400 && $count > $peakToday) {
            $peakToday = $count;
        }
    }

    return ['peak_today'=>$peakToday, 'all_time_peak'=>$allPeak];
}

$playerData = getSteamPlayerData(3224910);
$peaks      = getSteamChartsPeaks(3224910);
$playerData['peak_today']    = $peaks['peak_today'];
$playerData['all_time_peak'] = $peaks['all_time_peak'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Discord Embed Open Graph -->
    <meta property="og:title" content="Neo Nation- Nexus Station Observatory" />
    <meta property="og:description" content="Current: <?php echo number_format($playerData['current']); ?> | 24h Peak: <?php echo number_format($playerData['peak_today']); ?> | All-Time: <?php echo number_format($playerData['all_time_peak']); ?>" />
    <meta property="og:image" content="https://neonation.net/assets/neoslayer.png" />
    <meta property="og:url" content="https://neonation.net/nexus-station/" />
    <meta property="og:type" content="website" />
    <meta name="theme-color" content="#39FF14" />
    <meta charset="UTF-8">
    
    <!-- Tab Name -->
    <title>Neo Nation- NSPS</title>
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/neoslayer.png" sizes="32x32" type="image/png">
    
    <style>
        body {
            background-image: url('/assets/tiles.gif');
            background-repeat: repeat;
            color: #39FF14;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 2em;
            text-align: center;
        }
        a {
            color: #aa5c00;
            text-decoration: none;
            transition: color 0.15s;
        }
        a:hover {
            text-decoration: underline;
            color: #ffb347;
        }
        .main-content {
            background: #181818cc;
            border: 1.5px solid #39FF14;
            border-radius: 10px;
            box-shadow: 0 0 18px #181818, 0 0 8px #39FF14;
            max-width: 900px;
            margin: 2.5em auto 2em auto;
            padding: 2.5em 2em 2em 2em;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 0.2em;
            text-shadow: 0 0 5px #39FF14;
        }
        h2 {
            font-size: 2em;
            margin-bottom: 0.5em;
            text-shadow: 0 0 3px #39FF14;
        }
        hr {
            border: none;
            height: 2px;
            background: #39FF14;
            width: 60%;
            margin: 1em auto;
        }
        .neon-button {
            background: #aa5c00;
            border: 2px solid #aa5c00;
            color: #000;
            padding: 0.5em 2em;
            margin-top: 1em;
            font-weight: bold;
            font-size: 1em;
            font-family: 'Courier New', Courier, monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 0 10px #aa5c00, 0 0 20px #aa5c00, inset 0 0 20px #aa5c00;
            cursor: pointer;
            transition: 0.15s ease-in-out;
            text-decoration: none;
            display: inline-block;
        }
        .neon-button:hover, .neon-button:focus {
            background: #000;
            color: #aa5c00;
            box-shadow: 0 0 10px #aa5c00, 0 0 30px #aa5c00;
            outline: none;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2em;
            flex-wrap: wrap;
            gap: 1em;
        }
        .header-button {
            background: transparent;
            border: 2px solid #aa5c00;
            color: #aa5c00;
            padding: 0.3em 1.2em;
            font-size: 0.85em;
            font-family: 'Courier New', Courier, monospace;
            cursor: pointer;
            transition: 0.15s ease-in-out;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
        }
        .header-button:hover {
            background: #aa5c00;
            color: #111;
            box-shadow: 0 0 8px #aa5c00;
        }
        .back-button {
            border-color: #39FF14;
            color: #39FF14;
        }
        .back-button:hover {
            background: #39FF14;
            color: #111;
            box-shadow: 0 0 8px #39FF14;
        }
        .blinker {
            animation: blink 1s steps(2, start) infinite;
        }
        @keyframes blink {
            to { visibility: hidden; }
        }
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 2em;
            margin: 2em 0;
            flex-wrap: wrap;
        }
        .stat-card {
            background: #181818;
            border: 2px solid #39FF14;
            padding: 1.5em;
            min-width: 200px;
            box-shadow: 0 0 10px #39FF14;
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #39FF14;
            text-shadow: 0 0 8px #39FF14;
        }
        .stat-label {
            font-size: 1.1em;
            color: #aa5c00;
            margin-top: 0.5em;
        }
        .chart-container {
            background: #111;
            border: 2px solid #39FF14;
            margin: 2em auto;
            padding: 2em;
            max-width: 1000px;
            position: relative;
        }
        .info-panel {
            background: #181818;
            border: 1px dashed #39FF14;
            padding: 1.5em;
            margin: 2em auto;
            max-width: 800px;
            text-align: left;
        }
        .update-time {
            color: #888;
            font-size: 0.9em;
            font-style: italic;
        }
        footer {
            margin-top: 4em;
            font-size: 0.95em;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Standardized header with navigation -->
        <div class="page-header">
            <a href="/" class="header-button back-button">← Back to Home</a>
        </div>
        <h1>Nexus Station Observatory</h1>
        <h2 class="blinker">Player Statistics</h2>
        <hr />
        <!-- Current Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $playerData['current'] !== null ? number_format($playerData['current']) : ':C'; ?></div>
                <div class="stat-label">Current Players</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $playerData['peak_today'] !== null ? number_format($playerData['peak_today']) : ':C'; ?></div>
                <div class="stat-label">24h Peak</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $playerData['all_time_peak'] !== null && $playerData['all_time_peak'] > 0 ? number_format($playerData['all_time_peak']) : ':C'; ?></div>
                <div class="stat-label">All-Time Peak</div>
            </div>
        </div>
        <!-- Info Panel -->
        <div class="info-panel">
            <h3 style="color: #39FF14; margin-top: 0;">What is this?</h3>
            <p>Player data for Nexus Station. Hehehehaw.</p>
            <p class="update-time">Last updated: <?php echo date('Y-m-d H:i:s T'); ?></p>
            <p style="font-size: 0.9em; color: #888;">Data provided by <a href="https://steamcharts.com/app/3224910" target="_blank">SteamCharts</a> and Steam Web API</p>
        </div>
    </div>
    <!-- Auto-refresh script -->
    <script>
        setTimeout(function() {
            location.reload();
        }, 300000);
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                    this.style.boxShadow = '0 0 20px #39FF14';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = '0 0 10px #39FF14';
                });
            });
        });
    </script>
    <hr>
    <footer>
        <p style="color: #aa5c00">All things considered, PW was a better game.</p>
    </footer>
</body>
</html>
