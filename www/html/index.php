<?php
    // Mobile redirect
    if (!isset($_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], '/mobile') === false) {
        $isMobile = false;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
            if (preg_match('/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/', $ua)) {
                $isMobile = true;
            }
        }
        if ($isMobile) {
            header('Location: /mobile/');
            exit;
        }
    }

    require_once __DIR__ . '/session_init.php';
    
    require_once __DIR__ . '/Parsedown.php';
    $log = __DIR__ . '/../environments/guestbook.txt';
    $maxEntries = 50;
    $entryError = '';
    $entrySuccess = '';
    $rateLimitSeconds = 86400;
    $now = time();

    if (!isset($_SESSION['last_guestbook_post'])) {
        $_SESSION['last_guestbook_post'] = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guestbook_submit'], $_SESSION['discord_user'])) {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', (string)$_POST['csrf_token'])) {
            $entryError = 'Invalid CSRF token.';
        } else {
        $entry = trim($_POST['guestbook_entry']);
        if ($entry === '' || preg_match('/^\s*$/', str_replace(["\r", "\n"], '', $entry))) {
            $entryError = 'Entry cannot be empty or only whitespace!';
        } elseif (mb_strlen($entry) > 500) {
            $entryError = 'Entry is too long (max 500 characters).';
        } elseif ($now - $_SESSION['last_guestbook_post'] < $rateLimitSeconds) {
            $entryError = 'You can only post once per day. Please wait.';
        } else {
            $username = $_SESSION['discord_user']['username'];
            $timestamp = date('Y-m-d H:i');
            $line = "[$timestamp] $username: " . str_replace(["\r", "\n"], ['\\r', '\\n'], $entry);
            $fp = fopen($log, 'a');
            if ($fp && flock($fp, LOCK_EX)) {
                fwrite($fp, $line . "\n");
                flock($fp, LOCK_UN);
                fclose($fp);
                $_SESSION['last_guestbook_post'] = $now;
                $_SESSION['entry_success'] = 'Thank you for signing the guestbook!';
                header('Location: /');
                exit;
            }
            $entryError = 'Could not write to the guestbook.';
        }
        }
    }

    $entrySuccess = '';
    if (isset($_SESSION['entry_success'])) {
        $entrySuccess = $_SESSION['entry_success'];
        unset($_SESSION['entry_success']);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signout'])) {
        if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'] ?? '', (string)$_POST['csrf_token'])) {
            session_destroy();
            header('Location: /');
            exit;
        }
    }
    ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Discord Embed Open Graph -->
        <meta property="og:title" content="Neonation" />
        <meta property="og:description" content="h0i!" />
        <meta property="og:image" content="https://neonation.net/assets/neoslayer.png" />
        <meta property="og:url" content="https://neonation.net/" />
        <meta property="og:type" content="website" />
        <meta name="theme-color" content="#39FF14" />
        <meta charset="UTF-8">

        <!-- Tab Name -->
        <title>Neo Nation</title>

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
            h1 {
                font-size: 3em;
                margin-bottom: 0.2em;
                text-shadow: 0 0 5px #39FF14;
            }
            hr {
                border: none;
                height: 2px;
                background: #39FF14;
                width: 60%;
                margin: 1em auto;
            }
            a {
                color: #aa5c00;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
            .blinker {
                animation: blink 1s steps(2, start) infinite;
            }
            @keyframes blink {
                to { visibility: hidden; }
            }
            footer {
                margin-top: 4em;
                font-size: 0.9em;
                opacity: 0.6;
            }
            .big-link {
                font-size: 2.5em;
                font-weight: bold;
                text-shadow: 0 0 8px #aa5c00;
            }
            .neon-button {
                background: #39FF14;
                border: 2px solid #39FF14;
                color: #000;
                padding: 0.5em 2em;
                margin-top: 0.9em;
                font-weight: bold;
                font-size: 1em;
                font-family: 'Courier New', Courier, monospace;
                text-transform: uppercase;
                letter-spacing: 1px;
                box-shadow: 0 0 10px #39FF14, 0 0 20px #39FF14 inset;
                cursor: pointer;
                transition: 0.15s ease-in-out;
            }
            .neon-button:hover {
                background: #000;
                color: #39FF14;
                box-shadow: 0 0 10px #39FF14, 0 0 30px #39FF14;
            }
            .page-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2em;
                flex-wrap: wrap;
                gap: 1em;
            }
            .user-info {
                color: #39FF14;
                font-size: 0.95em;
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
            textarea {
                width: 95%;
                max-width: 100%;
                box-sizing: border-box;
                height: 6em;
                resize: vertical;
                transition: all 0.2s ease-in-out;
            }
            textarea:focus {
                outline: none;
                border-color: #39FF14;
                box-shadow: 0 0 10px #39FF14;
                background-color: #111;
            }
            form {
                max-width: 600px;
                margin: 2em auto;
                background: #181818;
                border: 1px solid #39FF14;
                padding: 1em;
            }
            .discord-section {
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: flex-start;
                gap: 2em;
                margin: 3em 0;
                flex-wrap: wrap;
            }
            .discord-section .big-link {
                display: inline-block;
                margin-top: 1em;
            }
            .fella-container {
                width: 60%;
                min-width: 400px;
                max-width: 1100px;
                margin: 0 auto;
                overflow: hidden;
                position: relative;
                height: 100px;
            }
            .fella-dance {
                position: absolute;
                height: 100px;
            }
            @keyframes fellaSlide {
                0% {
                    left: -150px;
                }
                100% {
                    left: 1100px;
                }
            }
            .dino-dark {
                filter: invert(1) hue-rotate(180deg) brightness(0.9);
            }
        </style>
    </head>
    <body>
        <!-- Standardized header with navigation and auth -->
        <div class="page-header">
            <?php if (isset($_SESSION['discord_user'])): ?>
            <div class="user-info">
                Welcome back, <strong><?php echo $_SESSION['discord_user']['username']; ?></strong>!
                <form method="post" action="" style="display:inline; margin-left: 1em;">
                    <input type="hidden" name="signout" value="1">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <button type="submit" class="header-button logout-button">Sign Out</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- Title -->
        <h1>Neo Nation</h1>
        <div class="blinker">⚡ Site under construction ⚡</div>
        <hr>

        <!-- Recruitment section -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: flex-start; gap: 3em; margin-top: 3em; text-align: left; max-width: 1000px; margin-left: auto; margin-right: auto;">
            <div style="max-width: 500px;">
                <p><a href="/invite" class="big-link">Join us!</a></p>
                <p style="color: #39FF14; font-size: 1em; margin-top: 0.5em;">
                    We're a close knit silly community server of people who love Genshin, Arknights, Guardian Tales, Azur Lane, PGR, Limbus Company, Minecraft, Wuthering Waves, CRK, Clash, GD, Honkai and TONS more! Sometimes events happen as well, and we have some in-house game servers (mainly Minecraft, though sometimes Terraria playthroughs happen as well). You're welcome to join!
                </p>
                <br>
            </div>
            <div style="position: relative; width: 350px; height: 500px; border: 2px solid #39FF14;">
                <div style="
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 75px;
                    background: #39FF14;
                    color: #111;
                    font-weight: bold;
                    font-family: 'Courier New', monospace;
                    text-align: center;
                    line-height: 48px;
                    z-index: 2;
                    box-shadow: 0 0 6px #39FF14;
                ">
                <p style="font-size: 1.6em">NEONATION Discord</p>
                </div>

            <iframe
                src="https://discord.com/widget?id=591004895839256595&theme=dark"
                width="350"
                height="500"
                allowtransparency="true"
                frameborder="0"
                style="border: none; position: absolute; top: 0; left: 0; z-index: 1;">
            </iframe>
            </div>
        </div>
        <hr>

        <!-- Fella Dance Border (User Content link) -->
        <div class="fella-container" style="width: 60%; min-width: 400px; max-width: 1100px; margin: 0 auto; overflow: hidden; position: relative; height: 100px;">
            <a href="/usercontent" style="position: absolute; left: 0; top: 0; height: 100px; display: block; animation: fellaSlide 10s linear infinite; width: 150px;">
                <span style="position: absolute; left: 50%; top: 0; transform: translateX(-50%, -120%); color: #39FF14; font-size: 1.1em; font-weight: bold; text-shadow: 0 0 6px #000, 0 0 8px #39FF14; pointer-events: none; white-space: nowrap;">CDN!!!</span>
                <img src="../assets/fella-dance.gif" alt="Fella Dance" style="height: 100px; display: block;">
            </a>
        </div>
        <hr>

        <!-- Playlist Thing + 3D Engine link -->
        <div style="
            position: relative;
            width: 1100px;
            border: 2px solid #aa5c00;
            border-radius: 0;
            overflow: hidden;
            margin: 2em auto;
            padding-top: 60px;
            background: #111;
        ">
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 75px;
            background: #aa5c00;
            color: #111;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            text-align: center;
            line-height: 75px;
            z-index: 2;
            box-shadow: 0 0 6px #aa5c00;
        ">
        <h2>Le Playlist</h2>
        <!-- 3D Engine link: floating cube icon -->
        <a href="/engine/" title="Try the 3D Engine!" style="position: absolute; right: 18px; top: 12px; display: inline-block; z-index: 3;">
            <span style="display: inline-block; width: 38px; height: 38px; background: linear-gradient(135deg,#39FF14 60%,#181818 100%); border-radius: 7px; box-shadow: 0 0 8px #39FF14, 0 0 2px #000; border: 2px solid #39FF14; transform: rotate(18deg);"></span>
            <span style="position: absolute; left: 50%; top: 110%; transform: translateX(-50%); color: #39FF14; font-size: 0.8em; font-weight: bold; text-shadow: 0 0 6px #000, 0 0 8px #39FF14;">3D!</span>
        </a>
        </div>
        <iframe 
            style="border-radius:0px" 
            src="https://open.spotify.com/embed/playlist/3cDDP9AbGlexGyZzBoIp29?utm_source=generator&theme=0" 
            width="100%" 
            height="352" 
            frameBorder="0" 
            allowfullscreen="true" 
            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" 
            loading="lazy"
        >
        </iframe>
        </div>
        <hr>

        <!-- Guestbook -->
		<div id="guestbook" style="text-align: left; background: #000; border: 1px dashed #39FF14; padding: 1em; max-width: 800px; margin: 2em auto;">
			<h2 style="text-align: center;">Guestbook</h2>
			<hr>
            <?php
                $Parsedown = new Parsedown();
                if (method_exists($Parsedown, 'setSafeMode')) {
                    $Parsedown->setSafeMode(true);
                } elseif (method_exists($Parsedown, 'setMarkupEscaped')) {
                    $Parsedown->setMarkupEscaped(true);
                }
                if (file_exists($log) && is_readable($log)) {
                    $lines = file($log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    if ($lines === false) {
                        echo "Error reading guestbook entries.";
                    } else {
                        $total = count($lines);
                        $start = max(0, $total - $maxEntries);
                        $displayLines = array_slice($lines, $start);
                        foreach ($displayLines as $line) {
                            if (preg_match('/^\[(.*?)\] (.*?): (.*)$/', $line, $matches)) {
                                $timestamp = htmlspecialchars($matches[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                $username = htmlspecialchars($matches[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                $msg = str_replace(['\\r', '\\n'], ["\r", "\n"], $matches[3]);
                                echo '<div style="margin-bottom:1.5em; padding: 1em; background: #181818; border-left: 4px solid #39FF14;">';
                                echo '<span style="color: #888; font-size: 0.95em;">' . $timestamp . '</span> ';
                                echo '<span style="color: #aa5c00; font-weight: bold;">' . $username . '</span><br>';
                                echo '<div style="margin-top: 0.5em; color: #39FF14;">' . nl2br($Parsedown->text($msg)) . '</div>';
                                echo '</div>';
                            } else {
                                echo '<div style="margin-bottom:1.5em; padding: 1em; background: #181818; border-left: 4px solid #39FF14;">';
                                echo htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "<br>";
                                echo '</div>';
                            }
                        }
                        if ($total > $maxEntries) {
                            echo "<em>... (showing last $maxEntries of $total entries) ...</em>";
                        }
                    }
                } else {
                    echo "No entries yet. Be the first!";
                }
                ?>
        </div>
        <?php if (isset($_SESSION['discord_user'])): ?>
        <form method="post" action="" style="max-width: 600px; margin: 2em auto; background: #181818; border: 1px solid #39FF14; padding: 1em;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="guestbook_submit" value="1">
            <label for="guestbook_entry" style="display: block; margin-bottom: 0.5em;">Sign the Guestbook:</label>
            <p style="font-size: 0.9em;">Signed in as <strong><?php echo $_SESSION['discord_user']['username']; ?></strong></p>
            <textarea id="guestbook_entry" name="guestbook_entry" rows="3" style="width: 100%; font-family: inherit; font-size: 1em; background: #222; color: #39FF14; border: 1px solid #39FF14;" maxlength="500" required></textarea>
            <br>
            <button type="submit" class="neon-button">Submit</button>
        </form>
        <?php else: ?>
        <p><a href="/login.php" class="a:hover">Log in with Discord to sign the guestbook.</a></p>
        <?php endif; ?>
        <?php          
            if ($entryError) {
                echo '<div style="color: #ff4444; font-weight: bold;">' . htmlspecialchars($entryError) . '</div>';
            }
            if ($entrySuccess) {
                echo '<div style="color: #39FF14; font-weight: bold;">' . htmlspecialchars($entrySuccess) . '</div>';
            }
            ?>
        <?php if (!empty($entrySuccess)): ?>
            <script>
                // Scroll to guestbook after successful post to reassure users their entry landed
                const GUESTBOOK_ID = 'guestbook';
                window.addEventListener('load', () => {
                    var guestbook = document.getElementById(GUESTBOOK_ID);
                    if (!guestbook) return; // Early exit if guestbook isn't on page
                    guestbook.scrollIntoView({ behavior: 'smooth' });
                });
            </script>
        <?php endif; ?>
        <hr>

    <!-- Dino Game Section -->
    <div style="
        position: relative;
        width: 1100px;
        margin: 2em auto;
        border: 2px solid #39FF14;
        background: #111;
        padding: 1em;
        box-shadow: 0 0 10px rgba(57, 255, 20, 0.2);
    ">
        <div style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1em;
        ">
            <h2 style="margin: 0;">Dino Game</h2>
            <a href="/dinogame/" 
               class="neon-button" 
               style="
                   font-size: 0.8em;
                   padding: 0.3em 1em;
                   text-decoration: none;
               "
            >Play Fullscreen</a>
        </div>
        <iframe 
            src="https://neonation.net/dinogame/" 
            frameborder="0" 
            scrolling="no" 
            width="100%" 
            height="440px" 
            loading="lazy" 
            class="dino-dark"
            style="border: 1px solid #39FF14;"
        ></iframe>
    </div>
    <hr>
        <!-- Nexus Station link: badge below guestbook -->
        <div style="margin: 2em auto 0 auto; text-align: center;">
            <a href="/nexusstation/" title="Nexus Station Observatory" style="display: inline-block; background: #181818; border: 2px solid #39FF14; border-radius: 8px; box-shadow: 0 0 8px #39FF14; color: #39FF14; font-weight: bold; font-size: 1.1em; padding: 0.5em 1.5em; margin: 0.5em auto 1.5em auto; text-shadow: 0 0 6px #000, 0 0 8px #39FF14; letter-spacing: 1px;">
                <span style="color:#aa5c00;">Nexus Station</span> Observatory &rarr;
            </a>
            <!-- SMP World Download link: ZA WARUDO badge -->
            <a href="/ZA_WARUDO/" title="SMP World Download" style="display: inline-block; background: #181818; border: 2px solid #aa5c00; border-radius: 8px; box-shadow: 0 0 8px #aa5c00; color: #aa5c00; font-weight: bold; font-size: 1.1em; padding: 0.5em 1.5em; margin: 0.5em 1em 1.5em 1em; text-shadow: 0 0 6px #000, 0 0 8px #aa5c00; letter-spacing: 1px;">
                ZA WARUDO &darr;
            </a>
        </div>
        <footer>
            <p style="color: #aa5c00"> Site is under development, more to come soon^tm!</p>
        </footer>
    </body>
</html>
