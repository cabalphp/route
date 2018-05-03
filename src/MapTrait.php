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
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function get($path, $hander)
    {
        return $this->map('GET', $path, $hander);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function ws($path, $hander)
    {
        return $this->map('WS', $path, $hander);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function post($path, $hander)
    {
        return $this->map('POST', $path, $hander);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function put($path, $hander)
    {
        return $this->map('PUT', $path, $hander);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function patch($path, $hander)
    {
        return $this->map('PATCH', $path, $hander);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function delete($path, $hander)
    {
        return $this->map('DELETE', $path, $hander);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function head($path, $hander)
    {
        return $this->map('HEAD', $path, $hander);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function options($path, $hander)
    {
        return $this->map('OPTIONS', $path, $hander);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|mixed $hander
     * @return \Cabal\Route\Route
     */
    public function any($path, $hander)
    {
        return $this->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'], $path, $hander);
    }
}