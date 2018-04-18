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

    function __construct($optionsOrMethod = '')
    {
        $this->options = $optionsOrMethod;
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
        $collection = new RouteCollection($optionsOrMethod);
        $this->routes[] = $collection;
        $callback($collection);
        return $collection;
    }

    /**
     * Undocumented function
     *
     * @param string $optionsOrMethod
     * @param string|mixed $path
     * @param \Cabal\Route\Route $handler
     * @return void
     */
    public function map($optionsOrMethod, $path, $handler)
    {
        $route = new Route();
        $route->map($optionsOrMethod, $path, $handler);
        $this->routes[] = $route;
        return $route;
    }

    public function __call($method, $params)
    {
        $route = new Route();
        $this->routes[] = $route;
        $route->$method(...$params);
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
            $result[] = null;
        }
        return $result;
    }

    public function loop(RequestInterface $request, \FastRoute\RouteCollector $routeCollector, $parentOptions = '')
    {
        $options = $this->mergeOptions($parentOptions);

        foreach ($this->routes as $route) {
            $route->loop($request, $routeCollector, $options);
        }
    }
}