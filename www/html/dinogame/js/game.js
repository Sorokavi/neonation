(function(root){
  const { SPRITES, SPRITE_POS, BASE_SPEED, MAX_JUMP_HOLD, ASSET_PATH } = (typeof module !== 'undefined' && module.exports) ? require('./constants.js') : root.DinoGame;
  const { playSound, isMobile, isTouchDevice, setButtonPressed } = (typeof module !== 'undefined' && module.exports) ? require('./utils.js') : root.DinoGame;
  const { setupParallax, updateParallax } = (typeof module !== 'undefined' && module.exports) ? require('./parallax.js') : root.DinoGame;
  const { setupGround, updateGround } = (typeof module !== 'undefined' && module.exports) ? require('./ground.js') : root.DinoGame;
  const { addCloud, updateClouds } = (typeof module !== 'undefined' && module.exports) ? require('./clouds.js') : root.DinoGame;
  const { createObstacle, updateObstacles, animatePterodactyl } = (typeof module !== 'undefined' && module.exports) ? require('./obstacles.js') : root.DinoGame;
  const { updateDino, animateDino } = (typeof module !== 'undefined' && module.exports) ? require('./dino.js') : root.DinoGame;
  const { preloadAssets } = (typeof module !== 'undefined' && module.exports) ? require('./preload.js') : root.DinoGame;
  const { createState } = (typeof module !== 'undefined' && module.exports) ? require('./state.js') : root.DinoGame;

  function start() {
    var elements = {
      game: document.getElementById('game'),
      dino: document.getElementById('dino'),
      score: document.getElementById('score'),
      high: document.getElementById('highscore'),
      gameover: document.getElementById('gameover'),
      restart: document.getElementById('restart-btn'),
      loader: document.getElementById('loader'),
      jumpBtn: document.getElementById('jump-btn'),
      duckBtn: document.getElementById('duck-btn'),
      touch: document.getElementById('touch-controls'),
      startscreen: document.getElementById('startscreen'),
      startBtn: document.getElementById('start-btn')
    };

  var spriteLoadStatus = { dino: false, obstacle: false, pterodactyl: false, cloud: false, ground: false };
  var sounds = { jump: new Audio(ASSET_PATH + 'jump.wav'), score: new Audio(ASSET_PATH + 'score.wav'), gameover: new Audio(ASSET_PATH + 'gameover.wav') };

    var state = createState(elements, spriteLoadStatus);

    var setScoresUi = function(){ elements.score.textContent = String(state.get().score).padStart(5, '0'); elements.high.textContent = 'HI ' + String(state.get().highScore).padStart(5, '0'); };
    var updateScore = function(val){ state.setScore(val); if (state.get().score > state.get().highScore){ state.setHighScore(state.get().score); try{ localStorage.setItem('dinoHighScore', state.get().highScore); } catch(_e){} } setScoresUi(); };

    var showStart = function(){ elements.startscreen.style.display = 'flex'; elements.loader.style.display = 'none'; elements.gameover.style.display = 'none'; state.setGameStarted(false); };
    var hideStart = function(){ elements.startscreen.style.display = 'none'; state.setGameStarted(true); };

    var prepareDinoSprite = function(){
      if (spriteLoadStatus.dino){ elements.dino.style.backgroundImage = "url('" + SPRITES.dino + "')"; elements.dino.classList.remove('fallback', 'dino'); }
      else { elements.dino.classList.add('fallback', 'dino'); }
    };

    var resetGame = function(){
      setupParallax(elements.game, state.refs.parallaxLayers);
      state.reset(BASE_SPEED);
      updateScore(0);
      elements.gameover.style.display = 'none';
      prepareDinoSprite();
      var cloudsRef = state.refs.clouds(); while (cloudsRef.length) cloudsRef.pop();
      for (var i = 0; i < 3; i++){ addCloud(elements.game, state.refs.clouds, spriteLoadStatus, SPRITES); }
      setupGround(elements.game, state.refs.groundTiles, spriteLoadStatus, SPRITES);
      elements.touch.style.display = (isMobile() ? 'flex' : 'none');
      var now = performance.now(); state.setLastTime(now); state.setGameStartTime(now);
      requestAnimationFrame(gameLoop);
    };

    function onReady() {
      elements.loader.style.display = 'none';
      var hi = parseInt(localStorage.getItem('dinoHighScore') || '0', 10) || 0;
      state.setHighScore(hi);
      setScoresUi();
      setupParallax(elements.game, state.refs.parallaxLayers);
      showStart();
    }

    function startGame() { hideStart(); resetGame(); }

    // Input handling
    document.addEventListener('keydown', (e) => {
      var code = e.code || e.key;
      if (!state.get().gameStarted && (code === 'Space' || code === ' ')){ startGame(); e.preventDefault(); return; }
  if (state.get().gameStarted && (code === 'Space' || code === ' ')){ if (state.startJump(performance.now())) playSound(sounds.jump); }
      if (state.get().gameStarted && code === 'ArrowDown'){ state.setDucking(true); }
    });
    document.addEventListener('keyup', (e) => { if (!state.get().gameStarted) return; var code = e.code || e.key; if (code === 'Space' || code === ' '){ state.endJump(); } if (code === 'ArrowDown'){ state.setDucking(false); } });
    elements.startBtn.addEventListener('click', startGame);
    if (isMobile()){
      elements.startscreen.addEventListener('touchstart', (e) => { if (!state.get().gameStarted){ startGame(); e.preventDefault(); } });
    }
    elements.restart.addEventListener('click', resetGame);

    // Touch buttons
    elements.jumpBtn.addEventListener('touchstart', (e) => { if (state.startJump(performance.now())){} setButtonPressed(elements.jumpBtn, true); e.preventDefault(); });
    elements.jumpBtn.addEventListener('touchend', (e) => { state.endJump(); setButtonPressed(elements.jumpBtn, false); e.preventDefault(); });
    elements.duckBtn.addEventListener('touchstart', (e) => { state.setDucking(true); setButtonPressed(elements.duckBtn, true); e.preventDefault(); });
    elements.duckBtn.addEventListener('touchend', (e) => { state.setDucking(false); setButtonPressed(elements.duckBtn, false); e.preventDefault(); });

    function updateTouchControlsVisibility() { elements.touch.style.display = (isMobile() || isTouchDevice()) ? 'flex' : 'none'; }
    window.addEventListener('resize', updateTouchControlsVisibility); updateTouchControlsVisibility();

    // Main loop
   function gameLoop(currentTime) {
      var s = state.get();
      var dt = Math.min(currentTime - s.lastTime, 33); // Cap dt to prevent huge jumps
      state.setLastTime(currentTime);
      if (s.gameOver) return;

      updateParallax(state.refs.parallaxLayers, elements.game, s.speed, dt);
      updateClouds(elements.game, state.refs.clouds, dt, s.speed, 
        () => { addCloud(elements.game, state.refs.clouds, spriteLoadStatus, SPRITES); });
      updateGround(state.refs.groundTiles, dt, s.speed);

      var jumpInfo = state.getJumpHoldInfo(performance.now());
      updateDino(state, elements, dt, jumpInfo);
      animateDino(state, elements, dt, spriteLoadStatus.dino);

  var result = updateObstacles(state, dt, s.speed, () => {
    updateScore(state.get().score + 1);
    playSound(sounds.score);
    if (state.get().score % 5 === 0) state.setSpeed(state.get().speed + 0.01);
  }, animatePterodactyl);
  if (result === 'hit') {
    s.gameOver = true;
    elements.gameover.style.display = 'flex';
    playSound(sounds.gameover);
    return;
  }

      state.setObstacleTimer(s.obstacleTimer - dt);
      if (state.get().obstacleTimer <= 0){
        createObstacle(state, elements.game, spriteLoadStatus);
        var minInt = Math.max(200, 1000 - Math.floor(state.get().score / 5) * 100);
        var maxInt = Math.max(350, 1400 - Math.floor(state.get().score / 5) * 150);
        state.setObstacleTimer(minInt + Math.random() * (maxInt - minInt));
      }
      requestAnimationFrame(gameLoop);
    };

    // Preload assets
    preloadAssets().then((results) => {
      var bySrc = {}; for (var r of results){ bySrc[r.src] = r.ok; }
      // Mark sprite availability
      spriteLoadStatus.dino = !!bySrc[SPRITES.dino];
      spriteLoadStatus.obstacle = !!bySrc[SPRITES.obstacle];
      spriteLoadStatus.pterodactyl = !!bySrc[SPRITES.pterodactyl];
      spriteLoadStatus.cloud = !!bySrc[SPRITES.cloud];
      spriteLoadStatus.ground = !!bySrc[SPRITES.ground];
    }).catch(() => { /* fallback render */ }).finally(() => { onReady(); });
  }

  if (typeof module !== 'undefined' && module.exports){ module.exports = { start }; }
  else { root.DinoGame = root.DinoGame || {}; root.DinoGame.start = start; }
})(typeof window !== 'undefined' ? window : global);
