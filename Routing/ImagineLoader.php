<?php

namespace Avalanche\Bundle\ImagineBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ImagineLoader extends Loader
{
    private $cachePrefix;
    private $filters;

    public function __construct($cachePrefix, array $filters = array())
    {
        $this->cachePrefix = $cachePrefix;
        $this->filters     = $filters;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'imagine';
    }

    public function load($resource, $type = null)
    {
        $requirements = array('_method' => 'GET', 'filter' => '[A-z0-9_\-]*', 'path' => '.+');
        $defaults     = array('_controller' => 'imagine.controller:filterAction');
        $routes       = new RouteCollection();

        foreach ($this->filters as $filter => $options) {
            if (isset($options['path'])) {
                $pattern = '/' . trim($options['path'], '/') . '/{path}';
            } else {
                $pattern = '/' . trim($this->cachePrefix, '/') . '/{filter}/{path}';
            }

            $routes->add(
                '_imagine_' . $filter,
                new Route($pattern, array_merge($defaults, ['filter' => $filter]), $requirements)
            );
        }

        return $routes;
    }
}
