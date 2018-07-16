<?php
namespace Cabal\Route;

use FastRoute\RouteCollector;

class RawRouteCollector extends RouteCollector
{
    protected $raws = [];

    public function addRoute($httpMethod, $route, $handler)
    {
        $this->raws[] = [$httpMethod, $route, $handler];
    }

    public function getData()
    {
        return $this->raws;
    }
}