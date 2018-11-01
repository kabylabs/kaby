<?php

declare(strict_types=1);

namespace Kaby\Component\Bundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass($this->buildMappingCompilerPass());
    }

    /**
     * @return DoctrineOrmMappingsPass
     */
    public function buildMappingCompilerPass()
    {
        return DoctrineOrmMappingsPass::createXmlMappingDriver($this->getMappingDriver());
    }

    /**
     * @return array
     */
    protected function getMappingDriver(): array
    {
        return [sprintf('%s/Resources/config/doctrine', $this->getBundleName()) => $this->getModelNamespace()];
    }

    /**
     * @return string
     */
    abstract protected function getBundleName(): string;

    /**
     * @return string
     */
    abstract protected function getModelNamespace(): string;
}