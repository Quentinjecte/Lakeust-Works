<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

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

    foreach (Route::getRoutes() as $route) {
        if (!in_array('GET', $route->methods(), true)) continue;
        $name = $route->getName();
        if (!$name || str_starts_with($name, 'storage.')) continue;
        $uri = '/' . ltrim($route->uri(), '/');
        if (str_contains($uri, '{')) { $this->warn("Skip $uri (paramétrée)"); continue; }

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
    // Artefacts serveur PHP sans objet sur un hébergeur statique.
    File::delete(array_filter([
        $out.'/index.php', $out.'/.htaccess', $out.'/hot', $out.'/robots.txt',
    ], fn ($f) => File::exists($f)));

    File::put($out.'/.nojekyll', '');
    File::put($out.'/CNAME', 'lakeust.works');

    $this->info("Export statique terminé -> {$out}");
})->purpose('Fige le site en HTML statique pour GitHub Pages');
