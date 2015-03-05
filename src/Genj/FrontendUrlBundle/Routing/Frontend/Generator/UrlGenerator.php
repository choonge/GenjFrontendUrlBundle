<?php

namespace Genj\FrontendUrlBundle\Routing\Frontend\Generator;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

/**
 * Class UrlGenerator
 *
 * @package Genj\FrontendUrlBundle\Routing\Frontend\Generator
 */
class UrlGenerator extends BaseUrlGenerator
{
    /**
     * @param \stdClass $object
     * @param bool      $preview
     * @param bool   $absolute   Whether or not to create an absolute url
     *
     * @return null|string
     */
    public function generateFrontendUrlForObject($object, $preview = false, $absolute = true)
    {
        $frontendUrl     = null;
        $routeName       = null;
        $routeParameters = null;

        if (is_callable(array($object, 'getRouteName'))) {
            $routeName = $object->getRouteName();
        }

        if (is_callable(array($object, 'getRouteParameters'))) {
            $routeParameters = $object->getRouteParameters();
        }

        if ($routeName && $routeParameters) {
            $frontendUrl = $this->generateFrontendUrl($routeName, $routeParameters, $preview, $absolute);
        }

        return $frontendUrl;
    }

    /**
     * @param string $name       The name of the route
     * @param mixed  $parameters An array of parameters
     * @param bool   $preview    Whether or not to link to the preview environment. True by default.
     * @param bool   $absolute   Whether or not to create an absolute url
     *
     * @return null|string
     */
    public function generateFrontendUrl($name, $parameters = array(), $preview = false, $absolute = true)
    {
        $referenceType = self::ABSOLUTE_URL;

        if (!$absolute) {
            $referenceType = self::ABSOLUTE_PATH;
        }

        $url = parent::generate($name, $parameters, $referenceType);
        $url = $this->modifyPath($url, $preview);

        // If no host was explicitly specified for the route, we determine it ourselves
        if (!$this->routes->get($name)->getHost() && $absolute) {
            $url = $this->modifyHost($url, $preview);
        }

        return $url;
    }

    /**
     * Prepends 'www.' to the base domain name. If it's a preview url, 'upload.' is prepended instead, since we want
     * the preview to always show on the first server.
     *
     * @param string $url
     * @param bool   $preview
     *
     * @return mixed
     */
    protected function modifyHost($url, $preview = true)
    {
        // Get the host without any subdomains
        $originalHost = parse_url($url, PHP_URL_HOST);
        $modifiedHost = preg_replace('/^(?:admin|www|upload|static|secure)\./', '', $originalHost, 1);

        // Prepend www or static
        if ($preview === true) {
            $modifiedHost = 'upload.' . $modifiedHost;
        } else {
            $modifiedHost = 'www.' . $modifiedHost;
        }

        // Replace it
        $originalHost = preg_quote($originalHost, '/');
        $url          = preg_replace("/$originalHost/", $modifiedHost, $url, 1);

        return $url;
    }

    /**
     * Strips out any existing admin/ or admin/dev.php from the url path, and adds preview.php if needed
     *
     * @param string $url
     * @param bool   $preview
     *
     * @return string
     */
    protected function modifyPath($url, $preview = false)
    {
        $originalPath = parse_url($url, PHP_URL_PATH);
        $modifiedPath = preg_replace('/^(?:\/admin)/', '', $originalPath, 1);

        if ($preview === true) {
            $modifiedPath = preg_replace('/^(?:\/dev.php)/', '', $modifiedPath, 1);
            $modifiedPath = '/preview.php' . $modifiedPath;
        }

        $originalPath = preg_quote($originalPath, '/');
        $url          = preg_replace("/$originalPath/", $modifiedPath, $url, 1);

        return $url;
    }
}
