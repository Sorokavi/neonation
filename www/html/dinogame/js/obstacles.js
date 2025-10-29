(function(root){
  const { SPRITES } = (typeof module !== 'undefined' && module.exports) ? require('./constants.js') : root.DinoGame;

  function makeCactus(gameElem, spriteLoadStatus, type, xOffset) {
    var isTall = type === 'tall';
    var obs = { x: gameElem.offsetWidth + (xOffset || 0), y: 0, width: isTall ? 40 : 28, height: isTall ? 80 : 56, elem: document.createElement('div'), cactusType: type };
    obs.elem.className = 'obstacle';
    if (spriteLoadStatus.obstacle){
      obs.elem.style.backgroundImage = "url('" + SPRITES.obstacle + "')";
      obs.elem.style.backgroundPosition = isTall ? '0 -40px' : '0 0';
      obs.elem.style.backgroundSize = 'auto';
    } else {
      obs.elem.classList.add('fallback', 'obstacle');
      obs.elem.setAttribute('data-label', isTall ? '|' : 'l');
      obs.elem.style.background = isTall ? '#497c3a' : '#6ab04c';
      obs.elem.style.borderRadius = '4px';
    }
    obs.elem.style.width = obs.width + 'px';
    obs.elem.style.height = obs.height + 'px';
    obs.elem.style.left = obs.x + 'px';
    obs.elem.style.bottom = obs.y + 'px';
    gameElem.appendChild(obs.elem);
    return obs;
  }

  function makePterodactyl(gameElem, spriteLoadStatus, y) {
    var obs = { x: gameElem.offsetWidth, y: y, width: 46, height: 40, elem: document.createElement('div'), isPterodactyl: true, frameIndex: 0, frameTimer: 0 };
    obs.elem.className = 'pterodactyl';
    if (spriteLoadStatus.pterodactyl){ obs.elem.style.backgroundImage = "url('" + SPRITES.pterodactyl + "')"; }
    else { obs.elem.classList.add('fallback', 'pterodactyl'); obs.elem.setAttribute('data-label', '~'); obs.elem.style.background = 'linear-gradient(90deg, #888 60%, #fff 100%)'; obs.elem.style.borderRadius = '50% 50% 40% 40%/60% 60% 40% 40%'; }
    obs.elem.style.width = obs.width + 'px';
    obs.elem.style.height = obs.height + 'px';
    obs.elem.style.left = obs.x + 'px';
    obs.elem.style.bottom = obs.y + 'px';
    obs.elem.style.position = 'absolute';
    obs.elem.style.zIndex = 2;
    gameElem.appendChild(obs.elem);
    return obs;
  }

  function createObstacle(state, gameElem, spriteLoadStatus) {
    var now = performance.now();
    if (now - state.get().gameStartTime < 2500){ state.setObstacleTimer(300); return; }

    var score = state.get().score;
    var obstacles = state.refs.obstacles();

    var doCombo = false;
    if (score > 10 && Math.random() < 0.4) doCombo = true;

    if (doCombo){
      var comboType = Math.floor(Math.random() * 4);
      switch(comboType){
        case 0: {
          var cactus = makeCactus(gameElem, spriteLoadStatus, 'small', 0);
          obstacles.push(cactus);
          var gap0 = 85 + Math.random() * 25;
          var lowPt = { x: cactus.x + cactus.width + gap0, y: 70 };
          var p0 = makePterodactyl(gameElem, spriteLoadStatus, lowPt.y);
          p0.x = lowPt.x; p0.elem.style.left = p0.x + 'px';
          obstacles.push(p0);
          return;
        }
        case 1: {
          var cactusT = makeCactus(gameElem, spriteLoadStatus, 'tall', 0); obstacles.push(cactusT);
          var gap1 = 100 + Math.random() * 35; var highPt = { x: cactusT.x + cactusT.width + gap1, y: 120 };
          var p1 = makePterodactyl(gameElem, spriteLoadStatus, highPt.y); p1.x = highPt.x; p1.elem.style.left = p1.x + 'px'; obstacles.push(p1); return;
        }
        case 2: {
          var lastX = gameElem.offsetWidth; for (var i = 0; i < 2; i++){ var c = makeCactus(gameElem, spriteLoadStatus, 'small', 0); c.x = lastX; c.elem.style.left = c.x + 'px'; obstacles.push(c); lastX += c.width + 10 + Math.random() * 10; }
          var gap2 = 80 + Math.random() * 25; var midPt = makePterodactyl(gameElem, spriteLoadStatus, 90); midPt.x = lastX + gap2; midPt.elem.style.left = midPt.x + 'px'; obstacles.push(midPt); return;
        }
        case 3: {
          var last = gameElem.offsetWidth; for (var j = 0; j < 3; j++){ var c3 = makeCactus(gameElem, spriteLoadStatus, 'small', 0); c3.x = last; c3.elem.style.left = c3.x + 'px'; obstacles.push(c3); last += c3.width + 8 + Math.random() * 8; }
          var gap3 = 80 + Math.random() * 20; var low = makePterodactyl(gameElem, spriteLoadStatus, 65); low.x = last + gap3; low.elem.style.left = low.x + 'px'; obstacles.push(low); return;
        }
        default: break;
      }
    }

    var type = 'obstacle';
    var cactusType = 'small';
    var cactusStack = 1;
    if (score > 10){
      var r = Math.random();
      if (r < 0.18) type = 'pterodactyl';
      else if (r < 0.32) { type = 'obstacle'; cactusType = 'tall'; }
      else if (r < 0.48) { type = 'obstacle'; cactusType = 'small'; cactusStack = 2; }
      else if (r < 0.60) { type = 'obstacle'; cactusType = 'small'; cactusStack = 3; }
    } else {
      if (Math.random() < 0.2) cactusType = 'tall';
      if (Math.random() < 0.15) cactusStack = 2;
      if (Math.random() < 0.08) cactusStack = 3;
    }

    if (type === 'obstacle'){
      for (var k = 0; k < cactusStack; k++){
        var isTall = cactusType === 'tall';
        var offset = k * (isTall ? 44 : 32);
        var o = makeCactus(gameElem, spriteLoadStatus, cactusType, offset);
        obstacles.push(o);
      }
    } else {
      var yOptions = [60, 90, 120];
      var p = makePterodactyl(gameElem, spriteLoadStatus, yOptions[Math.floor(Math.random() * yOptions.length)]);
      obstacles.push(p);
    }

    if (score >= 30 && Math.random() < 0.35){
      setTimeout(() => { createObstacle(state, gameElem, spriteLoadStatus); }, 180 + Math.random() * 220);
    }
  }

  function updateObstacles(state, dt, speed, onScore, playWingAnim) {
    var obsArr = state.refs.obstacles();
    for (var i = obsArr.length - 1; i >= 0; i--){
      var obs = obsArr[i];
      obs.x -= speed * dt; obs.elem.style.left = obs.x + 'px';
      if (obs.isPterodactyl){ playWingAnim(obs, dt); }

      var d = state.get().dino;
      // hitbox padding
      var padding = 4;
      var dinoLeft = d.x + padding;
      var dinoRight = d.x + d.width - padding;
      var dinoBottom = d.y + padding;
      var dinoTop = d.y + d.height - padding;
      var obsLeft = obs.x + padding;
      var obsRight = obs.x + obs.width - padding;
      var obsBottom = obs.y + padding;
      var obsTop = obs.y + obs.height - padding;
      
      if (dinoLeft < obsRight && dinoRight > obsLeft && dinoBottom < obsTop && dinoTop > obsBottom){
        return 'hit';
      }
      if (obs.x + obs.width < 0){ obs.elem.remove(); obsArr.splice(i, 1); onScore(); }
    }
    return null;
  }

  function animatePterodactyl(obs, dt) {
    obs.frameTimer += dt;
    if (obs.frameTimer > 100){ obs.frameIndex = 1 - obs.frameIndex; obs.frameTimer = 0; obs.elem.style.backgroundPosition = obs.frameIndex === 0 ? '0 0' : '0 -40px'; }
  }

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { createObstacle, updateObstacles, animatePterodactyl };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.createObstacle = createObstacle;
    root.DinoGame.updateObstacles = updateObstacles;
    root.DinoGame.animatePterodactyl = animatePterodactyl;
  }
})(typeof window !== 'undefined' ? window : global);
