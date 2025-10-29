(function(root){
  const { SPRITE_POS, GRAVITY, MAX_JUMP_HOLD } = (typeof module !== 'undefined' && module.exports) ? require('./constants.js') : root.DinoGame;

  function updateDino(state, elements, dt, jumpInfo) {
    var d = state.get().dino;
    if (d.isJumping){
      // Improved jump hold: reduce gravity effect instead of adding velocity
      var gravityMultiplier = (jumpInfo.jumpKeyHeld && jumpInfo.heldMs < MAX_JUMP_HOLD && d.velocity > 0) ? 0.5 : 1.0;
      d.velocity -= GRAVITY * dt * gravityMultiplier;
      d.y += d.velocity * dt;
      if (d.y <= 0){ d.y = 0; d.velocity = 0; d.isJumping = false; }
    }
    d.width = (d.isDucking && !d.isJumping) ? 60 : 44;
    d.height = (d.isDucking && !d.isJumping) ? 25 : 47;
    elements.dino.style.left = d.x + 'px';
    elements.dino.style.bottom = d.y + 'px';
    elements.dino.style.width = d.width + 'px';
    elements.dino.style.height = d.height + 'px';
  }

  function animateDino(state, elements, dt, spriteLoaded) {
    var d = state.get().dino;
    d.frameTimer += dt;
    if (!d.isJumping && !d.isDucking && spriteLoaded){
      if (d.frameTimer > 80){ d.frameIndex = (d.frameIndex + 1) % 2; d.frameTimer = 0; }
      elements.dino.style.backgroundPosition = d.frameIndex === 0 ? SPRITE_POS.run1 : SPRITE_POS.run2;
    } else if (d.isDucking && !d.isJumping && spriteLoaded){
      elements.dino.style.backgroundPosition = SPRITE_POS.duck;
    } else if (d.isJumping && spriteLoaded){
      elements.dino.style.backgroundPosition = SPRITE_POS.jump;
    }
  }

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { updateDino, animateDino };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.updateDino = updateDino;
    root.DinoGame.animateDino = animateDino;
  }
})(typeof window !== 'undefined' ? window : global);
