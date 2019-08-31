<?php

namespace JustIversen\CspSuggest;

use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class Routes
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Get the routes relevant for CSP scanning.
     *
     * @return Collection
     */
    protected function get(): Collection
    {
        $routes = collect($this->router->getRoutes())->map(function ($route) {
            return $this->getRouteInformation($route);
        });

        return $routes->filter(function ($route) {
            return Str::contains($route['method'], 'GET');
        });
    }
    
    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array
     */
    protected function getRouteInformation(Route $route): array
    {
        return [
            'domain' => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri'    => $route->uri(),
            'name'   => $route->getName(),
        ];
    }


}
