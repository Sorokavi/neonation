<?php
session_start();

require_once '../load-env.php';

// 1) Define the uploads directory & manifest path
$uploadsDir   = __DIR__ . '/uploads/';
$manifestPath = $uploadsDir . 'uploads.json';

// 2) Define any globals (guild ID, maxFileSize, allowedExtensions)
$allowedGuildId = $_ENV['NEO_GUILD_ID'];
$maxFileSize = 1000 * 1024 * 1024;
$allowedExtensions = [
    'png','jpg','jpeg','gif',
    'mp4','mov','webm','ogg','avi','mkv',
    'mp3','wav','flac','flv','m4v',
    'pdf','txt','doc','docx','ppt','pptx'
];

// 3) Define helper functions
function human_filesize(int $bytes, int $decimals = 2): string {
    $sz = ['B','KB','MB','GB','TB'];
    $factor = floor((strlen((string)$bytes) - 1) / 3);
    return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $sz[$factor]);
}

function load_manifest(string $path): array {
    if (! file_exists($path)) {
        return [];
    }
    $json = file_get_contents($path);
    $arr  = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

function save_manifest(string $path, array $data): bool {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($path, $json) !== false;
}

// 4) Check login / permission
$isLoggedIn = isset($_SESSION['discord_user']);

if ($isLoggedIn) {
    $userGuilds = $_SESSION['discord_user']['guilds'] ?? [];
    $inServer   = false;
    foreach ($userGuilds as $g) {
        if (isset($g['id']) && strval($g['id']) === $allowedGuildId) {
            $inServer = true;
            break;
        }
    }
    if (! $inServer) {
        die(
            '<h1 style="color:red; text-align:center;">403 Forbidden</h1>' .
            '<p style="text-align:center; color:#39FF14;">You must be a member of the Neo Nation Discord server to access this page.</p>'
        );
    }

    $currentUserId   = $_SESSION['discord_user']['id'];
    $currentUsername = $_SESSION['discord_user']['username'];
}

// 5) Handle DELETE
$deleteError   = '';
$deleteSuccess = '';
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $toDelete = basename($_POST['delete_file']);
    if ($toDelete === '') {
        $deleteError = 'Invalid filename.';
    } else {
        $manifest = load_manifest($manifestPath);

        $foundIndex = null;
        foreach ($manifest as $idx => $entry) {
            if ($entry['file'] === $toDelete && $entry['uploader_id'] === $currentUserId) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex === null) {
            $deleteError = 'File not found or you do not have permission to delete it.';
        } else {
            $filePath = $uploadsDir . $toDelete;
            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
            array_splice($manifest, $foundIndex, 1);
            save_manifest($manifestPath, $manifest);
            $deleteSuccess = "Successfully deleted \"{$toDelete}\".";
        }
    }
}

$renameError   = '';
$renameSuccess = '';
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rename_file'])) {
    $oldFileName = basename($_POST['rename_file']);
    $newDisplayName = trim($_POST['new_display_name'] ?? '');
    
    if ($oldFileName === '' || $newDisplayName === '') {
        $renameError = 'Invalid filename or new name is empty.';
    } else {
        // Sanitize the new display name (allow only safe chars, preserve extension)
        $ext = strtolower(pathinfo($oldFileName, PATHINFO_EXTENSION));
        $baseName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($newDisplayName, PATHINFO_FILENAME));
        if (strlen($baseName) > 100) {
            $baseName = substr($baseName, 0, 100);
        }
        $newFileName = $baseName . '.' . $ext;
        $newFilePath = $uploadsDir . $newFileName;

        // Check for duplicate name for this user in manifest
        $manifest = load_manifest($manifestPath);
        $duplicate = false;
        foreach ($manifest as $entry) {
            if ($entry['file'] === $newFileName && $entry['uploader_id'] === $currentUserId) {
                $duplicate = true;
                break;
            }
        }
        if ($duplicate || file_exists($newFilePath)) {
            $renameError = 'A file with that name already exists.';
        } else {
            // Find the entry to rename
            $foundIndex = null;
            foreach ($manifest as $idx => $entry) {
                if ($entry['file'] === $oldFileName && $entry['uploader_id'] === $currentUserId) {
                    $foundIndex = $idx;
                    break;
                }
            }
            if ($foundIndex === null) {
                $renameError = 'File not found or you do not have permission to rename it.';
            } else {
                $oldFilePath = $uploadsDir . $oldFileName;
                // Rename the physical file
                if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                    if (rename($oldFilePath, $newFilePath)) {
                        // Update the manifest
                        $manifest[$foundIndex]['file'] = $newFileName;
                        $manifest[$foundIndex]['timestamp'] = time(); // Update timestamp for rename
                        if (save_manifest($manifestPath, $manifest)) {
                            $renameSuccess = "Successfully renamed to \"{$newFileName}\".";
                        } else {
                            // Rollback the file rename if manifest update fails
                            rename($newFilePath, $oldFilePath);
                            $renameError = 'Failed to update manifest. Rename rolled back.';
                        }
                    } else {
                        $renameError = 'Failed to rename the file on disk.';
                    }
                } else {
                    $renameError = 'Original file not found on disk.';
                }
            }
        }
    }
}

$uploadError   = '';
$uploadSuccess = '';
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_submit'])) {
    if (! isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
        $uploadError = 'No file uploaded or upload error.';
    } else {
        $file     = $_FILES['userfile'];
        $tmpPath  = $file['tmp_name'];
        $origName = basename($file['name']);
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (! in_array($ext, $allowedExtensions, true)) {
            $uploadError = 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions);
        }
        elseif ($file['size'] > $maxFileSize) {
            $uploadError = 'File is too large (max 10 MB).';
        } else {
            $safeBase  = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            $timestamp = time();
            $newName   = "{$safeBase}_{$currentUserId}_{$timestamp}.{$ext}";
            $targetPath = $uploadsDir . $newName;

            if (! move_uploaded_file($tmpPath, $targetPath)) {
                $uploadError = 'Failed to move uploaded file.';
            } else {
                chmod($targetPath, 0644);

                $manifest = load_manifest($manifestPath);
                $manifest[] = [
                    'file'          => $newName,
                    'uploader_id'   => $currentUserId,
                    'uploader_name' => $currentUsername,
                    'timestamp'     => $timestamp
                ];
                if (! save_manifest($manifestPath, $manifest)) {
                    unlink($targetPath);
                    $uploadError = 'Could not update manifest.';
                } else {
                    $publicUrl     = "https://cdn.neonation.net/uploads/{$newName}";
                    $uploadSuccess = "Upload successful! File is at:<br>
                                      <a href=\"{$publicUrl}\" target=\"_blank\">{$publicUrl}</a>";
                }
            }
        }
    }
}

$allEntries = load_manifest($manifestPath);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <!-- OG -->
  <meta property="og:title"       content="Neonation – User Content" />
  <meta property="og:description" content="Custom CDN wowowow" />
  <meta property="og:image"       content="https://neonation.net/assets/neoslayer.png" />
  <meta property="og:url"         content="https://neonation.net/usercontent/" />
  <meta property="og:type"        content="website" />
  <meta name="theme-color"        content="#39FF14" />

  <title>Neo Nation CDN</title>

  <!-- Favicon -->
  <link rel="icon" href="../assets/neoslayer.png" sizes="32x32" type="image/png">

  <style>
    body {
      background-image: url('/assets/tiles.gif');
      background-repeat: repeat;
      color: #39FF14;
      font-family: 'Courier New', Courier, monospace;
      margin: 0;
      padding: 2em;
      text-align: center;
    }
    h1 {
      font-size: 2.5em;
      text-shadow: 0 0 5px #39FF14;
      margin-bottom: 0.5em;
    }
    .login-prompt {
      text-align: center;
      background: #181818;
      border: 1px solid #39FF14;
      padding: 2em;
      margin: 3em auto;
      max-width: 500px;
      border-radius: 6px;
    }
    .login-prompt button {
      background: #39FF14;
      color: #000;
      border: none;
      padding: 0.5em 1.5em;
      font-family: 'Courier New', monospace;
      font-weight: bold;
      cursor: pointer;
      border-radius: 4px;
      box-shadow: 0 0 10px #39FF14;
    }
    .login-prompt button:hover {
      background: #000;
      color: #39FF14;
      box-shadow: 0 0 20px #39FF14;
    }
    .upload-form {
      max-width: 600px;
      margin: 2em auto;
      background: #181818;
      border: 1px solid #39FF14;
      padding: 1em;
    }
    .upload-form h3 {
      margin-top: 0;
      color: #39FF14;
    }
    .upload-form input[type="file"] {
      background: #000;
      color: #39FF14;
      border: 1px solid #39FF14;
      padding: 0.5em;
      margin-bottom: 1em;
      width: 100%;
    }
    .upload-form button {
      background: #39FF14;
      color: #000;
      border: none;
      padding: 0.5em 1.5em;
      font-family: 'Courier New', monospace;
      font-weight: bold;
      cursor: pointer;
      border-radius: 4px;
      box-shadow: 0 0 10px #39FF14;
    }
    .upload-form button:hover {
      background: #000;
      color: #39FF14;
      box-shadow: 0 0 20px #39FF14;
    }
    .message {
      max-width: 600px;
      margin: 1em auto;
      text-align: center;
    }
    .error {
      color: #ff4444;
      font-weight: bold;
    }
    .success {
      color: #39FF14;
      font-weight: bold;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 2em;
      margin-left: auto;
      margin-right: auto;
    }
    th, td {
      padding: 0.5em;
      border: 1px solid #39FF14;
      text-align: left;
    }
    th {
      background: #181818;
    }
    tr:nth-child(even) {
      background: #111;
    }
    a {
      color: #aa5c00;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .delete-button {
      background: #ff4444;
      color: #000;
      border: none;
      padding: 0.3em 0.8em;
      font-family: 'Courier New', monospace;
      font-weight: bold;
      cursor: pointer;
      text-transform: uppercase;
      border-radius: 4px;
      box-shadow: 0 0 5px #ff4444;
    }
    .delete-button:hover {
      background: #000;
      color: #ff4444;
      box-shadow: 0 0 15px #ff4444;
    }
    .rename-button {
      background: #3399ff;
      color: #000;
      border: none;
      padding: 0.3em 0.8em;
      font-family: 'Courier New', monospace;
      font-weight: bold;
      cursor: pointer;
      text-transform: uppercase;
      border-radius: 4px;
      box-shadow: 0 0 5px #3399ff;
    }
    .rename-button:hover {
      background: #000;
      color: #3399ff;
      box-shadow: 0 0 15px #3399ff;
    }
    .rename-form {
      display: none;
      margin-top: 0.5em;
      padding: 0.5em;
      background: #181818;
      border: 1px solid #3399ff;
      border-radius: 4px;
    }
    .rename-form input[type="text"] {
      background: #000;
      color: #39FF14;
      border: 1px solid #39FF14;
      padding: 0.3em;
      font-family: 'Courier New', monospace;
      width: 200px;
    }
    .rename-form button {
      background: #3399ff;
      color: #000;
      border: none;
      padding: 0.3em 0.8em;
      font-family: 'Courier New', monospace;
      font-weight: bold;
      cursor: pointer;
      border-radius: 4px;
      margin-left: 0.5em;
    }
    .rename-form button:hover {
      background: #000;
      color: #3399ff;
    }
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2em;
      flex-wrap: wrap;
      gap: 1em;
    }
    .user-info {
      color: #39FF14;
      font-size: 0.95em;
    }
    .header-button {
      background: transparent;
      border: 2px solid #aa5c00;
      color: #aa5c00;
      padding: 0.3em 1.2em;
      font-size: 0.85em;
      font-family: 'Courier New', Courier, monospace;
      cursor: pointer;
      transition: 0.15s ease-in-out;
      text-transform: uppercase;
      letter-spacing: 1px;
      text-decoration: none;
      display: inline-block;
    }
    .header-button:hover {
      background: #aa5c00;
      color: #111;
      box-shadow: 0 0 8px #aa5c00;
    }
    .back-button {
      border-color: #39FF14;
      color: #39FF14;
    }
    .back-button:hover {
      background: #39FF14;
      color: #111;
      box-shadow: 0 0 8px #39FF14;
    }
    .copy-link-button {
      background: #aa5c00;
      color: #000;
      border: none;
      padding: 0.3em 0.8em;
      font-family: 'Courier New', monospace;
      font-weight: bold;
      cursor: pointer;
      text-transform: uppercase;
      border-radius: 4px;
      box-shadow: 0 0 5px #aa5c00;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    }
    .copy-link-button:hover {
      background: #000;
      color: #aa5c00;
      box-shadow: 0 0 15px #aa5c00;
    }
  </style>
</head>
<body>

  <!-- Standardized header with navigation and auth -->
  <div class="page-header">
    <a href="/" class="header-button back-button">← Back to Home</a>
    <?php if (isset($_SESSION['discord_user'])): ?>
    <div class="user-info">
      Logged in as <strong><?php echo htmlspecialchars($currentUsername); ?></strong>
      <form method="post" action="/logout" style="display:inline; margin-left: 1em;">
        <button type="submit" class="header-button logout-button">Sign Out</button>
      </form>
    </div>
    <?php endif; ?>
  </div>

  <h1>User Content</h1>

  <?php if (! $isLoggedIn): ?>

    <!-- Login prompt -->
    <div class="login-prompt">
      <p>You must log in with Discord to upload or manage files.</p>
      <form method="get" action="/login.php">
        <button type="submit">Log in with Discord</button>
      </form>
    </div>

  <?php else: ?>

    <!-- Uploads panel -->
    <div class="upload-form">
      <h3>Upload a File</h3>
      <?php if (! empty($uploadError)): ?>
        <div class="message error"><?php echo $uploadError; ?></div>
      <?php elseif (! empty($uploadSuccess)): ?>
        <div class="message success"><?php echo $uploadSuccess; ?></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <input type="file"
               name="userfile"
               accept=".png,.jpg,.jpeg,.gif,.mp4,.mov,.webm,.ogg,.avi,.mkv,.mp3,.wav,.flac,.pdf,.txt,.doc,.docx,.ppt,.pptx"
               required>
        <br>
        <button type="submit" name="upload_submit">Upload</button>
      </form>
    </div>

    <!-- Uploads table -->
    <?php
      $userEntries = array_filter(
        $allEntries,
        fn($e) => $e['uploader_id'] === $currentUserId
      );
    ?>
    <?php if (empty($userEntries)): ?>
      <div class="message">You haven’t uploaded any files yet.</div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Filename</th>
            <th>Size</th>
            <th>Uploaded On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($userEntries as $item):
              $fileName = $item['file'];
              $filePath = $uploadsDir . $fileName;
              if (! is_file($filePath)) {
                  continue;
              }
              $filesize = filesize($filePath);
              $uploaded = date('Y-m-d H:i', $item['timestamp']);
              $urlPath  = "https://cdn.neonation.net/" . rawurlencode($fileName);
          ?>
          <tr>
            <td>
              <?php echo htmlspecialchars($fileName); ?>
              <!-- Rename Form -->
              <div class="rename-form" id="rename-form-<?php echo htmlspecialchars($fileName); ?>">
                <form method="post" style="display:inline;">
                  <input type="hidden" name="rename_file" value="<?php echo htmlspecialchars($fileName); ?>">
                  <input type="text" name="new_display_name" placeholder="New filename" required>
                  <button type="submit">Rename</button>
                  <button type="button" onclick="toggleRenameForm('<?php echo htmlspecialchars($fileName); ?>')">Cancel</button>
                </form>
              </div>
            </td>
            <td><?php echo human_filesize($filesize); ?></td>
            <td><?php echo $uploaded; ?></td>
            <td>
              <button type="button" class="copy-link-button" onclick="copyToClipboard('<?php echo $urlPath; ?>')">Copy Link</button>
              &nbsp;|&nbsp;
              <button type="button" class="rename-button" 
                      onclick="toggleRenameForm('<?php echo htmlspecialchars($fileName); ?>')">Rename</button>
              &nbsp;|&nbsp;
              <form method="post" style="display:inline;"
                    onsubmit="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($fileName); ?>?');">
                <input type="hidden" name="delete_file" value="<?php echo htmlspecialchars($fileName); ?>">
                <button type="submit" class="delete-button">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if (! empty($deleteError)): ?>
        <div class="message error"><?php echo $deleteError; ?></div>
      <?php elseif (! empty($deleteSuccess)): ?>
        <div class="message success"><?php echo $deleteSuccess; ?></div>
      <?php endif; ?>
      <?php if (! empty($renameError)): ?>
        <div class="message error"><?php echo $renameError; ?></div>
      <?php elseif (! empty($renameSuccess)): ?>
        <div class="message success"><?php echo $renameSuccess; ?></div>
      <?php endif; ?>
    <?php endif; ?>

  <?php endif;?>

  <script>
    function toggleRenameForm(fileName) {
      const form = document.getElementById('rename-form-' + fileName);
      if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
      } else {
        form.style.display = 'none';
      }
    }
    function copyToClipboard(text) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
          alert('Link copied to clipboard!');
        }, function(err) {
          alert('Failed to copy: ' + err);
        });
      } else {
        // fallback for older browsers
        var textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        try {
          document.execCommand('copy');
          alert('Link copied to clipboard!');
        } catch (err) {
          alert('Failed to copy: ' + err);
        }
        document.body.removeChild(textarea);
      }
    }
  </script>

</body>
</html>
