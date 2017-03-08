<?php

namespace JDR\JWTBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jdr_jwt');

        $rootNode
            ->children()
                ->arrayNode('signers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('ES256')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('JDR\JWS\ECDSA\ES256')
                        ->end()
                        ->scalarNode('ES384')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('JDR\JWS\ECDSA\ES384')
                        ->end()
                        ->scalarNode('ES512')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('JDR\JWS\ECDSA\ES512')
                        ->end()
                        ->scalarNode('RS256')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('Lcobucci\JWT\Signer\Rsa\Sha256')
                        ->end()
                        ->scalarNode('RS384')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('Lcobucci\JWT\Signer\Rsa\Sha384')
                        ->end()
                        ->scalarNode('RS512')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('Lcobucci\JWT\Signer\Rsa\Sha512')
                        ->end()
                        ->scalarNode('HS256')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('Lcobucci\JWT\Signer\Hmac\Sha256')
                        ->end()
                        ->scalarNode('HS384')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('Lcobucci\JWT\Signer\Hmac\Sha384')
                        ->end()
                        ->scalarNode('HS512')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('Lcobucci\JWT\Signer\Hmac\Sha512')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('default')
                    ->children()
                        ->scalarNode('algorithm')->end()
                        ->scalarNode('private_key')->end()
                        ->scalarNode('passphrase')->end()
                        ->scalarNode('public_key')->end()
                        ->arrayNode('options')
                            ->children()
                                ->scalarNode('issuer')->end()
                                ->scalarNode('lifetime')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('keys')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('algorithm')->end()
                            ->scalarNode('private_key')->end()
                            ->scalarNode('passphrase')->end()
                            ->scalarNode('public_key')->end()
                            ->arrayNode('options')
                                ->children()
                                    ->scalarNode('issuer')->end()
                                    ->scalarNode('lifetime')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
