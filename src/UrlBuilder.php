<?php
namespace Cabal\Route;

use function FastRoute\simpleDispatcher;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Request;


class UrlBuilder
{
    /**
     * @var \Cabal\Route\RouteCollection
     */
    protected $routeCollection = [];

    protected $currentRouteName;

    protected $currentInfo = [];

    const DEFAULT_DISPATCH_REGEX = '[^/]+';

    const VARIABLE_REGEX = <<<'REGEX'
\{
    \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
    (?:
        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
    )?
\}
REGEX;

    function __construct(RouteCollection $routeCollection, $currentRouteName = '', $currentHost = '', $currentScheme = 'http', $currentParams = [])
    {
        $this->routeCollection = $routeCollection;;
        $this->currentRouteName = $currentRouteName;
        $this->currentInfo = [
            'host' => $currentHost,
            'scheme' => $currentScheme,
            'params' => $currentParams,
        ];
    }

    public function route($routeName, $params = [], $options = [])
    {
        $options = array_merge([
            'full' => false,
        ], $options);
        $route = $this->routeCollection->getNamedRoute($routeName);
        if (!$route) {
            throw new \Exception("route {$routeName} is undefined");
        }
        list(, $uri, $routeOptions) = $route;
        $routeOptions = $routeOptions['options'];
        $scheme = $options['scheme'] ?? ($routeOptions['scheme'] ?? '');
        $host = $routeOptions['host'] ?? '';


        if (preg_match_all(
            '~' . self::VARIABLE_REGEX . '~x',
            $uri,
            $matches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            $offset = 0;
            $routeData = [];
            foreach ($matches as $set) {
                if ($set[0][1] > $offset) {
                    $routeData[] = explode('[', substr($uri, $offset, $set[0][1] - $offset));
                }
                $routeData[] = [
                    true,
                    $set[0][0],
                    $set[1][0],
                    isset($set[2]) ? trim($set[2][0]) : self::DEFAULT_DISPATCH_REGEX
                ];
                $offset = $set[0][1] + strlen($set[0][0]);
            }

            if ($offset !== strlen($uri)) {
                // $routeData[] = explode(']', substr($uri, $offset));
                $routeData[] = substr($uri, $offset);
            }
            $parsedUri = [];
            $optional = [];
            // var_dump($routeData);
            foreach ($routeData as $data) {
                if ($data[0] === true) {
                    list(, $placeholder, $name, $regex) = $data;
                    if (isset($params[$name])) {
                        $parsedUri[] = array_shift($optional);
                        $parsedUri[] = $params[$name];
                        unset($params[$name]);
                    } elseif (count($optional) > 0) {
                        break;
                    } else {
                        throw new \Exception("param '{$name}' must provide");
                    }
                } elseif (is_array($data)) {
                    $parsedUri[] = trim($data[0], ']');
                    if (isset($data[1])) {
                        $optional[] = $data[1];
                    }
                } else {
                    $parsedUri[] = trim($data, ']');
                }
            }
            $uri = implode('', $parsedUri);
        }

        if (count($params) > 0) {
            $uri = $uri . '?' . http_build_query($params);
        }

        if ($options['full'] || ($host && $host != $this->currentInfo['currentHost'])) {
            //
            $host = $host ?: $this->currentInfo['host'];
            $scheme = $scheme ?: $this->currentInfo['scheme'];
            return sprintf('%s://%s%s', $scheme, $host, $uri);
        } else {
            return $uri;
        }
    }

    public function cover($params = [], $route = null)
    {
        $params = array_merge($this->currentInfo['params'], $params);
        return $this->route($route ?: $this->currentRouteName, $params);
    }
}
