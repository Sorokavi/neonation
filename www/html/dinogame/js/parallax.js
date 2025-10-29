(function(root){
  function setupParallax(gameElem, layersRef) {
    // Clear old
    for (var l of layersRef()) { if (l.elem && l.elem.remove) l.elem.remove(); }
    var layers = [];
    var hillColors = ['#a3cfa3', '#5e8c3a', '#3d6b2f'];
    var hillHeights = [32, 48, 70];
    var hillSpeeds = [0.04, 0.08, 0.13];
    for (var i = 0; i < 3; i++) {
      var layer = {
        x: 0,
        y: 0,
        width: gameElem.offsetWidth * 2,
        height: hillHeights[i],
        speed: hillSpeeds[i],
        elem: document.createElement('canvas')
      };
      layer.elem.width = layer.width;
      layer.elem.height = layer.height;
      layer.elem.style.position = 'absolute';
      layer.elem.style.left = '0px';
      layer.elem.style.bottom = (20 + (i === 0 ? 60 : i === 1 ? 30 : 0)) + 'px';
      layer.elem.style.width = layer.width + 'px';
      layer.elem.style.height = layer.height + 'px';
      layer.elem.style.zIndex = 0;
      layer.elem.style.pointerEvents = 'none';
      var ctx = layer.elem.getContext('2d');
      ctx.fillStyle = hillColors[i];
      ctx.beginPath();
      ctx.moveTo(0, layer.height);
      for (var x = 0; x <= layer.width; x += 1) {
        var freq = i === 0 ? 0.008 : i === 1 ? 0.012 : 0.018;
        var amp = i === 0 ? 8 : i === 1 ? 14 : 22;
        var y = layer.height - (Math.sin(x * freq) * amp + (i === 0 ? 6 : i === 1 ? 10 : 18));
        ctx.lineTo(x, y);
      }
      ctx.lineTo(layer.width, layer.height);
      ctx.lineTo(0, layer.height);
      ctx.closePath();
      ctx.fill();
      gameElem.appendChild(layer.elem);
      layers.push(layer);
    }
    // Replace reference contents
    var ref = layersRef();
    while (ref.length) ref.pop();
    for (var nl of layers) ref.push(nl);
  }

  function updateParallax(layersRef, gameElem, speed, dt) {
    for (var layer of layersRef()){
      layer.x -= speed * dt * layer.speed;
      if (layer.x < -gameElem.offsetWidth) layer.x += gameElem.offsetWidth;
      layer.elem.style.left = layer.x + 'px';
    }
  }

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { setupParallax, updateParallax };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.setupParallax = setupParallax;
    root.DinoGame.updateParallax = updateParallax;
  }
})(typeof window !== 'undefined' ? window : global);
