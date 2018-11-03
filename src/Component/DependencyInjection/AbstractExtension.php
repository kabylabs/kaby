<?php

declare(strict_types=1);

namespace Kaby\Component\DependencyInjection;

use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractExtension extends Extension
{
    /**
     * @var array
     */
    protected static $yamlConfigs = array(
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
            $loader = new YamlFileLoader($container, new FileLocator($path));
            foreach (static::$yamlConfigs as $name) {
                if (file_exists(sprintf('%s/%s.yaml', $path, $name))) {
                    $loader->load(sprintf('%s.yaml', $name));
                }
            }
        }
    }
}