(function(root){
  const { ASSET_PATH, SPRITES } = (typeof module !== 'undefined' && module.exports) ? require('./constants.js') : root.DinoGame;
  const { loadImage, loadAudio } = (typeof module !== 'undefined' && module.exports) ? require('./utils.js') : root.DinoGame;

  function preloadAssets() {
    var images = [SPRITES.dino, SPRITES.obstacle, SPRITES.pterodactyl, SPRITES.cloud, SPRITES.ground].map(loadImage);
    var audio = [ASSET_PATH + 'jump.wav', ASSET_PATH + 'score.wav', ASSET_PATH + 'gameover.wav'].map(loadAudio);
    return Promise.all(images.concat(audio));
  }

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { preloadAssets };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.preloadAssets = preloadAssets;
  }
})(typeof window !== 'undefined' ? window : global);
