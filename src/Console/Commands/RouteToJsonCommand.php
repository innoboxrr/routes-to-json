<?php

namespace Innoboxrr\RoutesToJson\Console\Commands;

use File;
use Illuminate\Routing\Router;
use Illuminate\Console\Command;

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
            
            $routes[$route->getName()] = $route->uri();

        }

        $path = config('routes-to-json.path');

        File::put($path, json_encode($routes, JSON_PRETTY_PRINT));

        return 0;

    }
    
}
