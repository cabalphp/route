<?php

use PHPUnit\Framework\TestCase;
use Cabal\Route\RouteCollection;
use Zend\Diactoros\ServerRequest;

require_once __DIR__ . '/../vendor/autoload.php';

class RouteTest extends TestCase
{
    /**
     * @var \Cabal\Route\RouteCollection
     */
    static $route;

    public static function setUpBeforeClass()
    {
    }

    public function testRequestDispatch()
    {
        $route = new RouteCollection();
        $route->map('GET', '/', 'DefaultController@getIndex');
        $route->get('/get', 'DefaultController@get');
        $route->post('/post', 'DefaultController@post');
        $route->get('/var/{id}', 'DefaultController@var');

        $route->group([
            'host' => '',
            'method' => 'GET',
            'scheme' => 'https',
            'basePath' => '/https',
            'namespace' => 'App\\Controller',
            'middleware' => ['httpsMiddleware'],
        ], function ($route) {
            $route->map([], '/index', 'HttpsController@getIndex');
            $route->map([], '/index2', 'HttpsController@getIndex2');

            $route->group([
                'basePath' => '/group',
            ], function ($route) {
                $route->map([], '/deep2', 'HttpsController@deep2');
            });
        });
        foreach ([
            ['http', 'www.cabalphp.com', '/', 'GET', '\DefaultController@getIndex', [], []],
            ['http', 'www.cabalphp.com', '/get', 'GET', '\DefaultController@get', [], []],
            ['http', 'www.cabalphp.com', '/get', 'GET', '\DefaultController@get', [], []],
            ['http', 'www.cabalphp.com', '/var/1', 'GET', '\DefaultController@var', [], ['id' => '1']],
            ['https', 'www.cabalphp.com', '/https/index', 'GET', '\App\Controller\HttpsController@getIndex', ['httpsMiddleware'], []],
            ['https', 'www.cabalphp.com', '/https/group/deep2', 'GET', '\App\Controller\HttpsController@deep2', ['httpsMiddleware'], []],
            ['https', 'www.cabalphp.com', '/https/2group/deep2', 'GET', null, [], []],
            ['http', 'www.cabalphp.com', '/https/index', 'GET', null, [], []],
            ['https', 'www.cabalphp.com', '/https/index', 'POST', null, [], []],
        ] as $request) {
            list($scheme, $host, $path, $method, $rightHandler, $rightMiddleware, $rightVars) = $request;
            $request = $this->newRequest($scheme, $host, $path, $method);
            list($code, $handler, $vars) = $route->dispatch($request);
            $vars = $vars ? : [];

            $this->assertEquals(isset($handler['handler']) ? $handler['handler'] : null, $rightHandler);
            $this->assertEquals(isset($handler['middleware']) ? json_encode($handler['middleware']) : '[]', json_encode($rightMiddleware));
            $this->assertEquals(json_encode($vars), json_encode($rightVars));
        }

    }

    public function testSimpleRequestDispatch()
    {
        $route = new RouteCollection();
        $route->map('GET', '/', 'DefaultController@getIndex');
        $route->get('/get', 'DefaultController@get');
        $route->post('/post', 'DefaultController@post');
        $route->get('/var/{id}', 'DefaultController@var');

        $route->group([
            'host' => '',
            'method' => 'GET',
            'scheme' => 'https',
            'basePath' => '/https',
            'namespace' => 'App\\Controller',
            'middleware' => ['httpsMiddleware'],
        ], function ($route) {
            $route->map([], '/index', 'HttpsController@getIndex');
            $route->map([], '/index2', 'HttpsController@getIndex2');
        });
        foreach ([
            ['/', 'GET', '\DefaultController@getIndex', [], []],
            ['/get', 'GET', '\DefaultController@get', [], []],
            ['/get', 'GET', '\DefaultController@get', [], []],
            ['/var/1', 'GET', '\DefaultController@var', [], ['id' => '1']],
            ['/https/index', 'GET', '\App\Controller\HttpsController@getIndex', ['httpsMiddleware'], []],
            ['/https/index', 'GET', '\App\Controller\HttpsController@getIndex', ['httpsMiddleware'], []],
            ['/https/index', 'POST', null, [], []],
        ] as $request) {
            list($path, $method, $rightHandler, $rightMiddleware, $rightVars) = $request;
            list($code, $handler, $vars) = $route->simpleDispatch($method, $path);
            $vars = $vars ? : [];

            $this->assertEquals(isset($handler['handler']) ? $handler['handler'] : null, $rightHandler);
            $this->assertEquals(isset($handler['middleware']) ? json_encode($handler['middleware']) : '[]', json_encode($rightMiddleware));
            $this->assertEquals(json_encode($vars), json_encode($rightVars));
        }

    }

    protected function newRequest($scheme, $host, $path, $method = 'GET')
    {
        $url = "{$scheme}://{$host}{$path}";
        return new ServerRequest(
            [],
            [],
            $url,
            $method,
            fopen('/dev/null', 'r'),
            [],
            [],
            [],
            [],
            '1.0'
        );
    }
}