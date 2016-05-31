<?php

namespace Genj\FrontendUrlBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GenjFrontendUrlExtension
 *
 * @package Genj\FrontendUrlBundle\Twig
 */
class GenjFrontendUrlExtension extends \Twig_Extension
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('genj_url_for_frontend', array($this, 'generateFrontendUrlForObject'))
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('genj_frontend_url', array($this, 'generateFrontendUrl'))
        );
    }

    /**
     * Generates URL to frontend.
     *
     * @param \stdClass $object
     * @param bool      $preview
     * @param bool      $absolute Whether or not to create an absolute url
     *
     * @return null|string
     */
    public function generateFrontendUrlForObject($object, $preview = true, $absolute = true)
    {
        $urlGenerator = $this->container->get('genj_frontend_url.routing.frontend.generator.url_generator');
        $frontendUrl  = $urlGenerator->generateFrontendUrlForObject($object, $preview, $absolute);

        return $frontendUrl;
    }

    /**
     * Generates URL to frontend.
     *
     * @param \stdClass $object
     * @param bool      $preview
     * @param bool      $absolute Whether or not to create an absolute url
     *
     * @return null|string
     */
    public function generateFrontendUrl($name, $parameters = array(), $preview = false, $absolute = true)
    {
        $urlGenerator = $this->container->get('genj_frontend_url.routing.frontend.generator.url_generator');
        $frontendUrl  = $urlGenerator->generateFrontendUrl($name, $parameters, $preview, $absolute);

        return $frontendUrl;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'genj_frontend_url_extension';
    }
}
