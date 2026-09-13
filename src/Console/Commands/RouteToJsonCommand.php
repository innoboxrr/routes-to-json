<?php

namespace Innoboxrr\RoutesToJson\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;

class RouteToJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate routes for javascript in JSON format';


    protected $router;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct();

        $this->router = $router;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = [];

        foreach ($this->router->getRoutes() as $route) {
            $name = $route->getName();

            // Una ruta sin nombre no se puede pedir por nombre desde el
            // frontend, y todas compartirian la clave "" pisandose entre si.
            if ($name === null || $name === '') {
                continue;
            }

            $routes[$name] = $route->uri();
        }

        $path = $this->outputPath();

        // Verificar si el directorio existe, si no, crearlo
        $directory = dirname($path);

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true); // Crear directorio con permisos 0755
        }

        // Guardar el archivo JSON
        File::put($path, json_encode($routes, JSON_PRETTY_PRINT));

        $this->info('Rutas generadas correctamente en formato JSON.');

        return 0;
    }

    /**
     * La ruta configurada, o la de por defecto si la configuracion la deja
     * vacia (JSON_ROUTES_FILE= en el .env). Una ruta relativa, como
     * JSON_ROUTES_FILE=resources/react/assets/json/routes.json, se resuelve
     * contra la raiz del proyecto y no contra el directorio de trabajo, que
     * fuera de artisan no tiene por que ser la raiz.
     */
    protected function outputPath(): string
    {
        $path = config('routes-to-json.path');

        if (! is_string($path) || $path === '') {
            return resource_path('vue/assets/json/routes.json');
        }

        $absolute = str_starts_with($path, '/')
            || str_starts_with($path, '\\')
            || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1
            || str_contains($path, '://');

        return $absolute ? $path : base_path($path);
    }
}
