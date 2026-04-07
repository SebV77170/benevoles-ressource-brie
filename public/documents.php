<?php
session_start();
require '../actions/users/securityAction.php';

require '../src/bootstrap.php';
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';

$isAdmin = (int) $users->getAdmin() === 2;

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
    $name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name;
    $name = str_replace(['/', '\\'], '-', $name);
    $name = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name);
    $name = trim((string) $name, " .");
    if ($name === '') {
        return '';
    }

    return $name;
};

$normalizeDisplayName = static function (string $name): string {
    $name = trim($name);
    $name = str_replace(["\0", '/', '\\'], '', $name);
    return trim($name);
};

$getDirectoryMetadataPath = static function (string $absoluteDirectory): string {
    return rtrim($absoluteDirectory, '/') . '/.display_names.json';
};

$loadDirectoryMetadata = static function (string $absoluteDirectory) use ($getDirectoryMetadataPath): array {
    $metadataPath = $getDirectoryMetadataPath($absoluteDirectory);
    if (!is_file($metadataPath)) {
        return [];
    }

    $rawContent = file_get_contents($metadataPath);
    if ($rawContent === false || $rawContent === '') {
        return [];
    }

    $decoded = json_decode($rawContent, true);
    if (!is_array($decoded)) {
        return [];
    }

    return $decoded;
};

$saveDirectoryMetadata = static function (string $absoluteDirectory, array $metadata) use ($getDirectoryMetadataPath): void {
    ksort($metadata);
    $metadataPath = $getDirectoryMetadataPath($absoluteDirectory);
    file_put_contents($metadataPath, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
};

$setDisplayNameForItem = static function (string $absoluteDirectory, string $storedName, string $displayName) use ($loadDirectoryMetadata, $saveDirectoryMetadata): void {
    if ($storedName === '' || $displayName === '') {
        return;
    }

    $metadata = $loadDirectoryMetadata($absoluteDirectory);
    $metadata[$storedName] = $displayName;
    $saveDirectoryMetadata($absoluteDirectory, $metadata);
};

$removeDisplayNameForItem = static function (string $absoluteDirectory, string $storedName) use ($loadDirectoryMetadata, $saveDirectoryMetadata): void {
    if ($storedName === '') {
        return;
    }

    $metadata = $loadDirectoryMetadata($absoluteDirectory);
    if (!array_key_exists($storedName, $metadata)) {
        return;
    }

    unset($metadata[$storedName]);
    $saveDirectoryMetadata($absoluteDirectory, $metadata);
};

$sanitizeExistingItemName = static function (?string $name): string {
    $name = trim((string) $name);
    if ($name === '' || $name === '.' || $name === '..') {
        return '';
    }
    if (strpos($name, '/') !== false || strpos($name, '\\') !== false || strpos($name, "\0") !== false) {
        return '';
    }

    return $name;
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
    $adminOnlyActions = ['create_folder', 'rename_item', 'delete_item', 'move_item', 'upload'];

    if (in_array($action, $adminOnlyActions, true) && !$isAdmin) {
        $setFlashMessage('error', 'Action non autorisée. Seuls les administrateurs peuvent modifier les documents.');
        $redirectToCurrentPath($relativePath);
    }

    if ($action === 'create_folder') {
        $requestedFolderName = $normalizeDisplayName($_POST['folder_name'] ?? '');
        $folderName = $normalizeName($requestedFolderName);

        if ($folderName === '' || $requestedFolderName === '') {
            $setFlashMessage('error', 'Nom de dossier invalide.');
            $redirectToCurrentPath($relativePath);
        }

        $newFolderPath = $currentDirectory . '/' . $folderName;
        if (file_exists($newFolderPath)) {
            $setFlashMessage('error', 'Un élément porte déjà ce nom.');
            $redirectToCurrentPath($relativePath);
        }

        if (mkdir($newFolderPath, 0775, true)) {
            $setDisplayNameForItem($currentDirectory, $folderName, $requestedFolderName);
            $setFlashMessage('success', 'Dossier créé avec succès.');
        } else {
            $setFlashMessage('error', 'Impossible de créer le dossier.');
        }

        $redirectToCurrentPath($relativePath);
    }

    if ($action === 'rename_item') {
        $itemName = $sanitizeExistingItemName($_POST['item_name'] ?? '');
        $requestedDisplayName = $normalizeDisplayName($_POST['new_name'] ?? '');
        $newName = $normalizeName($requestedDisplayName);

        if ($itemName === '' || $newName === '' || $requestedDisplayName === '') {
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
            $removeDisplayNameForItem($currentDirectory, $itemName);
            $setDisplayNameForItem($currentDirectory, $newName, $requestedDisplayName);
            $setFlashMessage('success', 'Élément renommé avec succès.');
        } else {
            $setFlashMessage('error', 'Impossible de renommer l\'élément.');
        }

        $redirectToCurrentPath($relativePath);
    }

    if ($action === 'delete_item') {
        $itemName = $sanitizeExistingItemName($_POST['item_name'] ?? '');
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
            $removeDisplayNameForItem($currentDirectory, $itemName);
            $setFlashMessage('success', 'Élément supprimé.');
        } else {
            $setFlashMessage('error', 'Impossible de supprimer l\'élément.');
        }

        $redirectToCurrentPath($relativePath);
    }

    if ($action === 'move_item') {
        $itemName = $sanitizeExistingItemName($_POST['item_name'] ?? '');
        $targetFolder = $sanitizeExistingItemName($_POST['target_folder'] ?? '');

        if ($itemName === '' || $targetFolder === '') {
            $setFlashMessage('error', 'Déplacement invalide.');
            $redirectToCurrentPath($relativePath);
        }

        $sourcePath = $currentDirectory . '/' . $itemName;
        $targetDirectory = $currentDirectory . '/' . $targetFolder;
        $destinationPath = $targetDirectory . '/' . $itemName;

        if (!file_exists($sourcePath) || !is_dir($targetDirectory)) {
            $setFlashMessage('error', 'Élément ou dossier cible introuvable.');
            $redirectToCurrentPath($relativePath);
        }

        if (file_exists($destinationPath)) {
            $setFlashMessage('error', 'Un élément avec ce nom existe déjà dans le dossier cible.');
            $redirectToCurrentPath($relativePath);
        }

        if (is_dir($sourcePath)) {
            $sourceRealPath = realpath($sourcePath);
            $targetRealPath = realpath($targetDirectory);
            if ($sourceRealPath !== false && $targetRealPath !== false && strpos($targetRealPath . '/', $sourceRealPath . '/') === 0) {
                $setFlashMessage('error', 'Impossible de déplacer un dossier dans lui-même.');
                $redirectToCurrentPath($relativePath);
            }
        }

        if (rename($sourcePath, $destinationPath)) {
            $currentMetadata = $loadDirectoryMetadata($currentDirectory);
            if (isset($currentMetadata[$itemName])) {
                $setDisplayNameForItem($targetDirectory, $itemName, (string) $currentMetadata[$itemName]);
                $removeDisplayNameForItem($currentDirectory, $itemName);
            }
            $setFlashMessage('success', 'Élément déplacé avec succès.');
        } else {
            $setFlashMessage('error', 'Impossible de déplacer l\'élément.');
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
            $displayName = $normalizeDisplayName($originalName);
            $safeName = $normalizeName($displayName);
            if ($safeName === '') {
                continue;
            }

            $targetPath = $currentDirectory . '/' . $safeName;
            $pathInfo = pathinfo($safeName);
            $filename = $pathInfo['filename'] ?? 'document';
            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
            $counter = 1;
            $finalSafeName = $safeName;
            $finalDisplayName = $displayName;

            while (file_exists($targetPath)) {
                $finalSafeName = $filename . '_' . $counter . $extension;
                $finalDisplayName = $displayName . ' (' . $counter . ')';
                $targetPath = $currentDirectory . '/' . $finalSafeName;
                $counter++;
            }

            if (move_uploaded_file($documents['tmp_name'][$index], $targetPath)) {
                $setDisplayNameForItem($currentDirectory, $finalSafeName, $finalDisplayName);
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
$currentDirectoryMetadata = $loadDirectoryMetadata($currentDirectory);
$scanResult = scandir($currentDirectory);
if ($scanResult !== false) {
    foreach ($scanResult as $item) {
        if ($item === '.' || $item === '..' || $item === '.display_names.json') {
            continue;
        }

        $itemPath = $currentDirectory . '/' . $item;
        $isDirectory = is_dir($itemPath);
        $items[] = [
            'name' => $item,
            'displayName' => $currentDirectoryMetadata[$item] ?? $item,
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

    return strcasecmp($left['displayName'], $right['displayName']);
});

$pathSegments = $relativePath === '' ? [] : explode('/', $relativePath);
$displayPathSegments = [];
if (!empty($pathSegments)) {
    $runningAbsolutePath = $baseDirectoryRealPath;
    foreach ($pathSegments as $segment) {
        $metadata = $loadDirectoryMetadata($runningAbsolutePath);
        $displayPathSegments[] = $metadata[$segment] ?? $segment;
        $runningAbsolutePath .= '/' . $segment;
    }
}

$buildRelativePathForChild = static function (string $parentPath, string $child): string {
    return trim($parentPath . '/' . $child, '/');
};

$buildPublicFileLink = static function (string $path): string {
    $segments = explode('/', $path);
    $encodedSegments = array_map('rawurlencode', $segments);
    return '../fichiers/' . implode('/', $encodedSegments);
};

$getIconClass = static function (array $item): string {
    if ($item['isDirectory']) {
        return 'glyphicon-folder-open';
    }

    $extension = strtolower((string) pathinfo($item['name'], PATHINFO_EXTENSION));
    if ($extension === 'pdf') {
        return 'glyphicon-book';
    }
    if (in_array($extension, ['doc', 'docx', 'odt', 'txt'], true)) {
        return 'glyphicon-file';
    }
    if (in_array($extension, ['xls', 'xlsx', 'csv'], true)) {
        return 'glyphicon-list-alt';
    }
    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
        return 'glyphicon-picture';
    }
    if (in_array($extension, ['zip', 'rar', '7z'], true)) {
        return 'glyphicon-compressed';
    }

    return 'glyphicon-file';
};

$buildFolderTree = null;
$buildFolderTree = static function (string $absolutePath, string $relative) use (&$buildFolderTree, $loadDirectoryMetadata): array {
    $children = [];
    $metadata = $loadDirectoryMetadata($absolutePath);
    $entries = scandir($absolutePath);
    if ($entries === false) {
        return $children;
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..' || $entry === '.display_names.json') {
            continue;
        }

        $childAbsolutePath = $absolutePath . '/' . $entry;
        if (!is_dir($childAbsolutePath)) {
            continue;
        }

        $childRelativePath = trim($relative . '/' . $entry, '/');
        $children[] = [
            'name' => $entry,
            'displayName' => $metadata[$entry] ?? $entry,
            'path' => $childRelativePath,
            'children' => $buildFolderTree($childAbsolutePath, $childRelativePath)
        ];
    }

    usort($children, static function (array $left, array $right): int {
        return strcasecmp($left['displayName'], $right['displayName']);
    });

    return $children;
};

$folderTree = $buildFolderTree($baseDirectoryRealPath, '');

entete('Documents', 'Documents', '5');
?>

<style>
    .actions-cell {
        white-space: nowrap;
    }

    .actions-cell .btn {
        cursor: pointer;
    }

    tr.active {
        background-color: #f5f5f5 !important;
    }
</style>

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
                foreach ($pathSegments as $segmentIndex => $segment):
                    $breadcrumbPath = $buildRelativePathForChild($breadcrumbPath, $segment);
                ?>
                    <li>
                        <a href="documents.php?path=<?php echo rawurlencode($breadcrumbPath); ?>">
                            <?php echo htmlspecialchars($displayPathSegments[$segmentIndex] ?? $segment, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- ARBORESCENCE -->
        <div class="col-sm-4 col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Arborescence</div>
                <div class="panel-body">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="<?php echo $relativePath === '' ? 'active' : ''; ?>">
                            <a href="documents.php">
                                <i class="fa-solid fa-hard-drive"></i> Racine
                            </a>
                        </li>
                    </ul>

                    <?php
                    $renderTree = null;
                    $renderTree = static function (array $nodes, string $currentPath, int $depth = 0) use (&$renderTree): void {
                        if (count($nodes) === 0) return;
                    ?>
                        <ul class="nav nav-pills nav-stacked" style="margin-left: <?php echo (int) ($depth * 14); ?>px;">
                            <?php foreach ($nodes as $node): ?>
                                <li class="<?php echo $currentPath === $node['path'] ? 'active' : ''; ?>">
                                    <a href="documents.php?path=<?php echo rawurlencode($node['path']); ?>">
                                        <i class="fa-solid fa-folder-open"></i>
                                        <?php echo htmlspecialchars($node['displayName'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </li>
                                <?php $renderTree($node['children'], $currentPath, $depth + 1); ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php };
                    $renderTree($folderTree, $relativePath);
                    ?>
                </div>
            </div>
        </div>

        <!-- LISTE DES FICHIERS -->
        <div class="col-sm-8 col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Dossier courant :
                    <strong><?php echo $relativePath === '' ? 'Racine' : htmlspecialchars(implode(' / ', $displayPathSegments), ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>

                <div class="panel-body">

                    <?php if ($isAdmin): ?>
                        <!-- ACTIONS -->
                        <div class="row">
                            <div class="col-md-6">
                                <form method="post" class="form-inline">
                                    <input type="hidden" name="action" value="create_folder">
                                    <input type="text" class="form-control input-sm" name="folder_name" placeholder="Nouveau dossier" required>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-folder-plus"></i> Nouveau
                                    </button>
                                </form>
                            </div>

                            <div class="col-md-6">
                                <form method="post" enctype="multipart/form-data" id="upload-form" class="form-inline">
                                    <input type="hidden" name="action" value="upload">
                                    <input type="file" class="form-control input-sm" name="documents[]" id="documents-input" multiple>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-upload"></i> Envoyer
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            Consultation uniquement : les actions de modification sont réservées aux administrateurs.
                        </div>
                    <?php endif; ?>

                    <hr>

                    <?php if ($isAdmin): ?>
                        <!-- DROPZONE -->
                        <div id="documents-dropzone" class="panel panel-info">
                            <div class="panel-body text-center">
                                <i class="fa-solid fa-cloud-arrow-up fa-2x"></i><br>
                                Glissez-déposez vos fichiers ici.
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- TABLE -->
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
                            <?php foreach ($items as $item): ?>
                                <?php
                                $itemName = $item['name'];
                                $itemDisplayName = $item['displayName'];
                                $itemRelativePath = $buildRelativePathForChild($relativePath, $itemName);
                                ?>

                                <tr
                                    data-item-name="<?php echo htmlspecialchars($itemName); ?>"
                                    <?php if ($item['isDirectory']): ?>
                                        data-folder-target="1"
                                        data-folder-name="<?php echo htmlspecialchars($itemName); ?>"
                                    <?php endif; ?>
                                >
                                    <!-- NOM -->
                                    <td>
                                        <?php if ($item['isDirectory']): ?>
                                            <a href="documents.php?path=<?php echo rawurlencode($itemRelativePath); ?>" draggable="<?php echo $isAdmin ? 'true' : 'false'; ?>" data-item-name="<?php echo htmlspecialchars($itemName); ?>">
                                                <i class="fa-solid fa-folder"></i>
                                                <?php echo htmlspecialchars($itemDisplayName, ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo $buildPublicFileLink($itemRelativePath); ?>" target="_blank" draggable="<?php echo $isAdmin ? 'true' : 'false'; ?>" data-item-name="<?php echo htmlspecialchars($itemName); ?>">
                                                <i class="fa-solid fa-file"></i>
                                                <?php echo htmlspecialchars($itemDisplayName, ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                    <!-- DATE -->
                                    <td><?php echo date('d/m/Y H:i', (int) $item['modifiedAt']); ?></td>

                                    <!-- TYPE -->
                                    <td><?php echo $item['isDirectory'] ? 'Dossier' : 'Fichier'; ?></td>

                                    <!-- TAILLE -->
                                    <td>
                                        <?php echo $item['isDirectory'] ? '' : number_format((float)$item['size']/1024,1,',',' ') . ' Ko'; ?>
                                    </td>

                                    <!-- ACTIONS -->
                                    <td class="actions-cell">
                                        <?php if ($isAdmin): ?>
                                            <div class="btn-group btn-group-xs" role="group" aria-label="Actions fichier">
                                                <button
                                                    type="button"
                                                    class="btn btn-default rename-trigger"
                                                    data-item-name="<?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-item-display-name="<?php echo htmlspecialchars($itemDisplayName, ENT_QUOTES, 'UTF-8'); ?>"
                                                    title="Renommer"
                                                >
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-danger delete-trigger"
                                                    data-item-name="<?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-item-display-name="<?php echo htmlspecialchars($itemDisplayName, ENT_QUOTES, 'UTF-8'); ?>"
                                                    title="Supprimer"
                                                >
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($isAdmin): ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post">
                    <input type="hidden" name="action" value="delete_item">
                    <input type="hidden" name="item_name" id="delete-item-name">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h4>
                    </div>

                    <div class="modal-body">
                        <p>Voulez-vous vraiment supprimer <strong id="delete-item-label"></strong> ?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form method="post" id="move-form" style="display:none;">
        <input type="hidden" name="action" value="move_item">
        <input type="hidden" name="item_name" id="move-item-name">
        <input type="hidden" name="target_folder" id="move-target-folder">
    </form>

    <div class="modal fade" id="renameModal" tabindex="-1" role="dialog" aria-labelledby="renameModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post">
                    <input type="hidden" name="action" value="rename_item">
                    <input type="hidden" name="item_name" id="rename-item-name">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="renameModalLabel">Renommer</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rename-new-name">Nouveau nom</label>
                            <input type="text" class="form-control" id="rename-new-name" name="new_name" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
(function () {
    const dropzone = document.getElementById('documents-dropzone');
    const fileInput = document.getElementById('documents-input');
    const uploadForm = document.getElementById('upload-form');

    const renameButtons = document.querySelectorAll('.rename-trigger');
    const deleteButtons = document.querySelectorAll('.delete-trigger');

    const renameItemNameInput = document.getElementById('rename-item-name');
    const renameNewNameInput = document.getElementById('rename-new-name');

    const deleteItemNameInput = document.getElementById('delete-item-name');
    const deleteItemLabel = document.getElementById('delete-item-label');

    const moveForm = document.getElementById('move-form');
    const moveItemInput = document.getElementById('move-item-name');
    const moveTargetInput = document.getElementById('move-target-folder');

    const draggableItems = document.querySelectorAll('a[draggable="true"][data-item-name]');
    const folderRows = document.querySelectorAll('tr[data-folder-target="1"]');

    if (dropzone && fileInput && uploadForm) {
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
    }

    Array.prototype.forEach.call(renameButtons, function (button) {
        button.setAttribute('draggable', 'false');

        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (!renameItemNameInput || !renameNewNameInput) {
                return;
            }

            const itemName = button.getAttribute('data-item-name') || '';
            const itemDisplayName = button.getAttribute('data-item-display-name') || itemName;
            renameItemNameInput.value = itemName;
            renameNewNameInput.value = itemDisplayName;

            if (window.jQuery) {
                window.jQuery('#renameModal').modal('show');
            }
        });
    });

    Array.prototype.forEach.call(deleteButtons, function (button) {
        button.setAttribute('draggable', 'false');

        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (!deleteItemNameInput || !deleteItemLabel) {
                return;
            }

            const itemName = button.getAttribute('data-item-name') || '';
            const itemDisplayName = button.getAttribute('data-item-display-name') || itemName;
            deleteItemNameInput.value = itemName;
            deleteItemLabel.textContent = itemDisplayName;

            if (window.jQuery) {
                window.jQuery('#deleteModal').modal('show');
            }
        });
    });

    Array.prototype.forEach.call(draggableItems, function (item) {
        item.addEventListener('dragstart', function (event) {
            const itemName = item.getAttribute('data-item-name') || '';
            event.dataTransfer.setData('text/plain', itemName);
            event.dataTransfer.effectAllowed = 'move';
        });
    });

    Array.prototype.forEach.call(folderRows, function (row) {
        row.addEventListener('dragover', function (event) {
            if (event.target.closest('.actions-cell')) {
                return;
            }

            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            row.classList.add('active');
        });

        row.addEventListener('dragleave', function () {
            row.classList.remove('active');
        });

        row.addEventListener('drop', function (event) {
            if (event.target.closest('.actions-cell')) {
                return;
            }

            event.preventDefault();
            row.classList.remove('active');

            if (!moveForm || !moveItemInput || !moveTargetInput) {
                return;
            }

            const sourceItem = event.dataTransfer.getData('text/plain');
            const targetFolder = row.getAttribute('data-folder-name') || '';

            if (!sourceItem || !targetFolder || sourceItem === targetFolder) {
                return;
            }

            moveItemInput.value = sourceItem;
            moveTargetInput.value = targetFolder;
            moveForm.submit();
        });
    });
})();
</script>

<?php require '../includes/footer.php'; ?>
