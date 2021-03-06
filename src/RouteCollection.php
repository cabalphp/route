<?php
namespace Cabal\Route;

use function FastRoute\simpleDispatcher;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Request;


class RouteCollection
{
    use OptionsTrait, MapTrait;

    /**
     * Undocumented variable
     *
     * @var \Cabal\Route\Route[]
     */
    protected $routes = [];

    /**
     * @var \Cabal\Route\RouteCollection
     */
    protected $root;

    protected $routeMap = [];

    function __construct($optionsOrMethod = '', $root = null)
    {
        $this->options = $optionsOrMethod;
        if ($root) {
            $this->root  = $root;
        }
    }

    /**
     * Undocumented function
     *
     * @param string|mixed $optionsOrMethod
     * @param callable $callback
     * @return \Cabal\Route\RouteCollection
     */
    public function group($optionsOrMethod, $callback)
    {
        $collection = new RouteCollection($optionsOrMethod, $this);
        $this->routes[] = $collection;
        $callback($collection);
        return $collection;
    }

    public function occupyName($name)
    {
        if ($this->root) {
            $this->root->occupyName($name);
        } else {
            if (isset($this->routeMap[$name])) {
                throw new \Exception("route name '{$name}' must be unique.");
            }
            $this->routeMap[$name] = 1;
        }
    }

    /**
     * Undocumented function
     *
     * @param string $optionsOrMethod
     * @param string|mixed $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function map($optionsOrMethod, $path, $handler)
    {
        $route = new Route($this);
        $route->map($optionsOrMethod, $path, $handler);
        $this->routes[] = $route;
        return $route;
    }

    public function getDispatcher(RequestInterface $request)
    {
        $id = implode('://', [
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
        ]);
        if (!isset($this->cached[$id])) {
            $routeCollector = new \FastRoute\RouteCollector(
                new \FastRoute\RouteParser\Std,
                new \FastRoute\DataGenerator\GroupCountBased()
            );
            $this->loop($request, $routeCollector, $this->options);
            $this->cached[$id] = $routeCollector->getData();
        }
        return new \FastRoute\Dispatcher\GroupCountBased($this->cached[$id]);
    }

    public function dispatch(RequestInterface $request)
    {
        $dispatcher = $this->getDispatcher($request);
        $result = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        while (count($result) < 3) {
            $result[] = [];
        }
        return $result;
    }

    /**
     * 
     * @return \Cabal\Route\RawRouteCollector
     */
    public function getRoutesRaw()
    {
        $id = '_RAW_';
        $routeCollector = new RawRouteCollector(
            new \FastRoute\RouteParser\Std,
            new \FastRoute\DataGenerator\GroupCountBased()
        );

        $this->loop(null, $routeCollector, $this->options);
        $this->cached[$id] = $routeCollector->getData();
        return $this->cached[$id];
    }
    public function getNamedRoute($name = null)
    {
        $id = '_NAMEDS_';
        $routeCollector = new RawRouteCollector(
            new \FastRoute\RouteParser\Std,
            new \FastRoute\DataGenerator\GroupCountBased()
        );

        $this->loop(null, $routeCollector, $this->options);
        $this->cached[$id] = $routeCollector->getNameds();

        if ($name) {
            return $this->cached[$id][$name] ?? false;
        }
        return $this->cached[$id];
    }

    public function simpleDispatch($method, $uri)
    {
        if (!isset($this->cached['_SIMPLE_'])) {
            $routeCollector = new \FastRoute\RouteCollector(
                new \FastRoute\RouteParser\Std,
                new \FastRoute\DataGenerator\GroupCountBased()
            );
            $this->loop(null, $routeCollector, $this->options);
            $this->cached['_SIMPLE_'] = $routeCollector->getData();
        }
        $dispatcher = new \FastRoute\Dispatcher\GroupCountBased($this->cached['_SIMPLE_']);

        $result = $dispatcher->dispatch($method, $uri);
        while (count($result) < 3) {
            $result[] = null;
        }
        return $result;
    }

    public function loop(RequestInterface $request = null, \FastRoute\RouteCollector $routeCollector, $parentOptions = '')
    {
        $options = $this->mergeOptions($parentOptions);

        foreach ($this->routes as $route) {
            $route->loop($request, $routeCollector, $options);
        }
    }
}
