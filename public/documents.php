<?php
session_start();
require '../actions/users/securityAction.php';

require '../src/bootstrap.php';
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';

$uploadDirectory = dirname(__DIR__) . '/fichiers';
$uploadedFiles = [];
$uploadErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documents'])) {
    header('Content-Type: application/json; charset=utf-8');

    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Le dossier de destination est inaccessible.'
        ]);
        exit;
    }

    $documents = $_FILES['documents'];
    $total = is_array($documents['name']) ? count($documents['name']) : 0;

    for ($i = 0; $i < $total; $i++) {
        if ((int) $documents['error'][$i] !== UPLOAD_ERR_OK) {
            $uploadErrors[] = $documents['name'][$i];
            continue;
        }

        $originalName = basename($documents['name'][$i]);
        $safeName = preg_replace('/[^A-Za-z0-9._ -]/', '_', $originalName);
        $safeName = trim((string) $safeName);

        if ($safeName === '') {
            $uploadErrors[] = $originalName;
            continue;
        }

        $targetPath = $uploadDirectory . '/' . $safeName;
        $pathInfo = pathinfo($safeName);
        $filename = $pathInfo['filename'] ?? 'document';
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $counter = 1;

        while (file_exists($targetPath)) {
            $targetPath = $uploadDirectory . '/' . $filename . '_' . $counter . $extension;
            $counter++;
        }

        if (move_uploaded_file($documents['tmp_name'][$i], $targetPath)) {
            $uploadedFiles[] = basename($targetPath);
        } else {
            $uploadErrors[] = $originalName;
        }
    }

    echo json_encode([
        'success' => count($uploadedFiles) > 0,
        'uploaded' => $uploadedFiles,
        'errors' => $uploadErrors
    ]);
    exit;
}

$filesInDirectory = [];
if (is_dir($uploadDirectory)) {
    $filesInDirectory = array_values(array_filter(scandir($uploadDirectory), static function ($file) use ($uploadDirectory) {
        return $file !== '.' && $file !== '..' && is_file($uploadDirectory . '/' . $file);
    }));
    natcasesort($filesInDirectory);
    $filesInDirectory = array_values($filesInDirectory);
}

entete('Documents','Documents','5');
?>

<div class="doc">
    <div class="dropzone-wrapper">
        <h3>Déposer des documents</h3>
        <div id="documents-dropzone" class="documents-dropzone">
            Glissez-déposez vos fichiers ici<br>
            <span>ou cliquez pour sélectionner des fichiers</span>
        </div>
        <input type="file" id="documents-input" name="documents[]" multiple hidden>
        <p id="documents-feedback" class="documents-feedback"></p>
    </div>

    <ul>

        <h2>Association</h2>
        <a href="../fichiers/ri.pdf" target="_blank"><li id="bleu">Règlement intérieur</li></a>
        <a href="../fichiers/statuts2025.pdf" target="_blank"><li id="bleu">Statuts de l'association</li></a>

        <h2>Véhicules</h2>
        <a href="../fichiers/conducteur.pdf" target="_blank"><li id="bleu">Accréditation conducteur</li></a>
        <a href="../fichiers/utilisation_vehicule.pdf" target="_blank"><li id="bleu">Ordre de mission véhicule</li></a>

        <h2>Bricoleurs</h2>
        <a href="../fichiers/habilitation.pdf" target="_blank"><li id="bleu">Habilitation bricoleur</li></a>

        <h2>Documents déposés</h2>
        <?php if (count($filesInDirectory) === 0): ?>
            <li id="bleu">Aucun document</li>
        <?php else: ?>
            <?php foreach ($filesInDirectory as $file): ?>
                <?php $encodedFile = rawurlencode($file); ?>
                <a href="../fichiers/<?php echo $encodedFile; ?>" target="_blank">
                    <li id="bleu"><?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?></li>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<script>
    (function () {
        const dropzone = document.getElementById('documents-dropzone');
        const fileInput = document.getElementById('documents-input');
        const feedback = document.getElementById('documents-feedback');

        const sendFiles = (files) => {
            if (!files || files.length === 0) {
                return;
            }

            const formData = new FormData();
            [...files].forEach((file) => formData.append('documents[]', file));

            feedback.textContent = 'Téléversement en cours...';
            fetch('documents.php', {
                method: 'POST',
                body: formData
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        feedback.textContent = 'Téléversement terminé. Rechargement...';
                        window.location.reload();
                        return;
                    }

                    feedback.textContent = 'Aucun fichier n\'a pu être téléversé.';
                })
                .catch(() => {
                    feedback.textContent = 'Erreur lors du téléversement des fichiers.';
                });
        };

        dropzone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => sendFiles(fileInput.files));

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.remove('is-dragging');
            });
        });

        dropzone.addEventListener('drop', (event) => {
            sendFiles(event.dataTransfer.files);
        });
    })();
</script>

<?php require '../includes/footer.php'; ?>
