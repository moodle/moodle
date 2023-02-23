<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;

class JwksEndpoint
{
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public static function new(array $keys)
    {
        return new JwksEndpoint($keys);
    }

    public static function fromIssuer(IDatabase $database, $issuer)
    {
        $registration = $database->findRegistrationByIssuer($issuer);

        return new JwksEndpoint([$registration->getKid() => $registration->getToolPrivateKey()]);
    }

    public static function fromRegistration(ILtiRegistration $registration)
    {
        return new JwksEndpoint([$registration->getKid() => $registration->getToolPrivateKey()]);
    }

    public function getPublicJwks()
    {
        $jwks = [];
        foreach ($this->keys as $kid => $private_key) {
            $key_res = openssl_pkey_get_private($private_key);
            $key_details = openssl_pkey_get_details($key_res);
            $components = [
                'kty' => 'RSA',
                'alg' => 'RS256',
                'use' => 'sig',
                'e' => JWT::urlsafeB64Encode($key_details['rsa']['e']),
                'n' => JWT::urlsafeB64Encode($key_details['rsa']['n']),
                'kid' => $kid,
            ];
            $jwks[] = $components;
        }

        return ['keys' => $jwks];
    }

    public function outputJwks()
    {
        echo json_encode($this->getPublicJwks());
    }
}
