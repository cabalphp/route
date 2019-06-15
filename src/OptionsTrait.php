<?php
namespace Cabal\Route;

trait OptionsTrait
{
    protected $options = [];

    public function name($name)
    {
        if (isset($this->options['name']) && $this->options['name']) {
            if (!is_array($this->options['name'])) {
                $this->options['name'] = [$this->options['name']];
            }
            $this->options['name'][] = $name;
        } else {
            $this->options['name'] = $name;
        }
        $this->collection->occupyName($name, $this);
        return $this;
    }

    public function getName()
    {
        return $this->options['name'] ?? '';
    }

    public function getHost()
    {
        return $this->options['host'] ?? '';
    }

    public function host($host)
    {
        $this->options['host'] = $host;
        return $this;
    }

    public function getNamespace()
    {
        return $this->options['namespace'] ?? '';
    }

    public function namespace($namespace)
    {
        $this->options['namespace'] = $namespace;
        return $this;
    }

    public function getScheme()
    {
        return $this->options['scheme'] ?? '';
    }

    public function scheme($scheme)
    {
        $this->options['scheme'] = $scheme;
        return $this;
    }

    public function getMiddleware()
    {
        return $this->options['middleware'] ?? '';
    }

    public function middleware($middleware)
    {
        $this->options['middleware'] = $middleware;
        return $this;
    }

    protected function defaultOptions($optionsOrMethod)
    {
        $defaultOptions = [
            'name' => '',
            'method' => '',
            'host' => '',
            'namespace' => '',
            'scheme' => '',
            'basePath' => '',
            'middleware' => [],
        ];
        $keys = is_array($optionsOrMethod) ? array_keys($optionsOrMethod) : [];
        if (!is_array($optionsOrMethod) || current($keys) === 0) {
            $defaultOptions['method'] = $optionsOrMethod ?: 'GET';
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
            'name' => $options['name'] ?: $parentOptionsOrMethod['name'],
            'method' => $options['method'] ?: $parentOptionsOrMethod['method'],
            'host' => $options['host'] ?: $parentOptionsOrMethod['host'],
            'scheme' => $options['scheme'] ?: $parentOptionsOrMethod['scheme'],
            'middleware' => $parentOptionsOrMethod['middleware'] ? array_merge($parentOptionsOrMethod['middleware'], $options['middleware']) : $options['middleware'],
            'namespace' => trim(trim($parentOptionsOrMethod['namespace'], '\\') . '\\' . trim($options['namespace'], '\\'), '\\'),
            'basePath' => trim(trim($parentOptionsOrMethod['basePath'], '/') . '/' . trim($options['basePath'], '/'), '/'),
        ];
    }
}
