<?php

namespace Genj\FrontendUrlBundle\Routing\Frontend;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection as BaseRouteCollection;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Class RouteCollection
 *
 * @package Genj\FrontendUrlBundle\Routing\Frontend
 */
class RouteCollection extends BaseRouteCollection
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * Load routes of frontend application
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $frontendEnvironment = $this->container->getParameter('genj_frontend_url.frontend_environment');
        $configDir           = $this->container->get('kernel')->getRootDir() . '/config/';
        $routingFile         = $configDir . 'routing_' . $frontendEnvironment . '.yml';

        $loader         = $this->container->get('routing.loader');
        $importedRoutes = $loader->import($routingFile, 'yaml');

        $this->resolveParameters($importedRoutes);
        $this->addCollection($importedRoutes);
    }

    /**
     * Replaces placeholders with service container parameter values in:
     * - the route defaults,
     * - the route requirements,
     * - the route pattern.
     * - the route host.
     *
     * @param BaseRouteCollection $collection
     */
    protected function resolveParameters(BaseRouteCollection $collection)
    {
        foreach ($collection as $route) {
            foreach ($route->getDefaults() as $name => $value) {
                $route->setDefault($name, $this->resolve($value));
            }

            foreach ($route->getRequirements() as $name => $value) {
                $route->setRequirement($name, $this->resolve($value));
            }

            $route->setPath($this->resolve($route->getPath()));
            $route->setHost($this->resolve($route->getHost()));
        }
    }

    /**
     * Recursively replaces placeholders with the service container parameters.
     *
     * @param mixed $value The source which might contain "%placeholders%"
     *
     * @return mixed The source with the placeholders replaced by the container
     *               parameters. Arrays are resolved recursively.
     *
     * @throws ParameterNotFoundException When a placeholder does not exist as a container parameter
     * @throws RuntimeException           When a container value is not a string or a numeric value
     */
    private function resolve($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->resolve($val);
            }

            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        $container = $this->container;

        $escapedValue = preg_replace_callback('/%%|%([^%\s]++)%/', function ($match) use ($container, $value) {
            // skip %%
            if (!isset($match[1])) {
                return '%%';
            }

            $resolved = $container->getParameter($match[1]);

            if (is_string($resolved) || is_numeric($resolved)) {
                return (string) $resolved;
            }

            throw new RuntimeException(sprintf(
                'The container parameter "%s", used in the route configuration value "%s", ' .
                'must be a string or numeric, but it is of type %s.',
                $match[1],
                $value,
                gettype($resolved)
            ));
        }, $value);

        return str_replace('%%', '%', $escapedValue);
    }
}
