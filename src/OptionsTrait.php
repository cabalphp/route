<?php
namespace Cabal\Route;

trait OptionsTrait
{
    protected $options = [];

    public function host($host)
    {
        $this->options['host'] = $host;
    }

    public function namespace($namespace)
    {
        $this->options['namespace'] = $namespace;
    }

    public function scheme($scheme)
    {
        $this->options['scheme'] = $scheme;
    }

    public function middleware($middleware)
    {
        $this->options['middleware'] = $middleware;
    }

    protected function defaultOptions($optionsOrMethod)
    {
        $defaultOptions = [
            'method' => '',
            'host' => '',
            'namespace' => '',
            'scheme' => '',
            'basePath' => '',
            'middleware' => [],
        ];
        $keys = is_array($optionsOrMethod) ? array_keys($optionsOrMethod) : [];
        if (!is_array($optionsOrMethod) || current($keys) === 0) {
            $defaultOptions['method'] = $optionsOrMethod ? : 'GET';
        } else {
            $defaultOptions = array_merge($defaultOptions, $optionsOrMethod);
        }
        return $defaultOptions;
    }

    public function mergeOptions($parentOptionsOrMethod)
    {
        $options = $this->defaultOptions($this->options);
        $parentOptionsOrMethod = $this->defaultOptions($parentOptionsOrMethod);
        return [
            'method' => $options['method'] ? : $parentOptionsOrMethod['method'],
            'host' => $options['host'] ? : $parentOptionsOrMethod['host'],
            'scheme' => $options['scheme'] ? : $parentOptionsOrMethod['scheme'],
            'middleware' => $parentOptionsOrMethod['middleware'] ? array_merge($parentOptionsOrMethod['middleware'], $options['middleware']) : $options['middleware'],
            'namespace' => trim(trim($parentOptionsOrMethod['namespace'], '\\') . '\\' . trim($options['namespace'], '\\'), '\\'),
            'basePath' => trim(trim($parentOptionsOrMethod['basePath'], '/') . '/' . trim($options['basePath'], '/'), '/'),
        ];
    }


}