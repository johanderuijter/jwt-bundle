# jwt-bundle


Helper for [`lcobucci/jwt`][link-lcobucci-jwt].

## Instalation

Require the library with composer:
```
composer require jdr/jwt-bundle
```

## Configuration

Below you can find an example configuration. The default configuration is optional, provided more specific ones are provided.
When both the default and specific keys are set, the default keys are discarded for that option.

```yaml
jdr_jwt:
    signers:
        ES256: JDR\JWS\ECDSA\ES256 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
        ES384: JDR\JWS\ECDSA\ES384 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
        ES512: JDR\JWS\ECDSA\ES512 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
        RS256: Lcobucci\JWT\Signer\Rsa\Sha256 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
        RS384: Lcobucci\JWT\Signer\Rsa\Sha384 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
        RS512: Lcobucci\JWT\Signer\Rsa\Sha512 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
        HS256: Lcobucci\JWT\Signer\Hmac\Sha256 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
        HS384: Lcobucci\JWT\Signer\Hmac\Sha384 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
        HS512: Lcobucci\JWT\Signer\Hmac\Sha512 # [Default] Can be omitted. Provided as an option only to allow signers to be overridden.
    default:
        algorithm: ~ # Algorithm to use with default key.
        private_key: ~ # Path to the default private key.
        passphrase: ~ # Passphrase for the default private key.
        public_key: ~ # Path to the default public key.
        options:
            issuer: ~ # [Optional] Default issuer. Will be used to set the 'iss' claim.
            lifetime: ~ # [Optional] Default lifetime in seconds. Will be used to set the 'exp' claim.
    keys:
        some_purpose: # This key will be used to identify the created token builders and parsers.
            algorithm: ~ # Algorithm to use with specific key.
            private_key: ~ # Path to the specific private key.
            passphrase: ~ # Passphrase for the specific private key.
            public_key: ~ # Path to the specific public key.
            options:
                issuer: ~ # [Optional] Default issuer. Will be used to set the 'iss' claim.
                lifetime: ~ # [Optional] Default lifetime in seconds. Will be used to set the 'exp' claim.
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[link-lcobucci-jwt]: https://github.com/lcobucci/jwt
