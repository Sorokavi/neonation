(function(root){
  function playSound(sound) {
    if (!sound) return; // Avoid throwing in browsers blocking autoplay
    try { sound.currentTime = 0; sound.play().catch(() => {}); } catch(_e) { /* playback may be blocked */ }
  }

  function isMobile() {
    return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) || window.innerWidth < 700;
  }

  function isTouchDevice() {
    return ('ontouchstart' in window) || (navigator.maxTouchPoints ?? 0) > 0;
  }

  function setButtonPressed(btn, pressed) {
    if (!btn) return;
    if (pressed) btn.classList.add('pressed');
    else btn.classList.remove('pressed');
  }

  // Simple promise-based preloader for images/audio with then/catch/finally
  function loadImage(src) { return new Promise((resolve) => {
    var img = new Image();
    img.onload = function(){ resolve({ src: src, ok: true }); };
    img.onerror = function(){ resolve({ src: src, ok: false }); }; // resolve to allow fallback
    img.src = src;
  }); }

  function loadAudio(src) { return new Promise((resolve) => {
    var audio = new Audio();
    var done = false;
    var finish = function(ok){ if (done) return; done = true; ok ? resolve({ src: src, ok: true }) : resolve({ src: src, ok: false }); };
    audio.oncanplaythrough = function(){ finish(true); };
    audio.onerror = function(){ finish(false); };
    audio.src = src;
  }); }

  if (typeof module !== 'undefined' && module.exports){
    module.exports = { playSound, isMobile, isTouchDevice, setButtonPressed, loadImage, loadAudio };
  } else {
    root.DinoGame = root.DinoGame || {};
    root.DinoGame.playSound = playSound;
    root.DinoGame.isMobile = isMobile;
    root.DinoGame.isTouchDevice = isTouchDevice;
    root.DinoGame.setButtonPressed = setButtonPressed;
    root.DinoGame.loadImage = loadImage;
    root.DinoGame.loadAudio = loadAudio;
  }
})(typeof window !== 'undefined' ? window : global);
