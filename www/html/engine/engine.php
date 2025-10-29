<!-- <?php
// Wolfenstein/Doom-style raycasting engine in PHP

// If the request is for an image (has 'posX' parameter), render the image
if (isset($_GET['posX'])) {
    // Map definition (1 = wall, 0 = empty)
    $map = [
        [1,1,1,1,1,1,1,1,1,1],
        [1,0,0,0,0,0,0,0,0,1],
        [1,0,1,0,1,1,0,1,0,1],
        [1,0,1,0,0,0,0,1,0,1],
        [1,0,1,1,1,1,0,1,0,1],
        [1,0,0,0,0,0,0,0,0,1],
        [1,1,1,1,1,1,1,1,1,1],
    ];
    $mapWidth = count($map[0]);
    $mapHeight = count($map);

    // Player state from query parameters
    $posX = floatval($_GET['posX']);
    $posY = floatval($_GET['posY']);
    $dirX = floatval($_GET['dirX']);
    $dirY = floatval($_GET['dirY']);
    $planeX = floatval($_GET['planeX']);
    $planeY = floatval($_GET['planeY']);

    // Image size
    $width = 320;
    $height = 200;

    // Create image
    $img = imagecreatetruecolor($width, $height);
    $colorWall = imagecolorallocate($img, 100, 100, 100);
    $colorFloor = imagecolorallocate($img, 50, 50, 50);
    $colorCeil = imagecolorallocate($img, 120, 120, 180);

    // Draw floor and ceiling
    imagefilledrectangle($img, 0, 0, $width, $height/2, $colorCeil);
    imagefilledrectangle($img, 0, $height/2, $width, $height, $colorFloor);

    // Raycasting
    for ($x = 0; $x < $width; $x++) {
        $cameraX = 2 * $x / $width - 1;
        $rayDirX = $dirX + $planeX * $cameraX;
        $rayDirY = $dirY + $planeY * $cameraX;

        $mapX = (int)$posX;
        $mapY = (int)$posY;

        $deltaDistX = ($rayDirX == 0) ? 1e30 : abs(1 / $rayDirX);
        $deltaDistY = ($rayDirY == 0) ? 1e30 : abs(1 / $rayDirY);

        $stepX = ($rayDirX < 0) ? -1 : 1;
        $sideDistX = ($rayDirX < 0) ? ($posX - $mapX) * $deltaDistX : ($mapX + 1.0 - $posX) * $deltaDistX;
        $stepY = ($rayDirY < 0) ? -1 : 1;
        $sideDistY = ($rayDirY < 0) ? ($posY - $mapY) * $deltaDistY : ($mapY + 1.0 - $posY) * $deltaDistY;

        $hit = 0;
        $side = 0;
        while ($hit == 0) {
            if ($sideDistX < $sideDistY) {
                $sideDistX += $deltaDistX;
                $mapX += $stepX;
                $side = 0;
            } else {
                $sideDistY += $deltaDistY;
                $mapY += $stepY;
                $side = 1;
            }
            if ($mapX >= 0 && $mapX < $mapWidth && $mapY >= 0 && $mapY < $mapHeight && $map[$mapY][$mapX] > 0) {
                $hit = 1;
            }
        }
        if ($side == 0) {
            $perpWallDist = ($mapX - $posX + (1 - $stepX) / 2) / $rayDirX;
        } else {
            $perpWallDist = ($mapY - $posY + (1 - $stepY) / 2) / $rayDirY;
        }
        $lineHeight = ($perpWallDist > 0) ? (int)($height / $perpWallDist) : $height;
        $drawStart = max(0, -$lineHeight / 2 + $height / 2);
        $drawEnd = min($height - 1, $lineHeight / 2 + $height / 2);
        $shade = ($side == 1) ? 0.7 : 1.0;
        $wallColor = imagecolorallocate($img, 100 * $shade, 100 * $shade, 100 * $shade);
        imageline($img, $x, $drawStart, $x, $drawEnd, $wallColor);
    }

    // Output image
    header('Content-Type: image/png');
    imagepng($img);
    imagedestroy($img);
    exit;
}

// Otherwise, output the HTML/JS frontend
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Engine</title>
    <style>
        body { background: #222; color: #fff; text-align: center; }
        #game { border: 2px solid #fff; margin-top: 20px; }
    </style>
</head>
<body>
    <canvas id="game" width="320" height="200"></canvas>
    <script>
    // Initial player state
    let posX = 2.5, posY = 2.5, dirX = 1, dirY = 0, planeX = 0, planeY = 0.66;
    const moveSpeed = 0.15, rotSpeed = 0.08;
    const map = [
        [1,1,1,1,1,1,1,1,1,1],
        [1,0,0,0,0,0,0,0,0,1],
        [1,0,1,0,1,1,0,1,0,1],
        [1,0,1,0,0,0,0,1,0,1],
        [1,0,1,1,1,1,0,1,0,1],
        [1,0,0,0,0,0,0,0,0,1],
        [1,1,1,1,1,1,1,1,1,1],
    ];
    function render() {
        // Compose query string
        const params = new URLSearchParams({
            posX, posY, dirX, dirY, planeX, planeY
        });
        const img = new window.Image();
        img.onload = function() {
            const ctx = document.getElementById('game').getContext('2d');
            ctx.drawImage(img, 0, 0);
        };
        img.src = `engine.php?${params.toString()}&t=${Date.now()}`;
    }
    function isWall(x, y) {
        x = Math.floor(x); y = Math.floor(y);
        // Out of bounds is a wall
        if (y < 0 || y >= map.length || x < 0 || x >= map[0].length) return true;
        return map[y][x] !== 0;
    }
    document.addEventListener('keydown', function(e) {
        let moved = false;
        if (e.key === 'w') {
            const nx = posX + dirX * moveSpeed;
            const ny = posY + dirY * moveSpeed;
            if (!isWall(nx, posY)) posX = nx;
            if (!isWall(posX, ny)) posY = ny;
            moved = true;
        } else if (e.key === 's') {
            const nx = posX - dirX * moveSpeed;
            const ny = posY - dirY * moveSpeed;
            if (!isWall(nx, posY)) posX = nx;
            if (!isWall(posX, ny)) posY = ny;
            moved = true;
        } else if (e.key === 'a') {
            // rotate left
            const oldDirX = dirX;
            dirX = dirX * Math.cos(-rotSpeed) - dirY * Math.sin(-rotSpeed);
            dirY = oldDirX * Math.sin(-rotSpeed) + dirY * Math.cos(-rotSpeed);
            const oldPlaneX = planeX;
            planeX = planeX * Math.cos(-rotSpeed) - planeY * Math.sin(-rotSpeed);
            planeY = oldPlaneX * Math.sin(-rotSpeed) + planeY * Math.cos(-rotSpeed);
            moved = true;
        } else if (e.key === 'd') {
            // rotate right
            const oldDirX = dirX;
            dirX = dirX * Math.cos(rotSpeed) - dirY * Math.sin(rotSpeed);
            dirY = oldDirX * Math.sin(rotSpeed) + dirY * Math.cos(rotSpeed);
            const oldPlaneX = planeX;
            planeX = planeX * Math.cos(rotSpeed) - planeY * Math.sin(rotSpeed);
            planeY = oldPlaneX * Math.sin(rotSpeed) + planeY * Math.cos(rotSpeed);
            moved = true;
        }
        if (moved) render();
    });
    render();
    </script>
    <p>Use <b>WASD</b> to move and turn.</p>
</body>
</html> -->