<?php

namespace SchulIT\IdpExchangeBundle\DependencyInjection;

use SchulIT\IdpExchange\Client;
use SchulIT\IdpExchangeBundle\Service\SynchronizationManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IdpExchangeExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container) {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('idp_exchange.user_limit', $config['user_limit']);
        $container->setParameter('idp_exchange.templates.overview', $config['templates']['overview']);
        $container->setParameter('idp_exchange.templates.clear', $config['templates']['clear']);

        $definition = $container->getDefinition(Client::class);
        $definition->replaceArgument(0, $config['endpoint']);
        $definition->replaceArgument(1, $config['token']);
        $definition->replaceArgument(2, new Reference($config['guzzle']));

        if(isset($config['serializer'])) {
            $definition->replaceArgument(3, new Reference($config['serializer']));
        }

        if(isset($config['logger'])) {
            $definition->replaceArgument(4, new Reference($config['logger']));
        }

        $definition = $container->getDefinition(SynchronizationManager::class);
        $definition->replaceArgument(4, new Reference($config['user_loader']));
        $definition->replaceArgument(5, new Reference($config['user_updater']));

        if(isset($config['logger'])) {
            $definition->replaceArgument(6, new Reference($config['logger']));
        }

        $loader->load('controller.yaml');
        $loader->load('commands.yaml');
    }

    public function getAlias() {
        return 'idp_exchange';
    }
}