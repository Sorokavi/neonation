<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dino Game</title>
  <style>
    body {
      background-image: url('../assets/tiles.gif');
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
      max-width: 700px;
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
    #game {
      position: relative;
      background: #f7f7f7;
      border: 2px solid #535353;
      width: 600px;
      height: 150px;
      overflow: hidden;
      margin: 0 auto;
    }

    #dino, .obstacle, .cloud {
      position: absolute;
      bottom: 0;
      background-repeat: no-repeat;
      background-size: contain;
    }

    .fallback::before {
      content: attr(data-label);
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
      color: #fff;
      font-weight: bold;
      font-size: 20px;
    }

    .fallback.dino {
      background: #535353;
      border-radius: 6px;
    }

    .fallback.obstacle {
      background: #535353;
      border-radius: 4px;
    }

    .fallback.cloud {
      background: #c9c9c9;
      border-radius: 50%;
    }

    .fallback.ground {
      background: #535353;
    }

    #score, #highscore {
      position: absolute;
      top: 10px;
      font-size: 16px;
      color: #535353;
      font-family: monospace;
    }

    #score {
      right: 20px;
    }

    #highscore {
      left: 20px;
    }

    #gameover {
      display: none;
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255,255,255,0.9);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: #535353;
      z-index: 2;
      text-align: center;
      line-height: 1.2;
      font-family: monospace;
    }

    #gameover button {
      margin-top: 10px;
      font-size: 20px;
      cursor: pointer;
    }

    #loader {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(255,255,255,0.95);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      color: #555;
      z-index: 10;
    }

    #touch-controls {
      display: none;
      position: absolute;
      bottom: 10px;
      left: 0;
      width: 100%;
      z-index: 5;
      justify-content: space-between;
      pointer-events: none;
    }

    #touch-controls button {
      pointer-events: auto;
      font-size: 20px;
      margin: 0 10px;
      cursor: pointer;
    }

    #startscreen {
      display: flex;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255,255,255,0.95);
      align-items: center;
      justify-content: center;
      z-index: 20;
      flex-direction: column;
      font-size: 28px;
      color: #444;
    }

    @media (max-width: 700px) {
      #game {
        width: 98vw;
        height: 30vw;
        min-width: 240px;
        min-height: 120px;
        max-width: 100vw;
        max-height: 40vw;
      }

      #score, #highscore {
        font-size: 16px;
      }

      #gameover {
        font-size: 22px;
      }
    }
    @media (max-width: 700px), (pointer: coarse) {
      #touch-controls {
        display: flex !important;
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 80px;
        background: rgba(255,255,255,0.85);
        z-index: 100;
        justify-content: center;
        align-items: center;
        pointer-events: none;
        box-shadow: 0 -2px 12px rgba(0,0,0,0.08);
      }
      #touch-controls button {
        pointer-events: auto;
        font-size: 28px;
        padding: 18px 32px;
        margin: 0 24px;
        border-radius: 16px;
        background: #fffbe6;
        border: 2px solid #888;
        color: #333;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: background 0.15s, box-shadow 0.15s;
      }
      #touch-controls button:active, #touch-controls button.pressed {
        background: #ffe066;
        box-shadow: 0 2px 12px rgba(0,0,0,0.18);
      }
    }

    #ground {
      z-index: 0;
    }
    .ground {
      z-index: 0 !important;
    }
    #dino, .obstacle, .pterodactyl {
      z-index: 2;
    }
  </style>
</head>
    <body>
  <div class="main-content">
    <!-- Standardized header with navigation -->
    <div class="page-header">
      <a href="/" class="header-button back-button">← Back to Home</a>
    </div>
    <h1>Dino Game</h1>
    <hr>
    <div id="loader">Loading...</div>
    <div id="game">
      <div id="dino" data-label="D"></div>
      <div id="score">0</div>
      <div id="highscore"></div>
      <div id="gameover">
        GAME OVER<br>Press Space or Tap to Restart
        <button id="restart-btn" class="neon-button">Restart</button>
      </div>
      <div id="touch-controls">
        <button id="jump-btn" class="neon-button">Jump</button>
        <button id="duck-btn" class="neon-button">Duck</button>
      </div>
      <div id="startscreen">
        Press Space or Tap to Start
        <button id="start-btn" class="neon-button" style="margin-top:16px;font-size:20px;">Start</button>
      </div>
    </div>
  </div>
  <footer>
    <p style="color: #aa5c00"> Site is under development, more to come soon^tm!</p>
  </footer>

  <script src="./js/constants.js"></script>
  <script src="./js/utils.js"></script>
  <script src="./js/state.js"></script>
  <script src="./js/parallax.js"></script>
  <script src="./js/ground.js"></script>
  <script src="./js/clouds.js"></script>
  <script src="./js/dino.js"></script>
  <script src="./js/obstacles.js"></script>
  <script src="./js/preload.js"></script>
  <script src="./js/game.js"></script>
  <script>
    if (window.DinoGame && typeof window.DinoGame.start === 'function') { window.DinoGame.start(); }
  </script>
</body>
</html>
