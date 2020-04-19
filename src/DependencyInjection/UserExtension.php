<?php
namespace Enigma972\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class UserExtension extends Extension
{
    /**
     * Loads a specific configuration
     * 
     * 
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, 
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('user.check_mail', $config['check_mail']);
        $container->setParameter('user.reset_password_code_validity', $config['reset_password_code_validity']);
        $container->setParameter('user.target', $config['target']);
        $container->setParameter('user.no_reply_mail', $config['no_reply_mail']);
        $container->setParameter('user.not_send_welcome_mail', $config['not_send_welcome_mail']);
    }
}
