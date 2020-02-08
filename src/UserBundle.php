<?php
namespace Enigma972\UserBundle;

use Enigma972\UserBundle\DependencyInjection\Compiler\UserBundleCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class UserBundle extends Bundle
{
    public const VERSION = "0.0.1";

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new UserBundleCompilerPass);
    }
}
