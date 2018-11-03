<?php

declare(strict_types=1);

namespace Kaby\Component\Bundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \ReflectionException
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass($this->buildMappingCompilerPass());
    }

    /**
     * @return DoctrineOrmMappingsPass
     * @throws \ReflectionException
     */
    public function buildMappingCompilerPass()
    {
        return DoctrineOrmMappingsPass::createXmlMappingDriver($this->getMappingDriver());
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    private function getMappingDriver(): array
    {
        $reflection = new ReflectionClass(get_called_class());
        $path = dirname($reflection->getFileName()) . '/Resources/config/doctrine';

        return [$path => $this->getModelNamespace()];
    }

    /**
     * @return string
     */
    abstract protected function getModelNamespace(): string;
}