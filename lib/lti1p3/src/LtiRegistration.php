<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiRegistration;

class LtiRegistration implements ILtiRegistration
{
    private $issuer;
    private $clientId;
    private $keySetUrl;
    private $authTokenUrl;
    private $authLoginUrl;
    private $authServer;
    private $toolPrivateKey;
    private $kid;

    public function __construct(array $registration = [])
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

    public static function new(array $registration = [])
    {
        return new LtiRegistration($registration);
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $this;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getKeySetUrl()
    {
        return $this->keySetUrl;
    }

    public function setKeySetUrl($keySetUrl)
    {
        $this->keySetUrl = $keySetUrl;

        return $this;
    }

    public function getAuthTokenUrl()
    {
        return $this->authTokenUrl;
    }

    public function setAuthTokenUrl($authTokenUrl)
    {
        $this->authTokenUrl = $authTokenUrl;

        return $this;
    }

    public function getAuthLoginUrl()
    {
        return $this->authLoginUrl;
    }

    public function setAuthLoginUrl($authLoginUrl)
    {
        $this->authLoginUrl = $authLoginUrl;

        return $this;
    }

    public function getAuthServer()
    {
        return empty($this->authServer) ? $this->authTokenUrl : $this->authServer;
    }

    public function setAuthServer($authServer)
    {
        $this->authServer = $authServer;

        return $this;
    }

    public function getToolPrivateKey()
    {
        return $this->toolPrivateKey;
    }

    public function setToolPrivateKey($toolPrivateKey)
    {
        $this->toolPrivateKey = $toolPrivateKey;

        return $this;
    }

    public function getKid()
    {
        return $this->kid ?? hash('sha256', trim($this->issuer.$this->clientId));
    }

    public function setKid($kid)
    {
        $this->kid = $kid;

        return $this;
    }
}
