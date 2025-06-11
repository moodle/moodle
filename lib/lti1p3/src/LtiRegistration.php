<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiRegistration;

class LtiRegistration implements ILtiRegistration
{
    private ?string $issuer;
    private ?string $clientId;
    private ?string $keySetUrl;
    private ?string $authTokenUrl;
    private ?string $authLoginUrl;
    private ?string $authServer;
    private ?string $toolPrivateKey;
    private ?string $kid;

    public function __construct(?array $registration = null)
    {
        $this->issuer = $registration['issuer'] ?? null;
        $this->clientId = $registration['clientId'] ?? null;
        $this->keySetUrl = $registration['keySetUrl'] ?? null;
        $this->authTokenUrl = $registration['authTokenUrl'] ?? null;
        $this->authLoginUrl = $registration['authLoginUrl'] ?? null;
        $this->authServer = $registration['authServer'] ?? null;
        $this->toolPrivateKey = $registration['toolPrivateKey'] ?? null;
        $this->kid = $registration['kid'] ?? null;
    }

    public static function new(?array $registration = null): self
    {
        return new LtiRegistration($registration);
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function setIssuer(string $issuer): self
    {
        $this->issuer = $issuer;

        return $this;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getKeySetUrl(): ?string
    {
        return $this->keySetUrl;
    }

    public function setKeySetUrl(?string $keySetUrl): self
    {
        $this->keySetUrl = $keySetUrl;

        return $this;
    }

    public function getAuthTokenUrl(): ?string
    {
        return $this->authTokenUrl;
    }

    public function setAuthTokenUrl(?string $authTokenUrl): self
    {
        $this->authTokenUrl = $authTokenUrl;

        return $this;
    }

    public function getAuthLoginUrl(): ?string
    {
        return $this->authLoginUrl;
    }

    public function setAuthLoginUrl(?string $authLoginUrl): self
    {
        $this->authLoginUrl = $authLoginUrl;

        return $this;
    }

    public function getAuthServer(): ?string
    {
        return $this->authServer ?? $this->authTokenUrl;
    }

    public function setAuthServer(?string $authServer): self
    {
        $this->authServer = $authServer;

        return $this;
    }

    public function getToolPrivateKey()
    {
        return $this->toolPrivateKey;
    }

    public function setToolPrivateKey(string $toolPrivateKey): self
    {
        $this->toolPrivateKey = $toolPrivateKey;

        return $this;
    }

    public function getKid()
    {
        return $this->kid ?? hash('sha256', trim($this->issuer.$this->clientId));
    }

    public function setKid(string $kid): self
    {
        $this->kid = $kid;

        return $this;
    }
}
