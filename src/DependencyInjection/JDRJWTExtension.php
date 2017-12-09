<?php

namespace JDR\JWTBundle\DependencyInjection;

use function class_exists;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Lcobucci\JWT\Signer\Key;

/**
 * This is the class that loads and manages your bundle configuration.
 */
class JDRJWTExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!$container->hasParameter('kernel.real_root_dir')) {
            $container->setParameter('kernel.real_root_dir', realpath($container->getParameter('kernel.root_dir').'/..'));
        }

        $this->processSigners($container, $config['signers']);

        $default = $config['default'] ?? [];
        if (array_key_exists('keys', $config)) {
            $this->processKeys($container, $config['keys'], $default);
        }


        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'jdr_jwt';
    }

    private function processSigners(ContainerBuilder $container, $signers)
    {
        foreach ($signers as $algorithm => $class) {
            $signer = new Definition($class);

            $id = 'jdr.jwt.signer.'.strtolower($algorithm);
            $container->setDefinition($id, $signer);
        }
    }

    private function processKeys(ContainerBuilder $container, $keys, $default)
    {
        if (array_key_exists('private_key', $default) || array_key_exists('public_key', $default)) {
            // Default configuration present, use it to create the default builder and/or parser.
            $keys['default'] = $default;
        }

        foreach ($keys as $id => $config) {
            $mergedConfig = $this->mergeConfig($config, $default);
            $options = $mergedConfig['options'] ?? [];

            if (isset($mergedConfig['private_key'])) {
                $passphrase = $mergedConfig['passphrase'] ?? null;

                $this->registerBuilder($container, $id, $mergedConfig['algorithm'], $mergedConfig['private_key'], $passphrase, $options);
            }

            if (isset($mergedConfig['public_key'])) {
                $this->registerParser($container, $id, $mergedConfig['algorithm'], $mergedConfig['public_key'], $options);
            }
        }
    }

    private function mergeConfig(array $config = [], array $default = [])
    {
        $useDefaultKeys = !array_key_exists('private_key', $config) && !array_key_exists('public_key', $config);
        if ($useDefaultKeys) {
            // Check if either one of the default keys are set.
            if (!array_key_exists('private_key', $default) && !array_key_exists('public_key', $default)) {
                // No configuration present.
                return [];
            }

            // Default algorithm MUST be set.
            if (!isset($default['algorithm'])) {
                throw new \Exception('Algorithm MUST be set for key: "default".');
            }

            // Options can't implement a different algorithm.
            if (isset($config['algorithm']) && $config['algorithm'] != $default['algorithm']) {
                throw new \Exception('Algorithm MUST be the same.');
            }

            if ($config['options']) {
                $default['options'] = $config['options'] + $default['options'];
            }

            return $default;
        }

        // Algorithm MUST be set.
        if (!isset($config['algorithm'])) {
            throw new \Exception('Algorithm MUST be set.');
        }

        if ($default['options']) {
            $config['options'] = $config['options'] + $default['options'];
        }

        return $config;
    }

    private function registerBuilder(ContainerBuilder $container, string $id, string $algorithm, string $relativePath, string $passphrase = null, array $options = [])
    {
        $rootPath = $container->getParameter('kernel.real_root_dir');
        $path = $this->convertToAbsolutePath($relativePath, $rootPath);
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $definition = 'jdr.jwt.builder';
        if ($id !== 'default') {
            $definition = 'jdr.jwt.builder.'.$id;
        }

        $key = new Definition(Key::class, [
            'file://'.$path,
            $passphrase,
        ]);

        if (class_exists(ChildDefinition::class)) {
            $builder = new ChildDefinition('jdr.jwt.abstract_builder');
        } else {
            $builder = new DefinitionDecorator('jdr.jwt.abstract_builder');
        }

        $container
            ->setDefinition($definition, $builder)
            ->replaceArgument(0, $container->findDefinition('jdr.jwt.signer.'.strtolower($algorithm)))
            ->replaceArgument(1, $key)
            ->replaceArgument(2, $options)
        ;
    }

    private function registerParser(ContainerBuilder $container, string $id, string $algorithm, string $relativePath, array $options = [])
    {
        $rootPath = $container->getParameter('kernel.real_root_dir');
        $path = $this->convertToAbsolutePath($relativePath, $rootPath);
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $definition = 'jdr.jwt.parser';
        if ($id !== 'default') {
            $definition = 'jdr.jwt.parser.'.$id;
        }

        $key = new Definition(Key::class, [
            'file://'.$path,
        ]);

        if (class_exists(ChildDefinition::class)) {
            $parser = new ChildDefinition('jdr.jwt.abstract_parser');
        } else {
            $parser = new DefinitionDecorator('jdr.jwt.abstract_parser');
        }

        $container
            ->setDefinition($definition, $parser)
            ->replaceArgument(0, $container->findDefinition('jdr.jwt.signer.'.strtolower($algorithm)))
            ->replaceArgument(1, $key)
            ->replaceArgument(2, $options)
        ;
    }

    private function convertToAbsolutePath($path, $rootPath)
    {
        if (preg_match('{^/}', $path)) {
            return $path;
        }

        return $rootPath.'/'.$path;
    }
}
