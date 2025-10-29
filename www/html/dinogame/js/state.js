(function(root){
  const { SPRITE_POS, JUMP_VELOCITY } = (typeof module !== 'undefined' && module.exports) ? require('./constants.js') : root.DinoGame;

  function createState(elements, spriteLoadStatus) {
    // Private state held inside closure; adhere to rule: use var inside functions
    var dino = null;
    var obstacles = [];
    var clouds = [];
    var groundTiles = [];
    var parallaxLayers = [];
    var score = 0;
    var highScore = 0;
    var speed = 0;
    var gameOver = false;
    var lastTime = 0;
    var obstacleTimer = 0;
    var gameStartTime = 0;
    var gameStarted = false;
    var jumpKeyHeld = false;
    var jumpStartTime = 0;

    const initDino = function(){
      dino = {
        x: 50, y: 0,
        width: 44, height: 47,
        velocity: 0,
        isJumping: false,
        isDucking: false,
        frameIndex: 0,
        frameTimer: 0,
        isBlinking: false,
        blinkTimer: 0
      };
      return dino;
    };

    const setScore = function(val){ score = val; return score; };
    const incScore = function(){ score += 1; return score; };

    const setHighScore = function(val){ highScore = val; return highScore; };

    const setSpeed = function(val){ speed = val; return speed; };

    const markGameOver = function(val){ gameOver = !!val; return gameOver; };

    const setLastTime = function(t){ lastTime = t; return lastTime; };

    const setObstacleTimer = function(t){ obstacleTimer = t; return obstacleTimer; };

    const setGameStartTime = function(t){ gameStartTime = t; return gameStartTime; };

    const setGameStarted = function(val){ gameStarted = !!val; return gameStarted; };

    const startJump = function(now){ if (gameOver || dino.isJumping) return false; dino.isJumping = true; dino.velocity = JUMP_VELOCITY; jumpKeyHeld = true; jumpStartTime = now; return true; };
    const endJump = function(){ jumpKeyHeld = false; };

    const setDucking = function(val){ dino.isDucking = !!val; };

    const getJumpHoldInfo = function(now){ return { jumpKeyHeld: jumpKeyHeld, heldMs: now - jumpStartTime }; };

    const get = function(){
      return {
        dino: dino,
        obstacles: obstacles,
        clouds: clouds,
        groundTiles: groundTiles,
        parallaxLayers: parallaxLayers,
        score: score,
        highScore: highScore,
        speed: speed,
        gameOver: gameOver,
        lastTime: lastTime,
        obstacleTimer: obstacleTimer,
        gameStartTime: gameStartTime,
        gameStarted: gameStarted
      };
    };

    const reset = function(baseSpeed){
      obstacles.forEach(function(o){ if (o.elem && o.elem.remove) o.elem.remove(); });
      clouds.forEach(function(c){ if (c.elem && c.elem.remove) c.elem.remove(); });
      groundTiles.forEach(function(g){ if (g.elem && g.elem.remove) g.elem.remove(); });
      obstacles = [];
      clouds = [];
      groundTiles = [];
      parallaxLayers = [];
      score = 0;
      speed = baseSpeed;
      obstacleTimer = 0;
      gameOver = false;
      jumpKeyHeld = false;
      jumpStartTime = 0;
      initDino();
      return get();
    };

    return {
      get: get,
      reset: reset,
      initDino: initDino,
      setScore: setScore,
      incScore: incScore,
      setHighScore: setHighScore,
      setSpeed: setSpeed,
      markGameOver: markGameOver,
      setLastTime: setLastTime,
      setObstacleTimer: setObstacleTimer,
      setGameStartTime: setGameStartTime,
      setGameStarted: setGameStarted,
      startJump: startJump,
      endJump: endJump,
      setDucking: setDucking,
      getJumpHoldInfo: getJumpHoldInfo,
      // expose references to collections for mutation by systems
      refs: {
        obstacles: function(){ return obstacles; },
        clouds: function(){ return clouds; },
        groundTiles: function(){ return groundTiles; },
        parallaxLayers: function(){ return parallaxLayers; }
      }
    };
  }

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { createState };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.createState = createState;
  }
})(typeof window !== 'undefined' ? window : global);
