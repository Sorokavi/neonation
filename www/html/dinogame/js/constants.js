(function(root){
  const ASSET_PATH = './assets/';
  const SPRITES = {
    dino: ASSET_PATH + 'dino.png',
    obstacle: ASSET_PATH + 'obstacle.png',
    pterodactyl: ASSET_PATH + 'pterodactyl.png',
    cloud: ASSET_PATH + 'cloud.png',
    ground: ASSET_PATH + 'tiles.gif'
  };
  const SPRITE_POS = {
    run1: '-133px -2px',
    run2: '-180px -2px',
    jump: '-233px -2px',
    duck: '-281px -2px'
  };
  const GRAVITY = 0.0018;
  const JUMP_VELOCITY = 0.6;
  const MAX_JUMP_HOLD = 250;
  const BASE_SPEED = 0.35;

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { ASSET_PATH, SPRITES, SPRITE_POS, GRAVITY, JUMP_VELOCITY, MAX_JUMP_HOLD, BASE_SPEED };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.ASSET_PATH = ASSET_PATH;
    root.DinoGame.SPRITES = SPRITES;
    root.DinoGame.SPRITE_POS = SPRITE_POS;
    root.DinoGame.GRAVITY = GRAVITY;
    root.DinoGame.JUMP_VELOCITY = JUMP_VELOCITY;
    root.DinoGame.MAX_JUMP_HOLD = MAX_JUMP_HOLD;
    root.DinoGame.BASE_SPEED = BASE_SPEED;
  }
})(typeof window !== 'undefined' ? window : global);
