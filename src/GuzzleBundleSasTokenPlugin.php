<?php

namespace Moux2003\GuzzleBundleSasTokenPlugin;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Moux2003\GuzzleBundleSasTokenPlugin\DependencyInjection\GuzzleSasTokenExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;

class GuzzleBundleWssePlugin extends Bundle implements EightPointsGuzzleBundlePlugin
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $extension = new GuzzleSasTokenExtension();
        $extension->load($configs, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param string           $clientName
     * @param Definition       $handler
     */
    public function loadForClient(array $config, ContainerBuilder $container, string $clientName, Definition $handler)
    {
        if ($config['sasKey'] && $config['sasKeyName'] && $config['uri']) {
            $sasToken = new Definition('%guzzle_bundle_sastoken_plugin.middleware.sastoken.class%');
            $sasToken->setArguments([$config['sasKey'], $config['sasKeyName'], $config['uri']]);
            $sasToken->setPublic(true);

            $sasTokenServiceName = sprintf('guzzle_bundle_sastoken_plugin.middleware.sasToken.%s', $clientName);

            $container->setDefinition($sasTokenServiceName, $sasToken);

            $sasTokenExpression = new Expression(sprintf('service("%s").attach()', $sasTokenServiceName));

            $handler->addMethodCall('push', [$sasTokenExpression, 'sastoken']);
        }
    }

    /**
     * @param ArrayNodeDefinition $pluginNode
     */
    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        $pluginNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('sasKey')->defaultNull()->end()
                ->scalarNode('sasKeyName')->defaultNull()->end()
                ->scalarNode('uri')->defaultNull()->end()
                ->integerNode('expires')->defaultValue(60)->end()
            ->end();
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return 'sastoken';
    }
}
