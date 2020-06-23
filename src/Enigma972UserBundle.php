<?php
namespace Enigma972\UserBundle;

use Enigma972\UserBundle\DependencyInjection\Compiler\Enigma972UserBundleCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class Enigma972UserBundle extends Bundle
{
    public const VERSION = "v0.3.4";

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new Enigma972UserBundleCompilerPass);
    }
}
