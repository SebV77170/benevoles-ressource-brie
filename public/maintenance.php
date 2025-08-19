<?php
// maintenance.php
// --- En-têtes HTTP adaptés à la maintenance ---
http_response_code(503);                 // 503 Service Unavailable
header('Retry-After: 3600');             // Indique aux clients de réessayer dans 1h
header('Cache-Control: no-store');       // Évite la mise en cache

require('../src/config.php');
require('../src/bootstrap.php');

// (Optionnel) date/heure de fin estimée pour affichage
$maintenance_end = null; // Exemple: '2025-08-19 22:00' (heure serveur) ou null si inconnu

entete('Site en maintenance', 'Maintenance', '0');
?>

<div class="container text-center d-flex flex-column justify-content-center align-items-center" style="min-height:70vh">
  <div class="mb-4">
    <!-- Petit pictogramme inline, pas de dépendance externe -->
    <svg width="80" height="80" viewBox="0 0 24 24" role="img" aria-label="Maintenance">
      <path d="M22.7 19.3l-5.1-5.1a7.5 7.5 0 01-8.7-9.8l3.1 3.1 2.8-2.8-3.1-3.1A7.5 7.5 0 0118 7.2l5.1 5.1-2.4 2.4-2.1-2.1-2.1 2.1 2.1 2.1-2.4 2.4 2.4 2.4 2.4-2.4 1.7 1.7 1-1z" fill="currentColor" />
    </svg>
  </div>

  <h1 class="mb-3">Nous revenons très vite</h1>
  <p class="lead mb-1">Le site est actuellement en maintenance planifiée.</p>
  <?php if (!empty($maintenance_end)): ?>
    <p class="mb-3">Retour estimé : <strong><?php echo htmlspecialchars($maintenance_end); ?></strong></p>
  <?php endif; ?>
  <p class="text-muted mb-4">Merci pour votre patience.</p>

  <div class="d-flex gap-2">
    <button class="stdbouton" onclick="location.reload()">Réessayer</button>
    <a class="btn btn-outline-secondary" href="mailto:contact@exemple.com">Nous contacter</a>
  </div>

  <div class="mt-4">
    <small class="text-muted">Code d’état : 503 Service Unavailable</small>
  </div>
</div>

<?php require '../includes/footer.php'; ?>
