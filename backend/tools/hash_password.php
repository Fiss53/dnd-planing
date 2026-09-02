<?php
// Utilitaire en ligne de commande : genere le hash a coller dans
// backend/config.php (ADMIN_PASSWORD_HASH).
//
// Usage :
//   php backend/tools/hash_password.php "monMotDePasse"

if ($argc < 2) {
    fwrite(STDERR, "Usage: php hash_password.php \"mot de passe\"\n");
    exit(1);
}

echo password_hash($argv[1], PASSWORD_DEFAULT) . "\n";
