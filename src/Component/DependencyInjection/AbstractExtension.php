<?php

declare(strict_types=1);

namespace Kaby\Component\DependencyInjection;

use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractExtension extends Extension
{
    /**
     * @var array
     */
    protected static $xmlConfigs = array(
        'services',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $reflection = new ReflectionClass(get_called_class());
        $path = dirname($reflection->getFileName()) . '/../Resources/config';
        if (is_dir($path)) {
            $loader = new XmlFileLoader($container, new FileLocator($path));
            foreach (static::$xmlConfigs as $name) {
                if (file_exists(sprintf('%s/%s.xml', $path, $name))) {
                    $loader->load(sprintf('%s.xml', $name));
                }
            }
        }
    }
}