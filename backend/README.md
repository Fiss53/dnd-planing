# Backend

API PHP simple (pas de framework) pour le Calendrier Lune Rousse. Expose les
sessions de jeu en JSON et protege la creation/modification derriere le mot
de passe admin partage.

## Structure

- `index.php` — point d'entree unique, route les requetes vers `routes/`.
- `routes/` — un fichier par action (`login`, `logout`, `sessions_list`, `sessions_create`, `sessions_update`).
- `db.php` — connexion MySQL (PDO).
- `helpers.php` — reponses JSON, verification d'auth, mise en forme des sessions.
- `config.php` — **a creer toi-meme**, jamais commit (voir plus bas).
- `schema.sql` — a importer une fois dans la base MySQL.

## 1. Configurer

```bash
cp backend/config.example.php backend/config.php
```

Remplis `backend/config.php` avec :
- les identifiants MySQL (fournis par OVH, voir plus bas)
- le hash du mot de passe admin, genere avec :

```bash
php backend/tools/hash_password.php "le-mot-de-passe-que-tu-veux"
```

Colle le resultat dans `ADMIN_PASSWORD_HASH`.

## 2. Tester en local

Il faut PHP et MySQL installes en local (ex: via XAMPP/WAMP sur Windows, qui
installe les deux d'un coup).

1. Cree une base de donnees locale et importe `backend/schema.sql` dedans.
2. Renseigne les identifiants locaux dans `backend/config.php`.
3. Lance le serveur PHP :

```bash
php -S localhost:8000 -t backend backend/router.php
```

4. Dans un autre terminal, lance le front comme d'habitude (`npm run dev`).
   Le proxy deja configure dans `vite.config.js` fait passer les appels
   `/api/...` du front vers `localhost:8000` automatiquement — rien d'autre
   a faire, `.env` pointe deja vers `VITE_API_URL=/api`.

## 3. Deployer sur OVH

Important : la racine du domaine (`www/`) heberge deja le site WordPress de
l'asso. Tout ce projet est donc deploye dans un sous-dossier **`planning/`**,
jamais a la racine.

1. **Base de donnees** : dans l'espace client OVH, cree une base MySQL
   (Hebergements → ton hebergement → Bases de donnees). Note le host (le
   champ "Adresse du serveur", pas le nom du serveur physique), le nom de la
   base, l'utilisateur et le mot de passe fournis.
2. **Importer le schema** : ouvre phpMyAdmin depuis l'espace client OVH,
   selectionne ta base, onglet "SQL", colle le contenu de `backend/schema.sql`,
   execute.
3. **Uploader le backend** : envoie tout le contenu de `backend/` par FTP dans
   `www/planning/api/` (pense a activer l'affichage des fichiers caches dans
   ton client FTP pour que `.htaccess` parte bien).
4. **Creer `config.php` sur le serveur** : cree-le directement via FTP (ou
   l'explorateur de fichiers OVH) avec les vrais identifiants MySQL d'OVH et
   le hash du mot de passe admin. Ce fichier ne doit jamais passer par git.
5. **Compiler et uploader le front** :

```bash
npm run build
```

   `npm run build` charge automatiquement `.env.production`
   (`VITE_API_URL=/planning/api`) et compile avec `base: /planning/` (voir
   `vite.config.js`), donc les chemins generes sont deja corrects.

   Envoie **le contenu** du dossier `dist/` (pas le dossier lui-meme) dans
   `www/planning/` — a cote du dossier `api/`, pas dedans. Comme WordPress vit
   a la racine et que `planning/` est un vrai dossier sur le disque, son
   `.htaccess` de reecriture d'URL ne l'intercepte pas.

## Notes

- Le mot de passe admin est unique et partage entre tous les MJ (pas de
  compte individuel pour l'instant) — c'est un choix assume pour rester
  simple, pas un oubli.
- Les dates et tags de chaque session sont stockes en base sous forme de
  texte JSON (colonnes `dates` et `tags`) et parses cote front, exactement
  comme le fichier `src/data/sessions.json` d'origine.
