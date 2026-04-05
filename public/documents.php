<?php
session_start();
require '../actions/users/securityAction.php';

require '../src/bootstrap.php';
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';

$baseDirectoryPath = dirname(__DIR__) . '/fichiers';
if (!is_dir($baseDirectoryPath)) {
    mkdir($baseDirectoryPath, 0775, true);
}
$baseDirectoryRealPath = realpath($baseDirectoryPath);
if ($baseDirectoryRealPath === false) {
    throw new RuntimeException('Le dossier fichiers est inaccessible.');
}

$flashMessage = $_SESSION['documents_message'] ?? null;
unset($_SESSION['documents_message']);

$setFlashMessage = static function (string $type, string $text): void {
    $_SESSION['documents_message'] = [
        'type' => $type,
        'text' => $text
    ];
};

$normalizeName = static function (string $name): string {
    $name = trim($name);
    $name = str_replace(['/', '\\'], '-', $name);
    $name = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name);
    return trim((string) $name);
};

$normalizeRelativePath = static function (?string $path) use ($normalizeName): string {
    if ($path === null || $path === '') {
        return '';
    }

    $path = str_replace('\\', '/', $path);
    $segments = explode('/', $path);
    $safeSegments = [];

    foreach ($segments as $segment) {
        $segment = trim($segment);
        if ($segment === '' || $segment === '.' || $segment === '..') {
            continue;
        }

        $safeSegment = $normalizeName($segment);
        if ($safeSegment !== '') {
            $safeSegments[] = $safeSegment;
        }
    }

    return implode('/', $safeSegments);
};

$buildAbsolutePath = static function (string $relativePath) use ($baseDirectoryRealPath): string {
    if ($relativePath === '') {
        return $baseDirectoryRealPath;
    }

    return $baseDirectoryRealPath . '/' . $relativePath;
};

$relativePath = $normalizeRelativePath($_GET['path'] ?? '');
$currentDirectory = $buildAbsolutePath($relativePath);
if (!is_dir($currentDirectory)) {
    $relativePath = '';
    $currentDirectory = $baseDirectoryRealPath;
}

$redirectToCurrentPath = static function (string $path): void {
    $url = 'documents.php';
    if ($path !== '') {
        $url .= '?path=' . rawurlencode($path);
    }
    header('Location: ' . $url);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_folder') {
        $folderName = $normalizeName($_POST['folder_name'] ?? '');

        if ($folderName === '') {
            $setFlashMessage('error', 'Nom de dossier invalide.');
            $redirectToCurrentPath($relativePath);
        }

        $newFolderPath = $currentDirectory . '/' . $folderName;
        if (file_exists($newFolderPath)) {
            $setFlashMessage('error', 'Un élément porte déjà ce nom.');
            $redirectToCurrentPath($relativePath);
        }

        if (mkdir($newFolderPath, 0775, true)) {
            $setFlashMessage('success', 'Dossier créé avec succès.');
        } else {
            $setFlashMessage('error', 'Impossible de créer le dossier.');
        }

        $redirectToCurrentPath($relativePath);
    }

    if ($action === 'rename_item') {
        $itemName = $normalizeName($_POST['item_name'] ?? '');
        $newName = $normalizeName($_POST['new_name'] ?? '');

        if ($itemName === '' || $newName === '') {
            $setFlashMessage('error', 'Renommage invalide.');
            $redirectToCurrentPath($relativePath);
        }

        $sourcePath = $currentDirectory . '/' . $itemName;
        $destinationPath = $currentDirectory . '/' . $newName;

        if (!file_exists($sourcePath)) {
            $setFlashMessage('error', 'Élément introuvable.');
            $redirectToCurrentPath($relativePath);
        }

        if (file_exists($destinationPath)) {
            $setFlashMessage('error', 'Le nouveau nom existe déjà.');
            $redirectToCurrentPath($relativePath);
        }

        if (rename($sourcePath, $destinationPath)) {
            $setFlashMessage('success', 'Élément renommé avec succès.');
        } else {
            $setFlashMessage('error', 'Impossible de renommer l\'élément.');
        }

        $redirectToCurrentPath($relativePath);
    }

    if ($action === 'delete_item') {
        $itemName = $normalizeName($_POST['item_name'] ?? '');
        if ($itemName === '') {
            $setFlashMessage('error', 'Suppression invalide.');
            $redirectToCurrentPath($relativePath);
        }

        $itemPath = $currentDirectory . '/' . $itemName;
        if (!file_exists($itemPath)) {
            $setFlashMessage('error', 'Élément introuvable.');
            $redirectToCurrentPath($relativePath);
        }

        $deleted = false;
        if (is_dir($itemPath)) {
            $deleted = @rmdir($itemPath);
            if (!$deleted) {
                $setFlashMessage('error', 'Le dossier doit être vide pour être supprimé.');
                $redirectToCurrentPath($relativePath);
            }
        } else {
            $deleted = @unlink($itemPath);
        }

        if ($deleted) {
            $setFlashMessage('success', 'Élément supprimé.');
        } else {
            $setFlashMessage('error', 'Impossible de supprimer l\'élément.');
        }

        $redirectToCurrentPath($relativePath);
    }

    if ($action === 'upload' && isset($_FILES['documents'])) {
        $documents = $_FILES['documents'];
        $total = is_array($documents['name']) ? count($documents['name']) : 0;
        $uploadedCount = 0;

        for ($index = 0; $index < $total; $index++) {
            if ((int) $documents['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $originalName = basename((string) $documents['name'][$index]);
            $safeName = $normalizeName($originalName);
            if ($safeName === '') {
                continue;
            }

            $targetPath = $currentDirectory . '/' . $safeName;
            $pathInfo = pathinfo($safeName);
            $filename = $pathInfo['filename'] ?? 'document';
            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
            $counter = 1;

            while (file_exists($targetPath)) {
                $targetPath = $currentDirectory . '/' . $filename . '_' . $counter . $extension;
                $counter++;
            }

            if (move_uploaded_file($documents['tmp_name'][$index], $targetPath)) {
                $uploadedCount++;
            }
        }

        if ($uploadedCount > 0) {
            $setFlashMessage('success', $uploadedCount . ' fichier(s) envoyé(s).');
        } else {
            $setFlashMessage('error', 'Aucun fichier téléversé.');
        }

        $redirectToCurrentPath($relativePath);
    }
}

$items = [];
$scanResult = scandir($currentDirectory);
if ($scanResult !== false) {
    foreach ($scanResult as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $itemPath = $currentDirectory . '/' . $item;
        $isDirectory = is_dir($itemPath);
        $items[] = [
            'name' => $item,
            'isDirectory' => $isDirectory,
            'size' => $isDirectory ? null : filesize($itemPath),
            'modifiedAt' => filemtime($itemPath)
        ];
    }
}

usort($items, static function (array $left, array $right): int {
    if ($left['isDirectory'] !== $right['isDirectory']) {
        return $left['isDirectory'] ? -1 : 1;
    }

    return strcasecmp($left['name'], $right['name']);
});

$pathSegments = $relativePath === '' ? [] : explode('/', $relativePath);

$buildRelativePathForChild = static function (string $parentPath, string $child): string {
    return trim($parentPath . '/' . $child, '/');
};

$buildPublicFileLink = static function (string $path): string {
    $segments = explode('/', $path);
    $encodedSegments = array_map('rawurlencode', $segments);
    return '../fichiers/' . implode('/', $encodedSegments);
};

$buildFolderTree = null;
$buildFolderTree = static function (string $absolutePath, string $relative) use (&$buildFolderTree): array {
    $children = [];
    $entries = scandir($absolutePath);
    if ($entries === false) {
        return $children;
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $childAbsolutePath = $absolutePath . '/' . $entry;
        if (!is_dir($childAbsolutePath)) {
            continue;
        }

        $childRelativePath = trim($relative . '/' . $entry, '/');
        $children[] = [
            'name' => $entry,
            'path' => $childRelativePath,
            'children' => $buildFolderTree($childAbsolutePath, $childRelativePath)
        ];
    }

    usort($children, static function (array $left, array $right): int {
        return strcasecmp($left['name'], $right['name']);
    });

    return $children;
};

$folderTree = $buildFolderTree($baseDirectoryRealPath, '');

entete('Documents', 'Documents', '5');
?>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h2>Gestionnaire de documents</h2>

            <?php if ($flashMessage !== null): ?>
                <div class="alert <?php echo $flashMessage['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php echo htmlspecialchars($flashMessage['text'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <ol class="breadcrumb">
                <li><a href="documents.php">Racine</a></li>
                <?php
                $breadcrumbPath = '';
                foreach ($pathSegments as $segment):
                    $breadcrumbPath = $buildRelativePathForChild($breadcrumbPath, $segment);
                    ?>
                    <li><a href="documents.php?path=<?php echo rawurlencode($breadcrumbPath); ?>"><?php echo htmlspecialchars($segment, ENT_QUOTES, 'UTF-8'); ?></a></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Arborescence</div>
                <div class="panel-body">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="<?php echo $relativePath === '' ? 'active' : ''; ?>">
                            <a href="documents.php"><span class="glyphicon glyphicon-hdd"></span> Racine</a>
                        </li>
                    </ul>
                    <?php
                    $renderTree = null;
                    $renderTree = static function (array $nodes, string $currentPath, int $depth = 0) use (&$renderTree): void {
                        if (count($nodes) === 0) {
                            return;
                        }
                        ?>
                        <ul class="nav nav-pills nav-stacked" style="margin-left: <?php echo (int) ($depth * 14); ?>px;">
                            <?php foreach ($nodes as $node): ?>
                                <li class="<?php echo $currentPath === $node['path'] ? 'active' : ''; ?>">
                                    <a href="documents.php?path=<?php echo rawurlencode($node['path']); ?>">
                                        <span class="glyphicon glyphicon-folder-open"></span>
                                        <?php echo htmlspecialchars($node['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </li>
                                <?php $renderTree($node['children'], $currentPath, $depth + 1); ?>
                            <?php endforeach; ?>
                        </ul>
                        <?php
                    };
                    $renderTree($folderTree, $relativePath);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-sm-8 col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Dossier courant :
                    <strong><?php echo $relativePath === '' ? 'Racine' : htmlspecialchars($relativePath, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form method="post" class="form-inline">
                                <input type="hidden" name="action" value="create_folder">
                                <div class="form-group">
                                    <label class="sr-only" for="folder_name">Nom du dossier</label>
                                    <input type="text" class="form-control input-sm" id="folder_name" name="folder_name" placeholder="Nouveau dossier" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="glyphicon glyphicon-plus"></span> Nouveau dossier
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="post" enctype="multipart/form-data" id="upload-form" class="form-inline">
                                <input type="hidden" name="action" value="upload">
                                <div class="form-group">
                                    <label class="sr-only" for="documents-input">Fichiers</label>
                                    <input type="file" class="form-control input-sm" name="documents[]" id="documents-input" multiple>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <span class="glyphicon glyphicon-upload"></span> Envoyer
                                </button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div id="documents-dropzone" class="panel panel-info">
                        <div class="panel-body text-center">
                            Glissez-déposez vos fichiers ici.
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Modifié le</th>
                                <th>Type</th>
                                <th>Taille</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (count($items) === 0): ?>
                                <tr>
                                    <td colspan="5">Ce dossier est vide.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $item): ?>
                                    <?php
                                    $itemName = $item['name'];
                                    $itemRelativePath = $buildRelativePathForChild($relativePath, $itemName);
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if ($item['isDirectory']): ?>
                                                <a href="documents.php?path=<?php echo rawurlencode($itemRelativePath); ?>">
                                                    <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
                                                    <?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo $buildPublicFileLink($itemRelativePath); ?>" target="_blank">
                                                    <span class="glyphicon glyphicon-file" aria-hidden="true"></span>
                                                    <?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', (int) $item['modifiedAt']); ?></td>
                                        <td><?php echo $item['isDirectory'] ? 'Dossier de fichiers' : 'Fichier'; ?></td>
                                        <td><?php echo $item['isDirectory'] ? '' : number_format((float) $item['size'] / 1024, 1, ',', ' ') . ' Ko'; ?></td>
                                        <td>
                                            <form method="post" class="form-inline">
                                                <input type="hidden" name="action" value="rename_item">
                                                <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="form-group">
                                                    <label class="sr-only">Nouveau nom</label>
                                                    <input type="text" class="form-control input-sm" name="new_name" placeholder="Nouveau nom" required>
                                                </div>
                                                <button type="submit" class="btn btn-default btn-sm">Renommer</button>
                                            </form>
                                            <form method="post" class="form-inline" onsubmit="return confirm('Confirmer la suppression ?');">
                                                <input type="hidden" name="action" value="delete_item">
                                                <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const dropzone = document.getElementById('documents-dropzone');
        const fileInput = document.getElementById('documents-input');
        const uploadForm = document.getElementById('upload-form');

        if (!dropzone || !fileInput || !uploadForm) {
            return;
        }

        dropzone.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (fileInput.files.length > 0) {
                uploadForm.submit();
            }
        });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.remove('panel-info');
                dropzone.classList.add('panel-primary');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.remove('panel-primary');
                dropzone.classList.add('panel-info');
            });
        });

        dropzone.addEventListener('drop', function (event) {
            const files = event.dataTransfer.files;
            if (!files || files.length === 0) {
                return;
            }

            const dataTransfer = new DataTransfer();
            Array.prototype.forEach.call(files, function (file) {
                dataTransfer.items.add(file);
            });
            fileInput.files = dataTransfer.files;
            uploadForm.submit();
        });
    })();
</script>

<?php require '../includes/footer.php'; ?>
