<?php

namespace Benji\Bundle\AkismetBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\Config\Definition\Processor;

class BenjiAkismetExtension extends Extension
{
    /**
     * Handles the benji_akismet configuration.
     *
     * @param array $configs The configurations being loaded
     * @param ContainerBuilder $container
     */
    public function load(array $configs , ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $c = new Configuration();

        $processor = new Processor();

        $config = $processor->process($c->getConfigTree(), $configs);

        $container->setParameter('akismet.blog',$config['blog']);
        $container->setParameter('akismet.key',$config['key']);

        $loader->load('config.xml');
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/benji-akismet';
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getAlias()
    {
        return 'benji_akismet';
    }
}
