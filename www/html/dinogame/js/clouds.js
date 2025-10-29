(function(root){
  function addCloud(gameElem, cloudsRef, spriteLoadStatus, sprites) {
    var cloud = { x: gameElem.offsetWidth + Math.random() * 100, y: 110 + Math.random() * 60, width: 46, height: 14, elem: document.createElement('div') };
    cloud.elem.className = 'cloud';
    if (spriteLoadStatus.cloud){ cloud.elem.style.backgroundImage = "url('" + sprites.cloud + "')"; }
    else { cloud.elem.classList.add('fallback', 'cloud'); cloud.elem.setAttribute('data-label', '☁'); }
    cloud.elem.style.width = cloud.width + 'px';
    cloud.elem.style.height = cloud.height + 'px';
    cloud.elem.style.left = cloud.x + 'px';
    cloud.elem.style.top = cloud.y + 'px';
    cloud.elem.style.position = 'absolute';
    cloud.elem.style.zIndex = 0;
    gameElem.appendChild(cloud.elem);
    cloudsRef().push(cloud);
  }

  function updateClouds(_gameElem, cloudsRef, dt, speed, addCloudFn) {
    var arr = cloudsRef();
    for (var i = arr.length - 1; i >= 0; i--){
      var c = arr[i];
      c.x -= speed * dt * 0.2;
      c.elem.style.left = c.x + 'px';
      if (c.x + c.width < 0){
        c.elem.remove();
        arr.splice(i, 1);
        if (Math.random() < 0.7) addCloudFn();
      }
    }
  }

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { addCloud, updateClouds };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.addCloud = addCloud;
    root.DinoGame.updateClouds = updateClouds;
  }
})(typeof window !== 'undefined' ? window : global);
