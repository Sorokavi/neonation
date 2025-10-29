<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Discord Embed Open Graph -->
    <meta property="og:title"<body>
    <div class="main-content">
        <!-- Standardized header with navigation -->
        <div class="page-header">
            <a href="/" class="header-button back-button">← Back to Home</a>
        </div>
        <h1>Download World</h1>tent="Neonation- SMP World" />
    <meta property="og:description" content="Download the modded/vanilla minecraft SMP worlds here!" />
    <meta property="og:image" content="https://neonation.net/assets/neoslayer.png" />
    <meta property="og:url" content="https://neonation.net/" />
    <meta property="og:type" content="website" />
    <meta name="theme-color" content="#39FF14" />
    <meta charset="UTF-8">
    <title>Neo Nation- SMP Worlds</title>
    <link rel="icon" href="../assets/neoslayer.png" sizes="32x32" type="image/png">
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
            max-width: 600px;
            margin: 2.5em auto 2em auto;
            padding: 2.5em 2em 2em 2em;
        }
        h1 {
            font-size: 2.5em;
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
        }
        .neon-button:hover, .neon-button:focus {
            background: #000;
            color: #aa5c00;
            box-shadow: 0 0 10px #aa5c00, 0 0 30px #aa5c00;
            outline: none;
        }
        progress {
            width: 80%;
            height: 1.5em;
            margin: 1em auto;
            display: none;
            border-radius: 8px;
            border: 2px solid #39FF14;
            box-shadow: 0 0 10px #39FF14, 0 0 20px #39FF14;
            background: #111;
        }
        progress::-webkit-progress-bar {
            background: #111;
            border-radius: 8px;
        }
        progress::-webkit-progress-value {
            background: linear-gradient(90deg, #39FF14 0%, #39ff14cc 100%);
            box-shadow: 0 0 10px #39FF14, 0 0 20px #39FF14;
            border-radius: 8px;
        }
        progress::-moz-progress-bar {
            background: linear-gradient(90deg, #39FF14 0%, #39ff14cc 100%);
            box-shadow: 0 0 10px #39FF14, 0 0 20px #39FF14;
            border-radius: 8px;
        }
        progress::-ms-fill {
            background: linear-gradient(90deg, #39FF14 0%, #39ff14cc 100%);
            box-shadow: 0 0 10px #39FF14, 0 0 20px #39FF14;
            border-radius: 8px;
        }
        footer {
            margin-top: 4em;
            font-size: 0.95em;
            opacity: 0.7;
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
    </style>
</head>

<body>
    <div class="main-content">
        <a href="/" class="neon-button" style="margin-bottom:1.5em; display:inline-block;">Back to Home</a>
        <h1>Download World</h1>
        <hr>
        <?php
        // locate the .tar.gz archive
        $archives = glob(__DIR__ . '/*.tar.gz');
        if (!$archives):
        ?>
            <p>No world archive found.</p>
        <?php
        else:
            $file = basename($archives[0]);
        ?>
            <?php
            $size = filesize(__DIR__ . '/' . $file);
            function human_filesize($bytes, $decimals = 2) {
                $size = array('B','KB','MB','GB','TB','PB');
                $factor = floor((strlen($bytes) - 1) / 3);
                return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
            }
            ?>
            <button id="downloadBtn" class="neon-button">Download <?= htmlspecialchars($file) ?></button>
            <div id="fileSize" style="margin:0.5em 0; color:#39FF14; font-size:1.1em;">
                Approx. file size: <b><?= human_filesize($size) ?></b>
            </div>
            <progress id="progressBar" max="100" value="0"></progress>
            <div id="progressText" style="margin:0.5em 0; color:#39FF14; font-size:1em; display:none;"></div>

            <script>
            (function(){
                var btn = document.getElementById('downloadBtn');
                var bar = document.getElementById('progressBar');
                var progressText = document.getElementById('progressText');
                var totalSize = <?= $size ?>;
                function humanFileSize(bytes) {
                    var thresh = 1024;
                    if (Math.abs(bytes) < thresh) {
                        return bytes + ' B';
                    }
                    var units = ['KB','MB','GB','TB','PB','EB','ZB','YB'];
                    var u = -1;
                    do {
                        bytes /= thresh;
                        ++u;
                    } while(Math.abs(bytes) >= thresh && u < units.length - 1);
                    return bytes.toFixed(2)+' '+units[u];
                }
                btn.addEventListener('click', function(){
                    btn.disabled = true;
                    bar.style.display = 'block';
                    progressText.style.display = 'block';
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET','<?= $file ?>',true);
                    xhr.responseType = 'blob';
                    xhr.onprogress = function(e){
                        if (e.lengthComputable) {
                            var percent = (e.loaded / e.total) * 100;
                            bar.value = percent;
                            progressText.textContent = 'Downloaded ' + humanFileSize(e.loaded) + ' / ' + humanFileSize(e.total) + ' (' + percent.toFixed(1) + '%)';
                        } else {
                            progressText.textContent = 'Downloading...';
                        }
                    };
                    xhr.onload = function(){
                        var url = URL.createObjectURL(xhr.response);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = '<?= $file ?>';
                        document.body.appendChild(a);
                        a.click();
                        URL.revokeObjectURL(url);
                        btn.textContent = 'Done!';
                        progressText.textContent = 'Download complete!';
                    };
                    xhr.send();
                });
            })();
            </script>
        <?php endif; ?>
    </div>
    <footer>
        <p style="color: #aa5c00"> More worlds to come soon, I suppose.</p>
    </footer>
</body>
</html>
