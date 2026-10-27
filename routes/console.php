<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/* models:protect — chiffre les modèles 3D de la branche Arts. Lit les .gltf / .glb
   sources dans resources/models/ (hors dépôt : voir .gitignore, ce dossier ne doit
   JAMAIS être publié) et écrit public/models/<nom>.lwm, seul fichier servi par le site
   (AES-256-GCM, déchiffré en mémoire par ui/model-crypto.js). La clé est générée une
   fois dans resources/js/ui/model-key.js (versionné : le script doit la contenir) ;
   --rotate en crée une nouvelle (tous les .lwm sont alors réécrits).
   À relancer à chaque ajout ou modification d'un modèle, puis committer les .lwm. */
Artisan::command('models:protect {--rotate}', function () {
    $src = resource_path('models');
    $dst = public_path('models');
    $keyFile = resource_path('js/ui/model-key.js');

    $key = null;
    if (!$this->option('rotate') && File::exists($keyFile)
        && preg_match("/MODEL_KEY = '([A-Za-z0-9+\\/=]+)'/", File::get($keyFile), $m)) {
        $key = base64_decode($m[1], true);
    }
    if ($key === false || $key === null || strlen($key) !== 32) {
        $key = random_bytes(32);
        File::put($keyFile, "/* Clé de déchiffrement des modèles protégés (.lwm) — générée par `php artisan models:protect`.\n"
            . "   Ne pas modifier à la main : la changer rend tous les .lwm existants illisibles. */\n"
            . "export const MODEL_KEY = '" . base64_encode($key) . "';\n");
        $this->warn('Nouvelle clé générée -> ' . $keyFile);
    }

    File::ensureDirectoryExists($dst);
    $files = array_merge(glob($src . '/*.gltf') ?: [], glob($src . '/*.glb') ?: []);
    if (!$files) { $this->error("Aucun .gltf / .glb dans $src"); return 1; }

    foreach ($files as $f) {
        $iv = random_bytes(12);
        $enc = openssl_encrypt(File::get($f), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
        if ($enc === false) { $this->error('Chiffrement impossible : ' . basename($f)); return 1; }
        $out = $dst . '/' . pathinfo($f, PATHINFO_FILENAME) . '.lwm';
        File::put($out, 'LWM1' . $iv . $enc . $tag);   // WebCrypto attend le tag à la suite du chiffré
        $this->info(sprintf('OK  %-34s %7.1f Ko', basename($out), filesize($out) / 1024));
    }

    // Plus aucune copie en clair dans public/models (sinon la protection ne sert à rien).
    foreach (array_merge(glob($dst . '/*.gltf') ?: [], glob($dst . '/*.glb') ?: []) as $plain) {
        File::delete($plain);
        $this->warn('Supprimé (en clair) : public/models/' . basename($plain));
    }
})->purpose('Chiffre les modèles 3D (resources/models -> public/models/*.lwm)');

/* export:static — fige chaque route GET nommée (aucune n'utilise de DB ni
   d'auth, voir routes/web.php) en HTML statique dans dist/, pour un
   hébergement qui ne sait servir que des fichiers (GitHub Pages). À lancer
   après `npm run build` (sinon @vite() pointe vers le serveur de dev) et
   avec public/hot absent. Le kernel HTTP est réutilisé pour chaque requête
   plutôt que relancé : plus rapide, et évite de rebooter le conteneur. */
Artisan::command('export:static {--out=dist}', function () {
    $out = base_path($this->option('out'));
    File::deleteDirectory($out);
    File::makeDirectory($out, recursive: true);

    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);

    /* Routes paramétrées : une route avec {slug} ne se fige pas toute seule.
       On connaît ici la liste des valeurs (le catalogue des modèles 3D) ;
       toute autre route paramétrée reste ignorée avec un avertissement. */
    $expand = [
        'arts.model' => fn () => array_map(fn ($m) => '/arts-modele/' . $m['slug'], \App\Support\ModelCatalog::all()),
    ];

    $uris = [];
    foreach (Route::getRoutes() as $route) {
        if (!in_array('GET', $route->methods(), true)) continue;
        $name = $route->getName();
        if (!$name || str_starts_with($name, 'storage.')) continue;
        $uri = '/' . ltrim($route->uri(), '/');
        if (str_contains($uri, '{')) {
            if (isset($expand[$name])) { array_push($uris, ...$expand[$name]()); }
            else { $this->warn("Skip $uri (paramétrée)"); }
            continue;
        }
        $uris[] = $uri;
    }

    foreach ($uris as $uri) {

        /* Request::create($uri) seul retombe sur http://localhost (hôte par
           défaut de Symfony) : une fois ce Request lié au conteneur,
           UrlGenerator s'aligne dessus plutôt que sur config('app.url').
           Passer l'URL absolue force le bon domaine dans asset()/route(). */
        $request = Request::create(config('app.url').$uri, 'GET');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        if ($response->getStatusCode() !== 200) {
            $this->error("$uri -> HTTP {$response->getStatusCode()}, export interrompu");
            exit(1);
        }

        $target = ($uri === '/' ? $out.'/index.html' : $out.$uri.'/index.html');
        File::ensureDirectoryExists(dirname($target));
        File::put($target, $response->getContent());
        $this->info("OK  $uri");
    }

    // Assets réels (images, vidéos, GLB, textures, build/ Vite compilé...).
    File::copyDirectory(public_path(), $out);
    // Artefacts serveur PHP sans objet sur un hébergeur statique
    // (robots.txt reste : un hébergeur statique le sert tel quel).
    File::delete(array_filter([
        $out.'/index.php', $out.'/.htaccess', $out.'/hot',
    ], fn ($f) => File::exists($f)));

    File::put($out.'/.nojekyll', '');
    File::put($out.'/CNAME', 'lakeust.works');

    $this->info("Export statique terminé -> {$out}");
})->purpose('Fige le site en HTML statique pour GitHub Pages');
