<?php
namespace Cabal\Route;

use FastRoute\RouteCollector;

class RawRouteCollector extends RouteCollector
{
    protected $raws = [];
    protected $nameds = [];

    public function addRoute($httpMethod, $route, $handler)
    {
        $this->raws[] = [$httpMethod, $route, $handler];
        if (isset($handler['options']['name']) && $handler['options']['name']) {
            foreach ((array)$handler['options']['name'] as $name) {
                $this->nameds[$name] = [$httpMethod, $route, $handler];
            }
        }
    }

    public function getData()
    {
        return $this->raws;
    }
    public function getNameds()
    {
        return $this->nameds;
    }
}
