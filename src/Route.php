<?php
namespace Cabal\Route;

use Psr\Http\Message\RequestInterface;


class Route
{
    use OptionsTrait, MapTrait;

    const NOT_FOUND = \FastRoute\Dispatcher::NOT_FOUND;
    const METHOD_NOT_ALLOWED = \FastRoute\Dispatcher::METHOD_NOT_ALLOWED;
    const FOUND = \FastRoute\Dispatcher::FOUND;

    protected $path;

    protected $handler;

    function __construct()
    {
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
        $this->options = $this->defaultOptions($optionsOrMethod);
        $this->path = $path;
        $this->handler = $handler;
        return $this;
    }


    public function loop(RequestInterface $request = null, \FastRoute\RouteCollector $routeCollector, $parentOptions = '')
    {
        $options = $this->mergeOptions($parentOptions);

        if ($request && $options['scheme'] && !in_array($request->getUri()->getScheme(), (array)$options['scheme'])) {
            return;
        }
        if ($request && $options['host'] && !in_array($request->getUri()->getHost(), (array)$options['host'])) {
            return;
        }

        $handler = $this->handler;
        if (is_string($handler)) {
            $handler = $options['namespace'] ? "\\{$options['namespace']}\\{$this->handler}" : "\\{$this->handler}";
        }
        $path = '/' . ltrim("{$options['basePath']}/", '/') . trim($this->path, '/');

        $routeCollector->addRoute(
            $options['method'],
            $path,
            [
                'handler' => $handler,
                'middleware' => $options['middleware'],
            ]
        );
    }
}