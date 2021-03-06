<?php
namespace BootTests\Test;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use BootTests\Test\AbstractBootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;

/**
 * Class ControllerTestCase
 * @package BootTests\Test
 */
abstract class ControllerTestCase extends TestCase
{
    /**
     * The ActionController we are testing
     *
     * @var Zend\Mvc\Controller\AbstractActionController
     */
    protected $controller;

    /**
     * A request object
     *
     * @var Zend\Http\Request
     */
    protected $request;

    /**
     * A response object
     *
     * @var Zend\Http\Response
     */
    protected $response;

    /**
     * The matched route for the controller
     *
     * @var Zend\Mvc\Router\RouteMatch
     */
    protected $routeMatch;

    /**
     * An MVC event to be assigned to the controller
     *
     * @var Zend\Mvc\MvcEvent
     */
    protected $event;

    /**
     * The Controller fully qualified domain name, so each ControllerTestCase can create an instance
     * of the tested controller
     *
     * @var string
     */
    protected $controllerFQDN;

    /**
     * The route to the controller, as defined in the configuration files
     *
     * @var string
     */
    protected $controllerRoute;

    public function setup()
    {
        parent::setup();
        $this->controller = new $this->controllerFQDN;
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(['controller' => $this->controllerRoute]);
        $this->event      = new MvcEvent();
        $config = $this->serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : [];
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($this->serviceManager);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->controller);
        unset($this->request);
        unset($this->routeMatch);
        unset($this->event);
    }
}
