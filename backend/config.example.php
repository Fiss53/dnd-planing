<?php
// Copie ce fichier en "config.php" (a cote de celui-ci) et remplis tes vraies
// valeurs. config.php est ignore par git : il ne doit JAMAIS etre commit
// puisqu'il contient des identifiants et le hash du mot de passe admin.

// -- Base de donnees (informations disponibles dans l'espace client OVH) --
define('DB_HOST', 'localhost');
define('DB_NAME', 'nom_de_ta_base');
define('DB_USER', 'ton_utilisateur');
define('DB_PASS', 'ton_mot_de_passe');

// -- Mot de passe admin (partage entre les MJ pour creer/editer des sessions) --
// Ne mets JAMAIS le mot de passe en clair ici : genere son hash avec
//   php backend/tools/hash_password.php "monMotDePasse"
// et colle le resultat ci-dessous.
define('ADMIN_PASSWORD_HASH', '');
