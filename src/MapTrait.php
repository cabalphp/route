<?php
namespace Cabal\Route;

trait MapTrait
{
    /**
     * Undocumented function
     *
     * @param string $optionsOrMethod
     * @param string|mixed $path
     * @param \Cabal\Route\Route $handler
     * @return void
     */
    abstract public function map($optionsOrMethod, $path, $handler);

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function get($path, $handler)
    {
        return $this->map('GET', $path, $handler);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function ws($path, $handler)
    {
        return $this->map('WS', $path, $handler);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function post($path, $handler)
    {
        return $this->map('POST', $path, $handler);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function put($path, $handler)
    {
        return $this->map('PUT', $path, $handler);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function patch($path, $handler)
    {
        return $this->map('PATCH', $path, $handler);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function delete($path, $handler)
    {
        return $this->map('DELETE', $path, $handler);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function head($path, $handler)
    {
        return $this->map('HEAD', $path, $handler);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function options($path, $handler)
    {
        return $this->map('OPTIONS', $path, $handler);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $handler
     * @return \Cabal\Route\Route
     */
    public function any($path, $handler)
    {
        return $this->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'], $path, $handler);
    }
}