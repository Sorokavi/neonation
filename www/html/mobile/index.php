<?php
    session_start();
    
    require_once __DIR__ . '/../Parsedown.php';
    $log = __DIR__ . '/../../environments/guestbook.txt';
    $maxEntries = 50;
    $entryError = '';
    $entrySuccess = '';
    $rateLimitSeconds = 86400;
    $now = time();

    if (!isset($_SESSION['last_guestbook_post'])) {
        $_SESSION['last_guestbook_post'] = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guestbook_submit'], $_SESSION['discord_user'])) {
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
                header('Location: /mobile/');
                exit;
            }
            $entryError = 'Could not write to the guestbook.';
        }
    }

    $entrySuccess = '';
    if (isset($_SESSION['entry_success'])) {
        $entrySuccess = $_SESSION['entry_success'];
        unset($_SESSION['entry_success']);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signout'])) {
        session_destroy();
        header('Location: /mobile/');
        exit;
    }
    ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Discord Embed Open Graph -->
        <meta property="og:title" content="Neonation" />
        <meta property="og:description" content="h0i!" />
        <meta property="og:image" content="https://neonation.net/server-icon.png" />
        <meta property="og:url" content="https://neonation.net/" />
        <meta property="og:type" content="website" />
        <meta name="theme-color" content="#39FF14" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Tab Name -->
        <title>Neo Nation (Mobile)</title>

        <!-- Favicon -->
        <link rel="icon" href="/assets/neoslayer.png" sizes="32x32" type="image/png">

        <style>
            html, body {
                max-width: 100vw;
                overflow-x: hidden;
            }
            body {
                background-image: url('/assets/tiles.gif');
                background-repeat: repeat;
                color: #39FF14;
                font-family: 'Courier New', Courier, monospace;
                margin: 0;
                padding: 1em;
                text-align: center;
                box-sizing: border-box;
                width: 100vw;
            }
            h1, h2, h3, h4, h5, h6, p, div, span, a, label {
                word-break: break-word;
                overflow-wrap: break-word;
            }
            h1 {
                font-size: 2em;
                margin-bottom: 0.2em;
                text-shadow: 0 0 5px #39FF14;
            }
            hr {
                border: none;
                height: 2px;
                background: #39FF14;
                width: 100%;
                margin: 1em auto;
            }
            a {
                color: #aa5c00;
                text-decoration: none;
                word-break: break-all;
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
                margin-top: 2em;
                font-size: 0.9em;
                opacity: 0.6;
            }
            .big-link {
                font-size: 1.3em;
                font-weight: bold;
                text-shadow: 0 0 8px #aa5c00;
                word-break: break-word;
            }
            .neon-button {
                background: #39FF14;
                border: 2px solid #39FF14;
                color: #000;
                padding: 0.5em 1em;
                margin-top: 0.9em;
                font-weight: bold;
                font-size: 1em;
                font-family: 'Courier New', Courier, monospace;
                text-transform: uppercase;
                letter-spacing: 1px;
                box-shadow: 0 0 10px #39FF14, 0 0 20px #39FF14 inset;
                cursor: pointer;
                transition: 0.15s ease-in-out;
                width: 100%;
                box-sizing: border-box;
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
                font-size: 0.9em;
            }
            .header-button {
                background: transparent;
                border: 2px solid #aa5c00;
                color: #aa5c00;
                padding: 0.3em 1.2em;
                font-size: 0.8em;
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
            .signout-button {
                background: transparent;
                border: 2px solid #aa5c00;
                color: #aa5c00;
                padding: 0.3em 1em;
                font-size: 0.9em;
                font-family: 'Courier New', Courier, monospace;
                cursor: pointer;
                transition: 0.15s ease-in-out;
                text-transform: uppercase;
                letter-spacing: 1px;
                width: 100%;
                box-sizing: border-box;
            }
            .signout-button:hover {
                background: #aa5c00;
                color: #111;
                box-shadow: 0 0 8px #aa5c00;
            }
            textarea {
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                height: 5em;
                resize: vertical;
                transition: all 0.2s ease-in-out;
                font-size: 1em;
            }
            textarea:focus {
                outline: none;
                border-color: #39FF14;
                box-shadow: 0 0 10px #39FF14;
                background-color: #111;
            }
            form {
                max-width: 100%;
                margin: 1em auto;
                background: #181818;
                border: 1px solid #39FF14;
                padding: 1em;
                box-sizing: border-box;
                width: 100%;
            }
            .discord-section {
                display: block;
                margin: 2em 0;
                width: 100%;
                box-sizing: border-box;
            }
            .discord-section .big-link {
                display: inline-block;
                margin-top: 1em;
            }
            .fella-container {
                width: 100vw;
                max-width: 100vw;
                margin: 0 auto;
                overflow: hidden;
                position: relative;
                height: 40px;
            }
            .fella-dance {
                position: absolute;
                height: 40px;
                animation: fellaSlide 10s linear infinite;
            }
            @keyframes fellaSlide {
                0% {
                    left: -100px;
                }
                100% {
                    left: 100vw;
                }
            }
            .dino-dark {
                filter: invert(1) hue-rotate(180deg) brightness(0.9);
            }
            /* Responsive adjustments */
            @media (max-width: 600px) {
                h1 {
                    font-size: 1.1em;
                }
                .big-link {
                    font-size: 1em;
                }
                .fella-container {
                    height: 30px;
                }
                .fella-dance {
                    height: 30px;
                }
                iframe, .playlist-iframe {
                    width: 100% !important;
                    min-width: 0 !important;
                    max-width: 100vw !important;
                }
            }
            /* Remove horizontal scroll for all elements */
            * {
                box-sizing: border-box;
                max-width: 100vw;
            }
        </style>
    </head>
    <body>
        <a href="/" class="neon-button" style="margin-bottom:1.5em; display:inline-block;">Back to Home</a>
        <!-- Welcome message for logged in users -->
        <?php if (isset($_SESSION['discord_user'])): ?>
        <div class="username-greet" style="display: flex; flex-direction: column; align-items: center; gap: 0.5em; margin-bottom: 1em; width: 100%;">
            <span style="font-size: 1em; word-break: break-word;">Welcome back, <?php echo $_SESSION['discord_user']['username']; ?>!</span>
            <form method="post" action="" style="width: 100%; max-width: 300px;">
                <input type="hidden" name="signout" value="1">
                <button type="submit" class="signout-button">Sign out</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Title -->
        <h1>Neo Nation (Mobile)</h1>
        <div class="blinker">⚡ Site under construction ⚡</div>
        <hr>

        <!-- Recruitment section -->
        <div class="discord-section">
            <div>
                <p><a href="https://discord.com/invite/neonation" class="big-link">Join us!</a></p>
                <p style="color: #39FF14; font-size: 1em; margin-top: 0.5em; word-break: break-word; overflow-wrap: break-word;">
                    We're a close knit silly community server of people who love Genshin, Arknights, Guardian Tales, Azur Lane, PGR, Limbus Company, Minecraft, Wuthering Waves, CRK, Clash, GD, Honkai and TONS more! Sometimes events happen as well, and we have some in-house game servers (mainly Minecraft, though sometimes Terraria playthroughs happen as well). You're welcome to join!
                </p>
                <br>
            </div>
            <div style="position: relative; width: 100%; max-width: 100vw; height: 220px; border: 2px solid #39FF14; margin: 0 auto; box-sizing: border-box; overflow: hidden;">
                <div style="
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 60px;
                    background: #39FF14;
                    color: #111;
                    font-weight: bold;
                    font-family: 'Courier New', monospace;
                    text-align: center;
                    line-height: 60px;
                    z-index: 2;
                    box-shadow: 0 0 6px #39FF14;
                    font-size: 1em;
                ">
                <span style="font-size: 1em; margin:0;">NEONATION Discord</span>
                </div>

            <iframe
                src="https://discord.com/widget?id=591004895839256595&theme=dark"
                width="100%"
                height="220"
                allowtransparency="true"
                frameborder="0"
                style="border: none; position: absolute; top: 0; left: 0; z-index: 1; width: 100%; height: 100%; min-width: 0; max-width: 100vw; box-sizing: border-box; display: block; margin: 0 auto;"
            ></iframe>
            </div>
        </div>
        <hr>

        <!-- Fella Dance Border -->
        <div class="fella-container">
            <img src="../assets/fella-dance.gif" alt="Fella Dance" class="fella-dance">
        </div>
        <hr>

        <!-- Playlist -->
        <div style="
            position: relative;
            width: 100%;
            max-width: 100vw;
            border: 2px solid #aa5c00;
            border-radius: 0;
            overflow: hidden;
            margin: 2em auto;
            padding-top: 48px; /* match header height */
            background: #111;
            box-sizing: border-box;
        ">
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 48px;
            background: #aa5c00;
            color: #111;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            text-align: center;
            line-height: 48px;
            z-index: 2;
            box-shadow: 0 0 6px #aa5c00;
            font-size: 1em;
        ">
        <h2 style="font-size:1em; margin:0;">Le Playlist</h2>
        </div>
        <div style="width: 100%; max-width: 100vw; overflow-x: auto; box-sizing: border-box;">
        <iframe 
            class="playlist-iframe"
            style="border-radius:0px; width: 100%; min-width: 0; max-width: 100vw; display: block; margin: 0 auto; box-sizing: border-box;"
            src="https://open.spotify.com/embed/playlist/3cDDP9AbGlexGyZzBoIp29?utm_source=generator&theme=0" 
            width="100%" 
            height="120" 
            frameBorder="0" 
            allowfullscreen="true" 
            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" 
            loading="lazy"
        ></iframe>
        </div>
        </div>
        <hr>

        <!-- Guestbook -->
		<div id="guestbook" style="text-align: left; background: #000; border: 1px dashed #39FF14; padding: 1em; max-width: 100vw; margin: 2em auto; box-sizing: border-box;">
			<h2 style="text-align: center; font-size:1em;">Guestbook</h2>
			<hr>
            <?php
                $Parsedown = new Parsedown();
                if (file_exists($log) && is_readable($log)) {
                    $lines = file($log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    if ($lines === false) {
                        echo "Error reading guestbook entries.";
                    } else {
                        $total = count($lines);
                        $start = max(0, $total - $maxEntries);
                        $displayLines = array_slice($lines, $start);
                        $Parsedown = new Parsedown();
                        foreach ($displayLines as $line) {
                            if (preg_match('/^\[(.*?)\] (.*?): (.*)$/', $line, $matches)) {
                                $timestamp = htmlspecialchars($matches[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                $username = htmlspecialchars($matches[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                $msg = str_replace(['\\r', '\\n'], ["\r", "\n"], $matches[3]);
                                echo '<div style="margin-bottom:1em; padding: 0.7em; background: #181818; border-left: 4px solid #39FF14;">';
                                echo '<span style="color: #888; font-size: 0.92em;">' . $timestamp . '</span> ';
                                echo '<span style="color: #aa5c00; font-weight: bold;">' . $username . '</span><br>';
                                echo '<div style="margin-top: 0.4em; color: #39FF14; word-break: break-word;">' . nl2br($Parsedown->text($msg)) . '</div>';
                                echo '</div>';
                            } else {
                                echo '<div style="margin-bottom:1em; padding: 0.7em; background: #181818; border-left: 4px solid #39FF14;">';
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
        <form method="post" action="" style="max-width: 100vw; margin: 2em auto; background: #181818; border: 1px solid #39FF14; padding: 1em;">
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
                window.addEventListener("load", function () {
                const guestbook = document.getElementById("guestbook");
                    if (guestbook) {
                        guestbook.scrollIntoView({ behavior: "smooth" });
                    }
                });
            </script>
        <?php endif; ?>
        <hr>

        <!--Dino Game-->
        <h2 style="font-size:1em;">Dino Game</h2>
        <p style="font-size: 0.9em; color: #aa5c00;">Mobile user detected, no Dino Game for you!</p>
        <hr>

        <footer>
            <p style="color: #aa5c00"> Site is under development, more to come soon^tm!</p>
        </footer>
    </body>
</html>
