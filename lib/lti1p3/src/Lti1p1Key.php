<?php

namespace Packback\Lti1p3;

/**
 * Used for migrations from LTI 1.1 to LTI 1.3
 *
 * @see IMigrationDatabase
 */
class Lti1p1Key
{
    private ?string $key;
    private ?string $secret;

    public function __construct(?array $key = null)
    {
        $this->key = $key['key'] ?? null;
        $this->secret = $key['secret'] ?? null;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Create a signature using the key and secret
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3/migr#oauth_consumer_key_sign
     */
    public function sign(string $deploymentId, string $iss, string $clientId, string $exp, string $nonce): string
    {
        $signatureComponents = [
            $this->getKey(),
            $deploymentId,
            $iss,
            $clientId,
            $exp,
            $nonce,
        ];

        $baseString = implode('&', $signatureComponents);
        $utf8String = mb_convert_encoding($baseString, 'utf8', mb_detect_encoding($baseString));
        $hash = hash_hmac('sha256', $utf8String, $this->getSecret(), true);

        return base64_encode($hash);
    }

}
