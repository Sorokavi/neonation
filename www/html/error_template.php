<?php
function render_error_page(string $title, string $heading, string $message, int $code = 500): void {
    http_response_code($code);
    ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Neonation – <?php echo htmlspecialchars($heading); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($message); ?>" />
    <meta property="og:image" content="https://neonation.net/assets/neoslayer.png" />
    <meta property="og:url" content="https://neonation.net/" />
    <meta property="og:type" content="website" />
    <meta name="theme-color" content="#39FF14" />
    <title><?php echo htmlspecialchars($title); ?></title>
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
      p {
        max-width: 700px;
        margin: 0.5em auto 1.5em auto;
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
        text-decoration: none;
        display: inline-block;
      }
      .neon-button:hover {
        background: #000;
        color: #39FF14;
        box-shadow: 0 0 10px #39FF14, 0 0 30px #39FF14;
      }
      .box {
        max-width: 800px;
        margin: 2em auto;
        background: #181818;
        border: 1px solid #39FF14;
        padding: 1.5em;
        box-shadow: 0 0 10px rgba(57,255,20,0.2);
      }
      .code { color: #aa5c00; font-weight: bold; }
    </style>
  </head>
  <body>
    <div class="box">
      <h1><?php echo htmlspecialchars($heading); ?></h1>
      <p class="code">HTTP <?php echo (int)$code; ?></p>
      <p><?php echo nl2br(htmlspecialchars($message)); ?></p>
      <a href="/" class="neon-button">Back to Home</a>
    </div>
  </body>
</html>
<?php }
?>
