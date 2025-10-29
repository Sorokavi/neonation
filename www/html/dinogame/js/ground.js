(function(root){
  function setupGround(gameElem, tilesRef, spriteLoadStatus, sprites) {
    for (var g of tilesRef()) { if (g.elem && g.elem.remove) g.elem.remove(); }
    var tileWidth = 60;
    var count = Math.ceil(gameElem.offsetWidth / tileWidth) + 2;
    var newTiles = [];
    for (var i = 0; i < count; i++){
      var ground = { x: i * tileWidth, y: 0, width: tileWidth, height: 20, elem: document.createElement('div') };
      ground.elem.className = 'ground';
      if (spriteLoadStatus.ground){
        ground.elem.style.backgroundImage = "url('" + sprites.ground + "')";
        ground.elem.style.backgroundPosition = '0 0';
      } else {
        ground.elem.classList.add('fallback');
        ground.elem.setAttribute('data-label', '_');
      }
      ground.elem.style.width = ground.width + 'px';
      ground.elem.style.height = ground.height + 'px';
      ground.elem.style.left = ground.x + 'px';
      ground.elem.style.bottom = '0px';
      ground.elem.style.position = 'absolute';
      ground.elem.style.zIndex = 1;
      gameElem.appendChild(ground.elem);
      newTiles.push(ground);
    }
    var ref = tilesRef(); while (ref.length) ref.pop(); for (var t of newTiles) ref.push(t);
  }

  function updateGround(tilesRef, dt, speed) {
    var tiles = tilesRef();
    for (var g of tiles){
      g.x -= speed * dt;
      if (g.x + g.width < 0) g.x += tiles.length * g.width;
      g.elem.style.left = g.x + 'px';
    }
  }

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { setupGround, updateGround };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.setupGround = setupGround;
    root.DinoGame.updateGround = updateGround;
  }
})(typeof window !== 'undefined' ? window : global);
