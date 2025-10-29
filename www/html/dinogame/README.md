Structure
- js/constants.js: values for sprite paths, physics, etc.
- js/utils.js: helpers (playSound, device checks, lightweight loaders)
- js/state.js: factory that holds all mutable state inside a closure; exposes controlled getters/mutators
- js/parallax.js: background hills
- js/ground.js: ground tile creation/scrolling
- js/clouds.js: cloud creation/parallax movement
- js/dino.js: dino position, size, sprite animation updates
- js/obstacles.js: obstacle creation, updates/collisions
- js/preload.js: image/audio preloading
- js/game.js: wires DOM, state, systems, inputs, main loop

Notes
- Assets are optional; when an image fails to load, CSS fallback shapes render instead so the game still runs.
- Audio playback may be blocked until user interaction; calls are guarded.
- Removed an unrelated/buggy raycasting block that attempted to call `getContext` on a `div`.