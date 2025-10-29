<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Discord Embed Open Graph -->
    <meta property="og:title" content="Neonation- '3D' Engine" />
    <meta property="og:description" content="Fucking NeoNation Themed Wolfenstein Or Some Shit IDK" />
    <meta property="og:image" content="https://neonation.net/assets/neoslayer.png" />
    <meta property="og:url" content="https://neonation.net/engine/" />
    <meta property="og:type" content="website" />
    <meta name="theme-color" content="#39FF14" />
    <meta charset="UTF-8">

    <!-- Tab Name -->
    <title>Engine - Neonation</title>

    <!-- Favicon -->
    <link rel="icon" href="/assets/neoslayer.png" sizes="32x32" type="image/png">
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
            transition: color 0.15s;
        }
        a:hover {
            text-decoration: underline;
            color: #ffb347;
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
        
        /* Game-specific styles */
        .game-container {
            position: relative;
            display: inline-block;
            margin: 0 auto;
            border-radius: 6px;
            background: #000;
        }
        
        #game { 
            display: block;
            border-radius: 6px;
        }
        
        #minimap { 
            position: absolute; 
            left: 10px; 
            top: 10px; 
            background: rgba(17, 17, 17, 0.9); 
            border: 1px solid #39FF14;
            border-radius: 4px;
        }

        #weapon {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            height: 200px;
            image-rendering: pixelated;
            transform-origin: 50% 100%;
            transition: transform 0.1s ease-out;
        }

        .game-container.shooting #weapon {
            transform: translateX(-50%) translateY(-22px) scale(1.06);
        }
        
        footer {
            margin-top: 4em;
            font-size: 0.95em;
            opacity: 0.7;
        }
        
        .flash-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 200, 0.2);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.1s;
            z-index: 1000;
        }
        
        .flash-overlay.active {
            opacity: 1;
        }

        /* Wolf3D-style HUD: horizontal status bar beneath the game screen */
        #hud {
            position: relative;
            margin: 12px auto 0 auto;
            width: 700px;
            height: 84px;
            background: linear-gradient(180deg, #111 0%, #000 100%);
            border: 2px solid #39FF14;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
            box-shadow: 0 0 12px rgba(57,255,20,0.12);
            font-family: 'Courier New', monospace;
            color: #39FF14;
        }

        .hud-left, .hud-center, .hud-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hud-left { width: 260px; }
        .hud-center { flex: 1; justify-content: center; }
        .hud-right { width: 180px; justify-content: flex-end; }

        .face-portrait {
            width: 64px;
            height: 64px;
            background: #222;
            border: 2px solid #39FF14;
            display: flex;
            align-items: center;
            justify-content: center;
            image-rendering: pixelated;
        }

        .health-block {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 140px;
        }

        .health-text { font-size: 18px; font-weight: bold; color: #ff4444; }

        .health-bar {
            width: 140px;
            height: 14px;
            background: #222;
            border: 2px solid #660000;
            position: relative;
        }

        .health-fill {
            height: 100%;
            background: linear-gradient(90deg, #ff6666, #ff0000);
            box-shadow: 0 0 6px rgba(255,0,0,0.6);
            transition: width 0.2s ease;
        }

        .stat { font-size: 16px; text-shadow: none; }

        .ammo, .keys, .secrets, .score { padding: 4px 8px; border: 1px solid rgba(57,255,20,0.12); background: rgba(0,0,0,0.35); }

        .hud-logo { width: 52px; height: auto; }

        .health { 
            color: #ff4444; 
            transition: transform 0.2s, text-shadow 0.2s;
        }
        .health.invulnerable {
            animation: pulse-health 0.5s infinite alternate;
        }
        
        @keyframes pulse-health {
            from { text-shadow: 0 0 5px rgba(255, 68, 68, 0.5); }
            to   { text-shadow: 0 0 15px rgba(255, 68, 68, 1); }
        }

        .ammo { color: #ffff44; }
        .keys { color: #44ffff; }
        .secrets { color: #ff44ff; }
        .score { color: #ff88ff; }
        
        /* Debug panel */
        .debug-panel {
            position: absolute;
            top: 120px;
            left: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: #39FF14;
            padding: 10px;
            border: 1px solid #39FF14;
            border-radius: 4px;
            font-size: 12px;
            text-align: left;
            z-index: 1000;
            display: none;
        }
        
        .toggle-debug {
            position: absolute;
            top: 90px;
            left: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: #39FF14;
            border: 1px solid #39FF14;
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <audio id="shoot-sound" src="../assets/shoot.wav" preload="auto"></audio>
    <audio id="hit-sound" src="../assets/hit.wav" preload="auto"></audio>
    <audio id="enemy-shoot-sound" src="../assets/enemy-shoot.wav" preload="auto"></audio>
    <audio id="door-open-sound" src="../assets/door-open.wav" preload="auto"></audio>
    <audio id="secret-sound" src="../assets/secret.wav" preload="auto"></audio>
    
    <!-- Standardized header with navigation -->
    <div class="page-header">
        <div></div>
        <a href="/" class="header-button back-button">← Back to Home</a>
    </div>
    
    <!-- Title -->
    <h1>Fucking NeoNation Themed Wolfenstein Or Some Shit IDK</h1>
    <hr>
    
    <!-- Game Display Section -->
    <div style="
        position: relative;
        width: 700px;
        margin: 2em auto;
        border: 2px solid #39FF14;
        background: #111;
        padding: 1em;
        box-shadow: 0 0 15px rgba(57, 255, 20, 0.3);
        border-radius: 8px;
    ">
        <button class="toggle-debug" onclick="toggleDebug()">Debug</button>
        <div class="debug-panel" id="debug-panel">
            <div>Player Position: <span id="debug-pos">0,0</span></div>
            <div>Player Direction: <span id="debug-dir">0,0</span></div>
            <div>Enemies: <span id="debug-enemies">0</span></div>
            <div>FPS: <span id="debug-fps">0</span></div>
            <div>Enemy States: <span id="debug-states">-</span></div>
        </div>
        <div class="game-container">
            <canvas id="game" width="640" height="400"></canvas>
            <canvas id="minimap" width="140" height="98"></canvas>
            <img id="weapon" src="../assets/handgun_25d_idle.png" alt="Weapon">
            <div id="flash-overlay" class="flash-overlay"></div>
        </div>
        <div id="hud"></div>
    </div>
    
    <!-- Instructions Bubble -->
    <div style="
        text-align: left; 
        background: #181818; 
        border: 1px solid #39FF14; 
        border-radius: 8px;
        padding: 1.5em; 
        max-width: 600px; 
        margin: 2em auto;
        box-shadow: 0 0 10px rgba(57, 255, 20, 0.2);
    ">
        <h3 style="color: #39FF14; margin-top: 0; text-align: center;">Controls</h3>
        <div style="color: #39FF14; font-size: 1em; line-height: 1.5;">
            <p><span style="color: #aa5c00; font-weight: bold;">WASD</span> - Move and strafe</p>
            <p><span style="color: #aa5c00; font-weight: bold;">Mouse</span> - Look around (click to lock)</p>
            <p><span style="color: #aa5c00; font-weight: bold;">Left Click / Space</span> - Shoot</p>
            <p><span style="color: #aa5c00; font-weight: bold;">Shift</span> - Sprint (while moving)</p>
            <p><span style="color: #aa5c00; font-weight: bold;">E</span> - Use door/pushwall</p>
            <p><span style="color: #aa5c00; font-weight: bold;">F</span> - Toggle debug info</p>
            <hr style="border: none; height: 1px; background: #39FF14; margin: 1em 0;">
            <p style="font-size: 0.9em; color: #888;">
                Enemies have realistic AI: they patrol, investigate sounds, and hunt you down when spotted!
            </p>
        </div>
    </div>

    <script>
        class RaycastEngine {
            constructor() {
                this.canvas = document.getElementById('game');
                this.ctx = this.canvas.getContext('2d');
                this.minimapCanvas = document.getElementById('minimap');
                this.minimapCtx = this.minimapCanvas.getContext('2d');
                this.gameContainer = document.querySelector('.game-container');
                
                this.width = this.canvas.width;
                this.height = this.canvas.height;
                
                this.screenImageData = this.ctx.createImageData(this.width, this.height);
                this.screenBuffer = new Uint32Array(this.screenImageData.data.buffer);
                
                this.weaponImg = document.getElementById('weapon');
                this.weaponFrames = {
                    idle: "../assets/handgun_25d_idle.png",
                    fire: "../assets/handgun_25d_shot.png"
                };
                Object.values(this.weaponFrames).forEach(src => { var i = new Image(); i.src = src; });
                
                this.posX = 1.5;
                this.posY = 17.5;
                this.dirX = 1;
                this.dirY = 0;
                this.planeX = 0;
                this.planeY = 0.66;
                
                this.moveSpeed = 0.045;
                this.rotSpeed = 0.035;
                this.sprintMultiplier = 1.7;

                this.doors = new Map();
                this.pushing = [];
                this.weapons = { pistol:{rof:4, dmg:[8,15], spread:0.04, ammo:"bullets"} };
                this.currentWeapon = 'pistol';
                this.lastShot = 0;
                this.recentlyFired = false;
                this.lastShotTime = 0;
                this.soundRadius = 8; // How far gunshots can be heard
                
                // Enhanced map with more strategic layout
                this.map = [
                    [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
                    [1,0,0,0,0,1,0,0,0,0,0,0,0,2,0,0,0,0,0,1],
                    [1,0,0,0,0,1,0,0,0,0,0,0,0,1,0,0,0,0,0,1],
                    [1,0,0,1,1,1,2,0,1,1,1,1,0,1,1,1,2,0,0,1],
                    [1,0,0,0,0,0,0,0,1,0,0,1,0,0,0,1,0,0,0,1],
                    [1,1,2,0,0,0,0,0,1,0,0,1,0,0,0,1,0,0,0,1],
                    [1,0,1,0,0,1,1,1,1,0,0,1,1,0,0,1,1,2,1,1],
                    [1,0,1,0,0,2,0,0,0,0,0,0,1,0,0,0,0,0,0,1],
                    [1,0,1,0,0,1,0,0,0,0,0,0,1,0,0,0,0,0,0,1],
                    [1,0,2,0,0,1,0,0,1,1,1,0,1,0,0,1,0,0,0,1],
                    [1,0,0,0,0,1,0,0,1,0,1,0,1,0,0,1,0,0,0,1],
                    [1,0,0,0,0,0,0,0,1,0,1,0,0,0,0,1,0,0,0,1],
                    [1,1,1,1,2,1,0,0,1,0,1,1,1,1,1,1,0,0,0,1],
                    [1,0,0,0,0,1,0,0,1,0,0,0,0,0,0,1,0,0,0,1],
                    [1,0,0,0,0,1,0,0,1,0,0,0,0,0,0,1,0,0,0,1],
                    [1,0,0,1,1,1,0,0,1,1,1,1,1,0,0,1,1,1,1,1],
                    [1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1],
                    [1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1],
                    [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]
                ];
                
                this.sprites = [];
                
                this.collectibleAssets = {
                    health: '../assets/health.png',
                    ammo: '../assets/ammo.png',
                    key: '../assets/key.png',
                    secret: '../assets/secret.png',
                    treasure: '../assets/treasure.png'
                };

                this.collectibleImages = {};
                Object.entries(this.collectibleAssets).forEach(([k,src]) => {
                    var img = new Image(); img.src = src; this.collectibleImages[k] = img;
                });

                this.collectibles = [
                    {x: 2.5, y: 15.5, type: 'health'},
                    {x: 17.5, y: 15.5, type: 'health'},
                    {x: 3.5, y: 7.5,  type: 'health'},
                    {x: 16.5, y: 7.5,  type: 'health'},
                    {x: 7.5, y: 13.5, type: 'ammo'},
                    {x: 12.5, y: 13.5,type: 'ammo'},
                    {x: 9.5, y: 7.5,  type: 'ammo'},
                    {x: 2.5, y: 2.5,  type: 'key'},
                    {x: 17.5, y: 2.5, type: 'key'},
                    {x: 9.5, y: 4.5,  type: 'secret'},
                    {x: 5.5, y: 1.5,  type: 'treasure', value: 100},
                    {x: 14.5, y: 1.5, type: 'treasure', value: 100}
                ].map(c => ({...c, collected:false, asset: this.collectibleAssets[c.type] || null}));

                this.playerStats = {
                    health: 100,
                    maxHealth: 100,
                    ammo: 50,
                    maxAmmo: 100,
                    keys: 0,
                    secrets: 0,
                    score: 0,
                    lastDamageTime: 0,
                    invulnerableTime: 1000,
                    isDead: false
                };
                
                this.levelStats = {
                    startedAt: Date.now(),
                    endedAt: null,
                    kills: 0,
                    secrets: 0,
                    finished: false
                };

                this.minimapScale = 14;
                this.keys = {};
                this.shooting = false;
                this.sprinting = false;
                this.hudElement = document.getElementById('hud');
                
                // Wall textures
                this.wallTextures = [
                    "../assets/wall_bricks_tile.png",
                    "../assets/wall_bricks_tile_painting.png",
                    "../assets/wall_bricks_tile_sign.png",
                    "../assets/wall_bricks_tile_banner.png"
                ];
                this.wallTextureImages = [];
                this.wallTextureData = [];
                this.texturesLoaded = 0;
                
                this.floorTexture = new Image();
                this.floorTexture.src = "../assets/floor_tile_stone.png";
                this.ceilingTexture = new Image();
                this.ceilingTexture.src = "../assets/ceiling_tile_panels.png";
                
                // Enemy sprite frames
                this.enemyFrames = {
                    idle: "../assets/crow_enemy_idle.png",
                    flinch: "../assets/crow_enemy_flinch.png",
                    attack: "../assets/crow_enemy_attack.png"
                };
                
                this.preloadedSprites = {};
                Object.entries(this.enemyFrames).forEach(([key, src]) => {
                    var img = new Image();
                    img.onload = () => {
                        var canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        var ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        this.preloadedSprites[key] = {
                            image: img,
                            imageData: ctx.getImageData(0, 0, img.width, img.height)
                        };
                    };
                    img.src = src;
                });
                
                this.spriteTexture = new Image();
                this.spriteTexture.src = this.enemyFrames.idle;
                
                this.loadTextures();
                this.bindEvents();
                
                this.fps = 0;
                this.lastFrameTime = 0;
                this.frameCount = 0;
                this.debugMode = false;
                
                // Improved enemy placement with patrol routes
                this.placeEnemiesStrategic();
            }
            
            placeEnemiesStrategic() {
                // Define strategic positions and patrol routes for Wolf3D-style gameplay
                var enemyPlacements = [
                    // Guards at key positions
                    {x: 9.5, y: 1.5, patrol: [{x:8.5,y:1.5}, {x:10.5,y:1.5}], type: 'guard'},
                    {x: 1.5, y: 3.5, patrol: [{x:1.5,y:2.5}, {x:1.5,y:4.5}], type: 'guard'},
                    {x: 18.5, y: 3.5, patrol: [{x:18.5,y:2.5}, {x:18.5,y:4.5}], type: 'guard'},
                    
                    // Patrolling enemies in corridors
                    {x: 6.5, y: 7.5, patrol: [{x:5.5,y:7.5}, {x:13.5,y:7.5}], type: 'patrol'},
                    {x: 13.5, y: 8.5, patrol: [{x:13.5,y:7.5}, {x:13.5,y:11.5}], type: 'patrol'},
                    
                    // Room defenders
                    {x: 9.5, y: 10.5, patrol: [{x:8.5,y:10.5}, {x:10.5,y:10.5}], type: 'defender'},
                    {x: 15.5, y: 13.5, patrol: [{x:14.5,y:13.5}, {x:16.5,y:13.5}], type: 'defender'},
                    
                    // Elite enemies (higher stats)
                    {x: 9.5, y: 16.5, patrol: [{x:7.5,y:16.5}, {x:11.5,y:16.5}], type: 'elite'},
                ];
                
                enemyPlacements.forEach(placement => {
                    var enemy = {
                        x: placement.x,
                        y: placement.y,
                        texture: 2,
                        
                        // Enhanced stats based on type
                        health: placement.type === 'elite' ? 150 : placement.type === 'guard' ? 120 : 100,
                        maxHealth: placement.type === 'elite' ? 150 : placement.type === 'guard' ? 120 : 100,
                        damage: placement.type === 'elite' ? 25 : placement.type === 'guard' ? 20 : 15,
                        accuracy: placement.type === 'elite' ? 0.8 : placement.type === 'guard' ? 0.7 : 0.6,
                        
                        // AI State
                        state: 'patrolling', // idle, patrolling, investigating, alerted, attacking
                        alertLevel: 0, // 0-100, affects behavior
                        
                        // Patrol system
                        patrolRoute: placement.patrol,
                        currentPatrolTarget: 0,
                        patrolDirection: 1,
                        
                        // Detection and movement
                        dir: Math.random() * Math.PI * 2, // Current facing direction
                        detectionRange: placement.type === 'guard' ? 6 : 5,
                        hearingRange: placement.type === 'elite' ? 10 : 8,
                        attackRange: 4,
                        fov: Math.PI * 0.5, // 90 degree FOV
                        
                        // Movement and timing
                        speed: placement.type === 'elite' ? 0.02 : 0.015,
                        lastMoveTime: 0,
                        moveCooldown: 100,
                        lastAttackTime: 0,
                        attackCooldown: placement.type === 'elite' ? 800 : 1200,
                        lastStateChange: Date.now(),
                        
                        // Investigation system
                        investigationTarget: null,
                        lastPlayerPosition: null,
                        searchTime: 0,
                        maxSearchTime: 5000, // 5 seconds of searching
                        
                        // Animation and visual feedback
                        isFlinching: false,
                        flinchTime: 0,
                        animationFrame: 'idle',
                        
                        // Pathfinding cache
                        pathCache: null,
                        pathCacheTime: 0,
                        pathCacheTarget: null
                    };
                    
                    this.sprites.push(enemy);
                });
            }

            loadTextures() {
                var totalTextures = this.wallTextures.length + 2;
                
                var textureLoaded = () => {
                    this.texturesLoaded++;
                    if (this.texturesLoaded === totalTextures) {
                        this.precomputeTextures();
                        this.loop();
                    }
                };
                
                this.wallTextures.forEach((src, index) => {
                    var img = new Image();
                    img.onload = () => {
                        this.wallTextureImages[index] = img;
                        textureLoaded();
                    };
                    img.src = src;
                });
                
                this.floorTexture.onload = textureLoaded;
                this.ceilingTexture.onload = textureLoaded;
            }
            
            bindEvents() {
                var onKey = (e, down) => {
                    if (this.playerStats.isDead) return;
                    var k = e.key.toLowerCase();
                    this.keys[k] = down;
                    this.sprinting = !!this.keys['shift'];
                    if (k === ' ') e.preventDefault();
                    
                    if (down && k === 'e') {
                        var tx = Math.round(this.posX + this.dirX);
                        var ty = Math.round(this.posY + this.dirY);
                        
                        if (this.map[ty] && this.map[ty][tx] === 2) {
                            this.openDoor(tx, ty);
                        } else if (this.map[ty] && this.map[ty][tx] === 3) {
                            if (this.playerStats.keys > 0) {
                                this.playerStats.keys--;
                                this.map[ty][tx] = 2;
                                this.openDoor(tx, ty);
                                this.updateHUD();
                            }
                        } else if (this.map[ty] && this.map[ty][tx] === 4) {
                            this.tryPush(tx, ty);
                        }
                    }
                };
                addEventListener('keydown', e => onKey(e, true));
                addEventListener('keyup',   e => onKey(e, false));

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'f' || e.key === 'F') {
                        this.debugMode = !this.debugMode;
                        document.getElementById('debug-panel').style.display = this.debugMode ? 'block' : 'none';
                    }
                });

                // Mouse controls
                this.mouseEnabled = true;
                this.mouseSensitivity = 0.002;

                this.canvas.addEventListener('click', () => {
                    if (this.playerStats.isDead) return;
                    if (!this.mouseEnabled) return;
                    this.canvas.requestPointerLock = this.canvas.requestPointerLock || this.canvas.mozRequestPointerLock;
                    if (this.canvas.requestPointerLock) this.canvas.requestPointerLock();
                });

                var pointerLockChange = () => {
                    var locked = document.pointerLockElement === this.canvas || document.mozPointerLockElement === this.canvas;
                    this.isPointerLocked = locked;
                };
                document.addEventListener('pointerlockchange', pointerLockChange);
                document.addEventListener('mozpointerlockchange', pointerLockChange);

                document.addEventListener('mousemove', (e) => {
                    if (this.playerStats.isDead) return;
                    if (!this.mouseEnabled || !this.isPointerLocked) return;
                    var movementX = e.movementX || e.mozMovementX || e.webkitMovementX || 0;
                    this.rotate(movementX * this.mouseSensitivity);
                });

                document.addEventListener('mousedown', (e) => {
                    if (this.playerStats.isDead) return;
                    if (this.isPointerLocked && e.button === 0) {
                        this.shoot();
                    }
                });
            }
            
            precomputeTextures() {
                this.wallTextureData = this.wallTextureImages.map(img => {
                    var canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    return ctx.getImageData(0, 0, canvas.width, canvas.height);
                });
                
                this.floorCanvas = document.createElement('canvas');
                this.floorCanvas.width = this.floorTexture.width;
                this.floorCanvas.height = this.floorTexture.height;
                var floorCtx = this.floorCanvas.getContext('2d');
                floorCtx.drawImage(this.floorTexture, 0, 0);
                this.floorImageData = floorCtx.getImageData(0, 0, this.floorCanvas.width, this.floorCanvas.height);
                
                this.ceilingCanvas = document.createElement('canvas');
                this.ceilingCanvas.width = this.ceilingTexture.width;
                this.ceilingCanvas.height = this.ceilingTexture.height;
                var ceilingCtx = this.ceilingCanvas.getContext('2d');
                ceilingCtx.drawImage(this.ceilingTexture, 0, 0);
                this.ceilingImageData = ceilingCtx.getImageData(0, 0, this.ceilingCanvas.width, this.ceilingCanvas.height);
                
                this.spriteCanvas = document.createElement('canvas');
                this.spriteCanvas.width = this.spriteTexture.width;
                this.spriteCanvas.height = this.spriteTexture.height;
                var spriteCtx = this.spriteCanvas.getContext('2d');
                spriteCtx.drawImage(this.spriteTexture, 0, 0);
                this.spriteImageData = spriteCtx.getImageData(0, 0, this.spriteCanvas.width, this.spriteCanvas.height);
            }
            
            update(dt) {
                if (this.playerStats.isDead || this.levelStats.finished) return;
                
                var moved = false;
                var currentSpeed = this.sprinting ? this.moveSpeed * this.sprintMultiplier : this.moveSpeed;
                var playerRadius = 0.2;
                
                var moveX = 0;
                var moveY = 0;

                if (this.keys['w']) {
                    moveX += this.dirX;
                    moveY += this.dirY;
                }
                if (this.keys['s']) {
                    moveX -= this.dirX;
                    moveY -= this.dirY;
                }
                if (this.keys['a']) {
                    moveX += this.dirY;
                    moveY -= this.dirX;
                }
                if (this.keys['d']) {
                    moveX -= this.dirY;
                    moveY += this.dirX;
                }
                
                const moveLength = Math.hypot(moveX, moveY);
                if (moveLength > 0) {
                    moveX /= moveLength;
                    moveY /= moveLength;
                    moved = this.tryMove(moveX * currentSpeed, moveY * currentSpeed, playerRadius);
                }
                
                if (this.keys[' ']) {
                    this.shoot();
                }
                
                this.recentlyFired = false;
                
                this.updateDoors(dt);
                this.updatePushwalls(dt);
                this.updateEnemiesAdvanced(dt);
                this.updateCollectibles();
                
                return moved;
            }
            
            // Advanced AI system with realistic behaviors
            updateEnemiesAdvanced(dt) {
                const currentTime = Date.now();
                const playerPos = {x: this.posX, y: this.posY};
                
                // Check if player recently fired
                const hearGunshot = this.recentlyFired || (currentTime - this.lastShotTime < 500);
                
                for (var i = this.sprites.length - 1; i >= 0; i--) {
                    const enemy = this.sprites[i];
                    if (!enemy.health) continue;
                    
                    // Remove dead enemies
                    if (enemy.health <= 0) {
                        this.sprites.splice(i, 1);
                        this.levelStats.kills++;
                        continue;
                    }
                    
                    // Update flinching
                    if (enemy.isFlinching && currentTime - enemy.flinchTime > 200) {
                        enemy.isFlinching = false;
                    }
                    
                    const distToPlayer = Math.hypot(playerPos.x - enemy.x, playerPos.y - enemy.y);
                    const angleToPlayer = Math.atan2(playerPos.y - enemy.y, playerPos.x - enemy.x);
                    
                    // Line of sight check
                    const hasLOS = this.hasLineOfSight(enemy.x, enemy.y, playerPos.x, playerPos.y);
                    
                    // Visual detection (cone-based FOV)
                    const angleDiff = Math.abs(this.normalizeAngle(angleToPlayer - enemy.dir));
                    const canSeePlayer = hasLOS && 
                                       distToPlayer <= enemy.detectionRange && 
                                       angleDiff <= enemy.fov / 2;
                    
                    // Sound detection
                    const canHearPlayer = hearGunshot && distToPlayer <= enemy.hearingRange;
                    const canHearMovement = moved && distToPlayer <= 3;
                    
                    // State machine logic
                    this.updateEnemyState(enemy, {
                        canSee: canSeePlayer,
                        canHear: canHearPlayer || canHearMovement,
                        distance: distToPlayer,
                        playerPos: playerPos,
                        currentTime: currentTime
                    });
                    
                    // Execute behavior based on state
                    this.executeEnemyBehavior(enemy, playerPos, dt, currentTime);
                    
                    // Attack logic
                    if (enemy.state === 'attacking' && 
                        distToPlayer <= enemy.attackRange &&
                        hasLOS &&
                        currentTime - enemy.lastAttackTime > enemy.attackCooldown) {
                        
                        this.enemyAttack(enemy, distToPlayer);
                    }
                }
            }
            
            updateEnemyState(enemy, sensors) {
                const {canSee, canHear, distance, playerPos, currentTime} = sensors;
                const timeSinceStateChange = currentTime - enemy.lastStateChange;
                
                switch (enemy.state) {
                    case 'patrolling':
                        if (canSee) {
                            this.changeEnemyState(enemy, 'alerted', currentTime);
                            enemy.alertLevel = Math.min(100, enemy.alertLevel + 50);
                            enemy.lastPlayerPosition = {...playerPos};
                        } else if (canHear) {
                            this.changeEnemyState(enemy, 'investigating', currentTime);
                            enemy.investigationTarget = {...playerPos};
                            enemy.alertLevel = Math.min(100, enemy.alertLevel + 25);
                        }
                        break;
                        
                    case 'investigating':
                        if (canSee) {
                            this.changeEnemyState(enemy, 'alerted', currentTime);
                            enemy.alertLevel = 100;
                            enemy.lastPlayerPosition = {...playerPos};
                        } else if (timeSinceStateChange > enemy.maxSearchTime) {
                            this.changeEnemyState(enemy, 'patrolling', currentTime);
                            enemy.alertLevel = Math.max(0, enemy.alertLevel - 10);
                        }
                        break;
                        
                    case 'alerted':
                        if (canSee) {
                            enemy.lastPlayerPosition = {...playerPos};
                            enemy.alertLevel = 100;
                            if (distance <= enemy.attackRange) {
                                this.changeEnemyState(enemy, 'attacking', currentTime);
                            }
                        } else if (timeSinceStateChange > 3000) { // Lost player for 3 seconds
                            this.changeEnemyState(enemy, 'investigating', currentTime);
                            enemy.investigationTarget = enemy.lastPlayerPosition;
                            enemy.alertLevel = Math.max(50, enemy.alertLevel - 20);
                        }
                        break;
                        
                    case 'attacking':
                        if (!canSee || distance > enemy.attackRange * 1.2) {
                            this.changeEnemyState(enemy, 'alerted', currentTime);
                        }
                        break;
                }
            }
            
            changeEnemyState(enemy, newState, currentTime) {
                enemy.state = newState;
                enemy.lastStateChange = currentTime;
                enemy.pathCache = null; // Invalidate path cache
            }
            
            executeEnemyBehavior(enemy, playerPos, dt, currentTime) {
                switch (enemy.state) {
                    case 'patrolling':
                        this.doPatrol(enemy, dt);
                        // Slowly rotate to scan area
                        enemy.dir += (Math.random() - 0.5) * 0.02;
                        break;
                        
                    case 'investigating':
                        if (enemy.investigationTarget) {
                            this.moveTowardsTarget(enemy, enemy.investigationTarget, dt);
                            // Look around when reaching investigation point
                            if (Math.hypot(enemy.x - enemy.investigationTarget.x, enemy.y - enemy.investigationTarget.y) < 0.5) {
                                enemy.dir += (Math.random() - 0.5) * 0.05;
                            }
                        }
                        break;
                        
                    case 'alerted':
                        if (enemy.lastPlayerPosition) {
                            this.moveTowardsTarget(enemy, enemy.lastPlayerPosition, dt);
                            // Face towards last known player position
                            const angleToLastPos = Math.atan2(
                                enemy.lastPlayerPosition.y - enemy.y,
                                enemy.lastPlayerPosition.x - enemy.x
                            );
                            enemy.dir = this.lerpAngle(enemy.dir, angleToLastPos, 0.1);
                        }
                        break;
                        
                    case 'attacking':
                        // Face player and prepare to attack
                        const angleToPlayer = Math.atan2(playerPos.y - enemy.y, playerPos.x - enemy.x);
                        enemy.dir = this.lerpAngle(enemy.dir, angleToPlayer, 0.15);
                        // Stop moving when attacking
                        break;
                }
            }
            
            doPatrol(enemy, dt) {
                if (!enemy.patrolRoute || enemy.patrolRoute.length === 0) return;
                
                const target = enemy.patrolRoute[enemy.currentPatrolTarget];
                const distToTarget = Math.hypot(enemy.x - target.x, enemy.y - target.y);
                
                if (distToTarget < 0.3) {
                    // Reached patrol point, move to next
                    enemy.currentPatrolTarget += enemy.patrolDirection;
                    
                    // Reverse direction at endpoints
                    if (enemy.currentPatrolTarget >= enemy.patrolRoute.length) {
                        enemy.currentPatrolTarget = enemy.patrolRoute.length - 2;
                        enemy.patrolDirection = -1;
                    } else if (enemy.currentPatrolTarget < 0) {
                        enemy.currentPatrolTarget = 1;
                        enemy.patrolDirection = 1;
                    }
                } else {
                    // Move towards current patrol target
                    this.moveTowardsTarget(enemy, target, dt * 0.5); // Patrol slower
                    
                    // Face movement direction
                    const moveAngle = Math.atan2(target.y - enemy.y, target.x - enemy.x);
                    enemy.dir = this.lerpAngle(enemy.dir, moveAngle, 0.05);
                }
            }
            
            moveTowardsTarget(enemy, target, dt) {
                if (Date.now() - enemy.lastMoveTime < enemy.moveCooldown) return;
                
                const path = this.findPath(enemy, target);
                if (path.length > 1) {
                    const nextPoint = path[1]; // Skip current position
                    const dx = nextPoint.x - enemy.x;
                    const dy = nextPoint.y - enemy.y;
                    const distance = Math.hypot(dx, dy);
                    
                    if (distance > 0.1) {
                        const moveSpeed = enemy.speed * dt * 60; // Normalize for 60fps
                        const newX = enemy.x + (dx / distance) * moveSpeed;
                        const newY = enemy.y + (dy / distance) * moveSpeed;
                        
                        // Check collision
                        if (!this.isWall(newX, enemy.y)) enemy.x = newX;
                        if (!this.isWall(enemy.x, newY)) enemy.y = newY;
                        
                        enemy.lastMoveTime = Date.now();
                    }
                }
            }
            
            findPath(enemy, target) {
                const currentTime = Date.now();
                
                // Use cached path if valid and recent
                if (enemy.pathCache && 
                    enemy.pathCacheTime > currentTime - 1000 && // Cache for 1 second
                    enemy.pathCacheTarget &&
                    Math.hypot(enemy.pathCacheTarget.x - target.x, enemy.pathCacheTarget.y - target.y) < 0.5) {
                    return enemy.pathCache;
                }
                
                // A* pathfinding
                const start = {x: Math.floor(enemy.x), y: Math.floor(enemy.y)};
                const end = {x: Math.floor(target.x), y: Math.floor(target.y)};
                
                const openSet = [start];
                const cameFrom = new Map();
                const gScore = new Map();
                const fScore = new Map();
                
                const key = (p) => `${p.x},${p.y}`;
                gScore.set(key(start), 0);
                fScore.set(key(start), this.heuristic(start, end));
                
                while (openSet.length > 0) {
                    // Find node with lowest fScore
                    let current = openSet.reduce((min, node) => 
                        fScore.get(key(node)) < fScore.get(key(min)) ? node : min
                    );
                    
                    if (current.x === end.x && current.y === end.y) {
                        // Reconstruct path
                        const path = [];
                        while (current) {
                            path.unshift({x: current.x + 0.5, y: current.y + 0.5}); // Center of tile
                            current = cameFrom.get(key(current));
                        }
                        
                        // Cache the path
                        enemy.pathCache = path;
                        enemy.pathCacheTime = currentTime;
                        enemy.pathCacheTarget = {...target};
                        
                        return path;
                    }
                    
                    openSet.splice(openSet.indexOf(current), 1);
                    
                    // Check neighbors
                    const neighbors = [
                        {x: current.x + 1, y: current.y},
                        {x: current.x - 1, y: current.y},
                        {x: current.x, y: current.y + 1},
                        {x: current.x, y: current.y - 1}
                    ];
                    
                    for (const neighbor of neighbors) {
                        if (this.isWall(neighbor.x + 0.5, neighbor.y + 0.5)) continue;
                        
                        const tentativeGScore = gScore.get(key(current)) + 1;
                        const neighborKey = key(neighbor);
                        
                        if (!gScore.has(neighborKey) || tentativeGScore < gScore.get(neighborKey)) {
                            cameFrom.set(neighborKey, current);
                            gScore.set(neighborKey, tentativeGScore);
                            fScore.set(neighborKey, tentativeGScore + this.heuristic(neighbor, end));
                            
                            if (!openSet.find(n => n.x === neighbor.x && n.y === neighbor.y)) {
                                openSet.push(neighbor);
                            }
                        }
                    }
                }
                
                // No path found, return direct line
                return [{x: enemy.x, y: enemy.y}, {x: target.x, y: target.y}];
            }
            
            heuristic(a, b) {
                return Math.abs(a.x - b.x) + Math.abs(a.y - b.y);
            }
            
            enemyAttack(enemy, distance) {
                enemy.lastAttackTime = Date.now();
                enemy.animationFrame = 'attack';
                
                // Calculate accuracy based on distance and enemy stats
                var accuracy = enemy.accuracy * Math.max(0.3, 1 - distance * 0.1);
                
                if (Math.random() < accuracy) {
                    this.takeDamage(enemy.damage);
                    
                    // Visual feedback for being hit
                    const overlay = document.getElementById('flash-overlay');
                    overlay.style.background = 'rgba(255, 0, 0, 0.3)';
                    overlay.style.opacity = '1';
                    setTimeout(() => {
                        overlay.style.opacity = '0';
                        overlay.style.background = 'rgba(255, 255, 200, 0.2)';
                    }, 150);
                }
                
                const enemyShootSound = document.getElementById('enemy-shoot-sound');
                enemyShootSound.currentTime = 0;
                enemyShootSound.play();
                
                // Reset to idle after attack animation
                setTimeout(() => {
                    enemy.animationFrame = 'idle';
                }, 300);
            }
            
            // Utility functions
            normalizeAngle(angle) {
                while (angle > Math.PI) angle -= 2 * Math.PI;
                while (angle < -Math.PI) angle += 2 * Math.PI;
                return angle;
            }
            
            lerpAngle(from, to, t) {
                const diff = this.normalizeAngle(to - from);
                return from + diff * t;
            }
            
            openDoor(x, y) {
                const k = `${x},${y}`;
                if (!this.doors.has(k)) {
                    this.doors.set(k, {open: 0, dir: 1, timer: 3000}); // Stay open for 3 seconds
                    const doorOpenSound = document.getElementById('door-open-sound');
                    doorOpenSound.currentTime = 0;
                    doorOpenSound.play();
                }
            }

            updateDoors(dt) {
                for (const [k, d] of this.doors) {
                    if (d.dir > 0) {
                        d.open = Math.min(1, d.open + d.dir * dt * 0.003);
                        if (d.open === 1) {
                            d.timer = Math.max(0, d.timer - dt);
                            if (d.timer === 0) d.dir = -1;
                        }
                    } else {
                        d.open = Math.max(0, d.open + d.dir * dt * 0.003);
                        if (d.open === 0) this.doors.delete(k);
                    }
                }
            }
            
            tryPush(tx, ty) {
                if (this.map[ty] && this.map[ty][tx] === 4) {
                    const dirX = Math.round(this.dirX);
                    const dirY = Math.round(this.dirY);
                    this.pushing.push({x: tx, y: ty, dirX, dirY, left: 3, done: false});
                    this.map[ty][tx] = 0;
                    const secretSound = document.getElementById('secret-sound');
                    secretSound.currentTime = 0;
                    secretSound.play();
                }
            }

            updatePushwalls(dt) {
                const speed = dt * 0.002;
                this.pushing.forEach(p => {
                    const nx = p.x + p.dirX * speed;
                    const ny = p.y + p.dirY * speed;
                    if (!this.isWall(nx + 0.5, ny + 0.5)) {
                        p.x = nx;
                        p.y = ny;
                    }
                    if ((Math.abs((p.x % 1)) < 0.02) && (Math.abs((p.y % 1)) < 0.02)) {
                        p.left--;
                        if (p.left <= 0) p.done = true;
                    }
                });
                this.pushing = this.pushing.filter(p => !p.done);
            }
            
            hasLineOfSight(x1, y1, x2, y2) {
                var dx = x2 - x1;
                var dy = y2 - y1;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                dx /= distance;
                dy /= distance;
                
                const step = 0.1;
                var currentX = x1;
                var currentY = y1;
                
                for (var i = 0; i < distance / step; i++) {
                    currentX += dx * step;
                    currentY += dy * step;
                    
                    if (this.isWall(currentX, currentY)) {
                        return false;
                    }
                    
                    if (Math.hypot(currentX - x2, currentY - y2) < step) {
                        break;
                    }
                }
                
                return true;
            }

            updateCollectibles(){
                const picked = new Set();
                for (const c of this.collectibles){
                    if (c.collected) continue;
                    const px = this.posX, py = this.posY;
                    if (Math.hypot(px - c.x, py - c.y) < 0.5){
                        if (c.type === 'health') this.playerStats.health = Math.min(this.playerStats.maxHealth, this.playerStats.health + 25);
                        if (c.type === 'secret') this.playerStats.secrets = (this.playerStats.secrets||0) + 1;
                        if (c.type === 'ammo') this.playerStats.ammo = Math.min(this.playerStats.maxAmmo, this.playerStats.ammo + 15);
                        if (c.type === 'key') this.playerStats.keys = (this.playerStats.keys||0) + 1;
                        if (c.type === 'treasure') this.playerStats.score += (c.value || 50);
                        c.collected = true;
                        picked.add(c);
                        this.updateHUD();
                    }
                }
            }
            
            shoot(now = performance.now()) {
                if (this.playerStats.isDead || this.shooting) return;
                
                const w = this.weapons[this.currentWeapon];
                if (now - this.lastShot < 1000 / w.rof) return;
                
                if (this.playerStats.ammo <= 0) {
                    return;
                }
                
                this.playerStats.ammo--;
                this.lastShot = now;
                this.lastShotTime = now; // For enemy hearing
                this.recentlyFired = true;

                this.shooting = true;
                this.gameContainer.classList.add('shooting');

                this.weaponImg.src = this.weaponFrames.fire;

                const shootSound = document.getElementById('shoot-sound');
                shootSound.currentTime = 0;
                shootSound.play();

                const overlay = document.getElementById('flash-overlay');
                overlay.style.opacity = 1;
                setTimeout(() => overlay.style.opacity = 0, 80);

                const angle = Math.atan2(this.dirY, this.dirX) + (Math.random() - 0.5) * w.spread;
                const rayDirX = Math.cos(angle);
                const rayDirY = Math.sin(angle);
                
                const hit = this.raycastFirstActor(rayDirX, rayDirY);
                if (hit && hit.sprite) {
                    const damage = w.dmg[0] + Math.random() * (w.dmg[1] - w.dmg[0]);
                    hit.sprite.health -= damage;
                    
                    hit.sprite.isFlinching = true;
                    hit.sprite.flinchTime = now;
                    hit.sprite.animationFrame = 'flinch';
                    
                    // Alert nearby enemies to gunshot
                    this.alertEnemiestoGunshot();
                    
                    const hitSound = document.getElementById('hit-sound');
                    hitSound.currentTime = 0;
                    hitSound.play();
                }

                setTimeout(() => {
                    this.weaponImg.src = this.weaponFrames.idle;
                    this.shooting = false;
                    this.gameContainer.classList.remove('shooting');
                }, 120);

                this.render();
                this.updateHUD();
            }
            
            alertEnemiestoGunshot() {
                const gunshot = {x: this.posX, y: this.posY};
                
                this.sprites.forEach(enemy => {
                    if (!enemy.health) return;
                    
                    const distance = Math.hypot(gunshot.x - enemy.x, gunshot.y - enemy.y);
                    if (distance <= this.soundRadius) {
                        // Increase alert level
                        enemy.alertLevel = Math.min(100, enemy.alertLevel + 30);
                        
                        // Change state based on current state and distance
                        if (enemy.state === 'patrolling') {
                            enemy.state = 'investigating';
                            enemy.investigationTarget = {...gunshot};
                            enemy.lastStateChange = Date.now();
                        } else if (enemy.state === 'investigating' && distance < 4) {
                            enemy.state = 'alerted';
                            enemy.lastPlayerPosition = {...gunshot};
                            enemy.lastStateChange = Date.now();
                        }
                    }
                });
            }

            raycastFirstActor(rayDirX, rayDirY) {
                const rayLength = 0.5;
                var rayX = this.posX;
                var rayY = this.posY;
                
                for (var i = 0; i < 200; i++) {
                    rayX += rayDirX * rayLength;
                    rayY += rayDirY * rayLength;
                    
                    if (this.isWall(rayX, rayY)) {
                        return {type: 'wall'};
                    }

                    for (const sprite of this.sprites) {
                        if (sprite.health && Math.hypot(rayX - sprite.x, rayY - sprite.y) < 0.4) {
                            return {type: 'sprite', sprite: sprite};
                        }
                    }
                }
                return null;
            }
            
            rotate(angle) {
                const oldDirX = this.dirX;
                this.dirX = this.dirX * Math.cos(angle) - this.dirY * Math.sin(angle);
                this.dirY = oldDirX * Math.sin(angle) + this.dirY * Math.cos(angle);
                
                const oldPlaneX = this.planeX;
                this.planeX = this.planeX * Math.cos(angle) - this.planeY * Math.sin(angle);
                this.planeY = oldPlaneX * Math.sin(angle) + this.planeY * Math.cos(angle);
            }
            
            isWall(x, y) {
                const mx = Math.floor(x), my = Math.floor(y);
                if (!this.map[my] || this.map[my][mx] === undefined) return true;
                
                if (this.map[my][mx] === 2 || this.map[my][mx] === 3) {
                    const door = this.doors.get(`${mx},${my}`);
                    return !door || door.open < 0.95;
                }

                for (const p of this.pushing) {
                    if (Math.floor(p.x) === mx && Math.floor(p.y) === my) {
                        return true;
                    }
                }

                return this.map[my][mx] !== 0;
            }

            tryMove(moveX, moveY, radius) {
                const newX = this.posX + moveX;
                const newY = this.posY + moveY;
                
                if (!this.isWallWithRadius(newX, newY, radius)) {
                    this.posX = newX;
                    this.posY = newY;
                    return true;
                }
                
                if (!this.isWallWithRadius(this.posX + moveX, this.posY, radius)) {
                    this.posX += moveX;
                    return true;
                }
                
                if (!this.isWallWithRadius(this.posX, this.posY + moveY, radius)) {
                    this.posY += moveY;
                    return true;
                }
                
                return false;
            }

            isWallWithRadius(x, y, radius) {
                const points = [
                    [x - radius, y - radius],
                    [x - radius, y + radius],
                    [x + radius, y - radius],
                    [x + radius, y + radius]
                ];
                
                return points.some(([px, py]) => this.isWall(px, py));
            }
            
            takeDamage(amount) {
                const currentTime = Date.now();
                if (currentTime - this.playerStats.lastDamageTime < this.playerStats.invulnerableTime) {
                    return;
                }
                this.playerStats.health = Math.max(0, this.playerStats.health - amount);
                this.playerStats.lastDamageTime = currentTime;

                document.body.style.backgroundColor = '#ff0000';
                setTimeout(() => document.body.style.backgroundColor = '', 100);

                const hurtSound = document.getElementById('hit-sound');
                hurtSound.currentTime = 0;
                hurtSound.play();

                if (this.playerStats.health <= 0 && !this.playerStats.isDead) {
                    this.playerStats.isDead = true;
                    this.handlePlayerDeath();
                }

                this.updateHUD();
            }

            handlePlayerDeath() {
                const overlay = document.createElement('div');
                overlay.style.position = 'absolute';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100%';
                overlay.style.height = '100%';
                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
                overlay.style.display = 'flex';
                overlay.style.flexDirection = 'column';
                overlay.style.alignItems = 'center';
                overlay.style.justifyContent = 'center';
                overlay.style.color = '#ff0000';
                overlay.style.fontSize = '2em';
                overlay.style.zIndex = '1000';
                overlay.innerHTML = `
                    <div style="text-shadow: 0 0 10px #ff0000;">GAME OVER</div>
                    <button onclick="location.reload()" style="
                        margin-top: 20px;
                        padding: 10px 20px;
                        background: #ff0000;
                        border: none;
                        color: white;
                        cursor: pointer;
                        font-family: 'Courier New', monospace;
                    ">Try Again</button>
                `;
                document.querySelector('.game-container').appendChild(overlay);
            }

            updateHUD() {
                this.hudElement = this.hudElement || document.getElementById('hud');
                if (!this.hudElement) return;

                const healthPercent = Math.max(0, Math.min(100, Math.round((this.playerStats.health / this.playerStats.maxHealth) * 100)));

                this.hudElement.innerHTML = `
                    <div class="hud-left">
                        <div class="face-portrait" id="hud-face">
                            <img src="../assets/fella-dance.gif" alt="face" style="width:56px;height:56px;object-fit:contain;">
                        </div>
                        <div class="health-block health">
                            <div class="health-text">HP: ${this.playerStats.health} / ${this.playerStats.maxHealth}</div>
                            <div class="health-bar"><div class="health-fill" style="width: ${healthPercent}%"></div></div>
                        </div>
                    </div>
                    <div class="hud-center">
                        <div class="ammo stat">Ammo: ${this.playerStats.ammo}</div>
                        <div class="keys stat">Keys: ${this.playerStats.keys}</div>
                        <div class="secrets stat">Secrets: ${this.playerStats.secrets}/3</div>
                    </div>
                    <div class="hud-right">
                        <div class="score stat">Score: ${this.playerStats.score}</div>
                    </div>
                `;

                const currentTime = Date.now();
                const healthSection = this.hudElement.querySelector('.health');
                if (currentTime - this.playerStats.lastDamageTime < this.playerStats.invulnerableTime) {
                    healthSection.classList.add('invulnerable');
                } else {
                    healthSection.classList.remove('invulnerable');
                }
            }

            _showLevelStats() {
                const t = Math.round((this.levelStats.endedAt - this.levelStats.startedAt) / 1000);
                const overlay = document.createElement('div');
                overlay.style.position = 'absolute';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100%';
                overlay.style.height = '100%';
                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
                overlay.style.display = 'flex';
                overlay.style.flexDirection = 'column';
                overlay.style.alignItems = 'center';
                overlay.style.justifyContent = 'center';
                overlay.style.color = '#39FF14';
                overlay.style.fontSize = '1.5em';
                overlay.style.zIndex = '1000';
                overlay.innerHTML = `
                    <div style="text-shadow: 0 0 10px #39FF14;">LEVEL COMPLETE</div>
                    <div style="margin-top: 20px;">Time: ${t}s</div>
                    <div>Kills: ${this.levelStats.kills}</div>
                    <div>Secrets: ${this.levelStats.secrets}</div>
                    <div>Score: ${this.playerStats.score}</div>
                    <button onclick="location.reload()" style="
                        margin-top: 20px;
                        padding: 10px 20px;
                        background: #39FF14;
                        border: none;
                        color: black;
                        cursor: pointer;
                        font-family: 'Courier New', monospace;
                    ">Next Level</button>
                `;
                document.querySelector('.game-container').appendChild(overlay);
            }
            
            updateDebugInfo() {
                if (this.debugMode) {
                    document.getElementById('debug-pos').textContent = `${this.posX.toFixed(2)}, ${this.posY.toFixed(2)}`;
                    document.getElementById('debug-dir').textContent = `${this.dirX.toFixed(2)}, ${this.dirY.toFixed(2)}`;
                    document.getElementById('debug-enemies').textContent = this.sprites.filter(s => s.health > 0).length;
                    document.getElementById('debug-fps').textContent = Math.round(this.fps);
                    
                    // Show enemy states
                    const states = this.sprites.filter(s => s.health > 0).map(s => s.state.charAt(0).toUpperCase()).join('');
                    document.getElementById('debug-states').textContent = states || 'None';
                }
            }

            renderRaycast() {
                this.screenBuffer.fill(0);
                var zBuffer = [];
                this.renderFloorAndCeiling();
                
                for (var x = 0; x < this.width; x++) {
                    var cameraX = 2 * x / this.width - 1;
                    var rayDirX = this.dirX + this.planeX * cameraX;
                    var rayDirY = this.dirY + this.planeY * cameraX;
                    
                    var mapX = Math.floor(this.posX);
                    var mapY = Math.floor(this.posY);
                    
                    var deltaDistX = rayDirX === 0 ? 1e30 : Math.abs(1 / rayDirX);
                    var deltaDistY = rayDirY === 0 ? 1e30 : Math.abs(1 / rayDirY);
                    
                    var stepX, stepY, sideDistX, sideDistY;
                    
                    if (rayDirX < 0) {
                        stepX = -1;
                        sideDistX = (this.posX - mapX) * deltaDistX;
                    } else {
                        stepX = 1;
                        sideDistX = (mapX + 1.0 - this.posX) * deltaDistX;
                    }
                    
                    if (rayDirY < 0) {
                        stepY = -1;
                        sideDistY = (this.posY - mapY) * deltaDistY;
                    } else {
                        stepY = 1;
                        sideDistY = (mapY + 1.0 - this.posY) * deltaDistY;
                    }
                    
                    var hit = false, side = 0;
                    while (!hit) {
                        if (sideDistX < sideDistY) {
                            sideDistX += deltaDistX;
                            mapX += stepX;
                            side = 0;
                        } else {
                            sideDistY += deltaDistY;
                            mapY += stepY;
                            side = 1;
                        }
                        
                        if (mapY >= 0 && mapY < this.map.length && 
                            mapX >= 0 && mapX < this.map[0].length) {
                            if (this.map[mapY][mapX] !== 0) {
                                hit = true;
                                if (this.map[mapY][mapX] === 2 || this.map[mapY][mapX] === 3) {
                                    const door = this.doors.get(`${mapX},${mapY}`);
                                    if (door && door.open >= 0.95) hit = false;
                                }
                                for (const p of this.pushing) {
                                    if(Math.floor(p.x) === mapX && Math.floor(p.y) === mapY) hit=true;
                                }
                            }
                        }
                    }
                    
                    var perpWallDist, wallX;
                    if (side === 0) {
                        perpWallDist = (mapX - this.posX + (1 - stepX) / 2) / rayDirX;
                        wallX = this.posY + perpWallDist * rayDirY;
                    } else {
                        perpWallDist = (mapY - this.posY + (1 - stepY) / 2) / rayDirY;
                        wallX = this.posX + perpWallDist * rayDirX;
                    }
                    
                    wallX -= Math.floor(wallX);
                    
                    const lineHeight = perpWallDist > 0 ? Math.floor(this.height / perpWallDist) : this.height;
                    const drawStart = Math.max(0, Math.floor(-lineHeight / 2 + this.height / 2));
                    const drawEnd = Math.min(this.height - 1, Math.floor(lineHeight / 2 + this.height / 2));
                    
                    var texX = Math.floor(wallX * this.wallTextureImages[0].width);
                    if (side === 0 && rayDirX > 0) texX = this.wallTextureImages[0].width - texX - 1;
                    if (side === 1 && rayDirY < 0) texX = this.wallTextureImages[0].width - texX - 1;
                    
                    const wallType = this.map[mapY][mapX] - 1;
                    const textureIndex = Math.max(0, wallType % this.wallTextureData.length);
                    const texData = this.wallTextureData[textureIndex];
                    
                    var shade = 1 - Math.min(perpWallDist * 0.03, 0.4);
                    if (side === 1) shade *= 0.9;
                    
                    for (var y = drawStart; y <= drawEnd; y++) {
                        const d = y - this.height / 2 + lineHeight / 2;
                        var texY = Math.floor(d * this.wallTextureImages[0].height / lineHeight);
                        var texIndex = ((texY * this.wallTextureImages[0].width + texX) * 4);
                        
                        var pixelIndex = y * this.width + x;
                        var r = Math.min(255, texData.data[texIndex] * shade * 1.3);
                        var g = Math.min(255, texData.data[texIndex + 1] * shade * 1.3);
                        var b = Math.min(255, texData.data[texIndex + 2] * shade * 1.3);
                        
                        this.screenBuffer[pixelIndex] = (255 << 24) | (b << 16) | (g << 8) | r;
                    }
                    zBuffer[x] = perpWallDist;
                }

                const currentTime = performance.now();
                this.sprites.forEach(sprite => {
                    if (sprite.isFlinching && currentTime - sprite.flinchTime > 200) {
                        sprite.isFlinching = false;
                        sprite.animationFrame = 'idle';
                    }
                });
                
                // Determine sprite animation frame based on enemy state
                this.sprites.forEach(sprite => {
                    if (sprite.health > 0) {
                        if (sprite.isFlinching) {
                            sprite.animationFrame = 'flinch';
                        } else if (sprite.state === 'attacking') {
                            sprite.animationFrame = 'attack';
                        } else {
                            sprite.animationFrame = 'idle';
                        }
                    }
                });
                
                const spriteData = this.preloadedSprites['idle']; // Default to idle
                if (spriteData && spriteData.imageData) {
                    this.spriteImageData = spriteData.imageData;
                }
                
                const allObjects = [...this.sprites.filter(s => s.health > 0), 
                                  ...this.collectibles.filter(c => !c.collected), 
                                  ...this.pushing.map(p => ({x: p.x + 0.5, y: p.y + 0.5, isPushwall: true}))];

                allObjects.sort((a, b) => {
                    const distA = Math.pow(this.posX - a.x, 2) + Math.pow(this.posY - a.y, 2);
                    const distB = Math.pow(this.posX - b.x, 2) + Math.pow(this.posY - b.y, 2);
                    return distB - distA;
                });
                
                allObjects.forEach(obj => {
                    let spriteX = obj.x - this.posX;
                    let spriteY = obj.y - this.posY;
                    let texture = null;
                    let isSprite = false;

                    if (obj.type) {
                        texture = {
                            color: obj.type === 'health' ? 0xFFFF0000 : 
                                  obj.type === 'ammo' ? 0xFFFFFF00 : 
                                  obj.type === 'key' ? 0xFF00FFFF : 
                                  obj.type === 'secret' ? 0xFFFF00FF :
                                  obj.type === 'treasure' ? 0xFFFFD700 : 0xFFFFFFFF,
                            size: 32
                        };
                    } else if (obj.health) {
                        isSprite = true;
                    } else if (obj.isPushwall) {
                        texture = { color: 0xFF884400, size: 64};
                    }

                    if (!isSprite && !texture) return;

                    const invDet = 1.0 / (this.planeX * this.dirY - this.dirX * this.planeY);
                    const transformX = invDet * (this.dirY * spriteX - this.dirX * spriteY);
                    const transformY = invDet * (-this.planeY * spriteX + this.planeX * spriteY);

                    if (transformY > 0) {
                        const spriteScreenX = Math.floor((this.width / 2) * (1 + transformX / transformY));
                        const spriteHeight = Math.abs(Math.floor(this.height / transformY));
                        const drawStartY = Math.max(0, Math.floor(-spriteHeight / 2 + this.height / 2));
                        const drawEndY = Math.min(this.height - 1, Math.floor(spriteHeight / 2 + this.height / 2));
                        const spriteWidth = Math.abs(Math.floor(this.height / transformY));
                        const drawStartX = Math.max(0, Math.floor(-spriteWidth / 2 + spriteScreenX));
                        const drawEndX = Math.min(this.width - 1, Math.floor(spriteWidth / 2 + spriteScreenX));

                        for (var stripe = drawStartX; stripe < drawEndX; stripe++) {
                            if (transformY > 0 && transformY < zBuffer[stripe]) {
                                if (isSprite) {
                                    // Use appropriate sprite frame
                                    const currentSpriteData = this.preloadedSprites[obj.animationFrame || 'idle'];
                                    const spriteImageData = currentSpriteData ? currentSpriteData.imageData : this.spriteImageData;
                                    
                                    var texX = Math.floor(256 * (stripe - (-spriteWidth / 2 + spriteScreenX)) * this.spriteCanvas.width / spriteWidth) / 256;
                                    for (var y = drawStartY; y < drawEndY; y++) {
                                        var d = y - this.height / 2 + spriteHeight / 2;
                                        var texY = Math.floor(d * this.spriteCanvas.height / spriteHeight);
                                        var texIndex = (Math.floor(texY) * this.spriteCanvas.width + Math.floor(texX)) * 4;
                                        var pixelIndex = y * this.width + stripe;
                                        if (spriteImageData.data[texIndex + 3] > 128) {
                                            var r = spriteImageData.data[texIndex];
                                            var g = spriteImageData.data[texIndex + 1];
                                            var b = spriteImageData.data[texIndex + 2];
                                            this.screenBuffer[pixelIndex] = (255 << 24) | (b << 16) | (g << 8) | r;
                                        }
                                    }
                                } else {
                                    for (var y = drawStartY; y < drawEndY; y++) {
                                        this.screenBuffer[y * this.width + stripe] = texture.color;
                                    }
                                }
                            }
                        }
                    }
                });
                
                this.ctx.putImageData(this.screenImageData, 0, 0);
            }
            
            renderFloorAndCeiling() {
                for (var y = 0; y < this.height; y++) {
                    const rayDirX0 = this.dirX - this.planeX;
                    const rayDirY0 = this.dirY - this.planeY;
                    const rayDirX1 = this.dirX + this.planeX;
                    const rayDirY1 = this.dirY + this.planeY;
                    
                    const p = y - this.height / 2;
                    const posZ = 0.5 * this.height;
                    const rowDistance = posZ / p;
                    
                    const floorStepX = rowDistance * (rayDirX1 - rayDirX0) / this.width;
                    const floorStepY = rowDistance * (rayDirY1 - rayDirY0) / this.width;
                    
                    let floorX = this.posX + rowDistance * rayDirX0;
                    let floorY = this.posY + rowDistance * rayDirY0;
                    
                    for (var x = 0; x < this.width; x++) {
                        const cellX = Math.floor(floorX);
                        const cellY = Math.floor(floorY);
                        
                        const tx = Math.floor(this.floorCanvas.width * (floorX - cellX)) & (this.floorCanvas.width - 1);
                        const ty = Math.floor(this.floorCanvas.height * (floorY - cellY)) & (this.floorCanvas.height - 1);
                        
                        const floorIndex = (ty * this.floorCanvas.width + tx) * 4;
                        const floorPixelIndex = y * this.width + x;
                        
                        const ceilingY = this.height - y - 1;
                        const ceilingIndex = (ty * this.ceilingCanvas.width + tx) * 4;
                        const ceilingPixelIndex = ceilingY * this.width + x;
                        
                        const shade = 1.0 - Math.min(rowDistance * 0.02, 0.7);
                        
                        const floorR = this.floorImageData.data[floorIndex] * shade;
                        const floorG = this.floorImageData.data[floorIndex + 1] * shade;
                        const floorB = this.floorImageData.data[floorIndex + 2] * shade;
                        this.screenBuffer[floorPixelIndex] = (255 << 24) | (floorB << 16) | (floorG << 8) | floorR;
                        
                        const ceilingR = this.ceilingImageData.data[ceilingIndex] * shade;
                        const ceilingG = this.ceilingImageData.data[ceilingIndex + 1] * shade;
                        const ceilingB = this.ceilingImageData.data[ceilingIndex + 2] * shade;
                        this.screenBuffer[ceilingPixelIndex] = (255 << 24) | (ceilingB << 16) | (ceilingG << 8) | ceilingR;
                        
                        floorX += floorStepX;
                        floorY += floorStepY;
                    }
                }
            }
            
            renderMinimap() {
                this.minimapCtx.clearRect(0, 0, this.minimapCanvas.width, this.minimapCanvas.height);
                
                const viewRadius = 6;
                const centerX = this.minimapCanvas.width / 2;
                const centerY = this.minimapCanvas.height / 2;
                
                const startX = Math.max(0, Math.floor(this.posX - viewRadius));
                const endX = Math.min(this.map[0].length, Math.ceil(this.posX + viewRadius));
                const startY = Math.max(0, Math.floor(this.posY - viewRadius));
                const endY = Math.min(this.map.length, Math.ceil(this.posY + viewRadius));
                
                const offsetX = centerX - this.posX * this.minimapScale;
                const offsetY = centerY - this.posY * this.minimapScale;
                
                for (var y = startY; y < endY; y++) {
                    for (var x = startX; x < endX; x++) {
                        var screenX = x * this.minimapScale + offsetX;
                        var screenY = y * this.minimapScale + offsetY;
                        
                        var tileColor = '#222';
                        if (this.map[y][x] !== 0) {
                             if (this.map[y][x] === 2) tileColor = '#888800';
                             else if (this.map[y][x] === 3) tileColor = '#8B0000';
                             else if (this.map[y][x] === 4) tileColor = '#444444';
                             else tileColor = '#888';
                        }
                        this.minimapCtx.fillStyle = tileColor;
                        this.minimapCtx.fillRect(
                            screenX,
                            screenY,
                            this.minimapScale,
                            this.minimapScale
                        );
                    }
                }
                
                // Draw enemies with state-based colors
                this.sprites.forEach(sprite => {
                    if (sprite.health <= 0) return;
                    if (Math.abs(sprite.x - this.posX) <= viewRadius &&
                        Math.abs(sprite.y - this.posY) <= viewRadius) {
                        var screenX = sprite.x * this.minimapScale + offsetX;
                        var screenY = sprite.y * this.minimapScale + offsetY;
                        
                        var color = 'blue'; // patrolling/idle
                        if (sprite.state === 'alerted') color = 'red';
                        else if (sprite.state === 'attacking') color = 'darkred';
                        else if (sprite.state === 'investigating') color = 'orange';
                        
                        this.minimapCtx.fillStyle = color;
                        this.minimapCtx.beginPath();
                        this.minimapCtx.arc(screenX, screenY, 3, 0, 2 * Math.PI);
                        this.minimapCtx.fill();
                        
                        // Draw enemy facing direction for alerted/attacking enemies
                        if (sprite.state === 'alerted' || sprite.state === 'attacking') {
                            this.minimapCtx.strokeStyle = color;
                            this.minimapCtx.lineWidth = 1;
                            this.minimapCtx.beginPath();
                            this.minimapCtx.moveTo(screenX, screenY);
                            this.minimapCtx.lineTo(
                                screenX + Math.cos(sprite.dir) * 8,
                                screenY + Math.sin(sprite.dir) * 8
                            );
                            this.minimapCtx.stroke();
                        }
                    }
                });
                
                // Draw collectibles
                this.collectibles.forEach(item => {
                    if (item.collected) return;
                    if (Math.abs(item.x - this.posX) <= viewRadius &&
                        Math.abs(item.y - this.posY) <= viewRadius) {
                        var screenX = item.x * this.minimapScale + offsetX;
                        var screenY = item.y * this.minimapScale + offsetY;
                        
                        var color = 'white';
                        if (item.type === 'health') color = 'red';
                        else if (item.type === 'ammo') color = 'yellow';
                        else if (item.type === 'key') color = 'cyan';
                        else if (item.type === 'secret') color = 'magenta';
                        else if (item.type === 'treasure') color = 'gold';
                        
                        this.minimapCtx.fillStyle = color;
                        this.minimapCtx.fillRect(screenX - 1, screenY - 1, 2, 2);
                    }
                });
                
                // Draw player
                this.minimapCtx.fillStyle = 'red';
                this.minimapCtx.beginPath();
                this.minimapCtx.arc(centerX, centerY, 4, 0, 2 * Math.PI);
                this.minimapCtx.fill();
                
                // Draw player direction
                this.minimapCtx.strokeStyle = 'yellow';
                this.minimapCtx.lineWidth = 2;
                this.minimapCtx.beginPath();
                this.minimapCtx.moveTo(centerX, centerY);
                this.minimapCtx.lineTo(
                    centerX + this.dirX * this.minimapScale * 0.8,
                    centerY + this.dirY * this.minimapScale * 0.8
                );
                this.minimapCtx.stroke();
                
                // FOV visualization
                const fovAngle = Math.PI * 0.33; // 60 degrees
                this.minimapCtx.strokeStyle = 'rgba(255, 255, 0, 0.3)';
                this.minimapCtx.lineWidth = 1;
                const playerAngle = Math.atan2(this.dirY, this.dirX);
                
                this.minimapCtx.beginPath();
                this.minimapCtx.moveTo(centerX, centerY);
                this.minimapCtx.lineTo(
                    centerX + Math.cos(playerAngle - fovAngle/2) * this.minimapScale * 2,
                    centerY + Math.sin(playerAngle - fovAngle/2) * this.minimapScale * 2
                );
                this.minimapCtx.moveTo(centerX, centerY);
                this.minimapCtx.lineTo(
                    centerX + Math.cos(playerAngle + fovAngle/2) * this.minimapScale * 2,
                    centerY + Math.sin(playerAngle + fovAngle/2) * this.minimapScale * 2
                );
                this.minimapCtx.stroke();
            }

            render() {
                this.renderRaycast();
                this.renderMinimap();
                this.updateHUD();
                this.updateDebugInfo();
            }

            loop() {
                var step = 1000 / 60;
                var accumulator = 0;
                var lastTime = performance.now();

                const gameLoop = (currentTime) => {
                    if (this.playerStats.isDead || this.levelStats.finished) {
                        return;
                    }

                    const deltaTime = currentTime - lastTime;
                    lastTime = currentTime;
                    accumulator += deltaTime;
                    
                    // Calculate FPS
                    this.frameCount++;
                    if (currentTime - this.lastFrameTime >= 1000) {
                        this.fps = this.frameCount;
                        this.frameCount = 0;
                        this.lastFrameTime = currentTime;
                    }

                    while (accumulator >= step) {
                        this.update(step);
                        accumulator -= step;
                    }
                    
                    this.render();
                    requestAnimationFrame(gameLoop);
                    
                    // Check win condition
                    if (this.sprites.filter(s => s.health > 0).length === 0 && !this.levelStats.finished) {
                        this.levelStats.finished = true;
                        this.levelStats.endedAt = Date.now();
                        this._showLevelStats();
                    }
                };
                
                requestAnimationFrame(gameLoop);
            }
        }
        
        const engine = new RaycastEngine();
        
        function toggleDebug() {
            const panel = document.getElementById('debug-panel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            engine.debugMode = panel.style.display === 'block';
        }
    </script>
</body>
</html>