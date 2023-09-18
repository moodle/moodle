<?php

namespace Packback\Lti1p3\Interfaces;

/** @internal */
interface ILtiRegistration
{
    public function getIssuer();

    public function setIssuer($issuer);

    public function getClientId();

    public function setClientId($clientId);

    public function getKeySetUrl();

    public function setKeySetUrl($keySetUrl);

    public function getAuthTokenUrl();

    public function setAuthTokenUrl($authTokenUrl);

    public function getAuthLoginUrl();

    public function setAuthLoginUrl($authLoginUrl);

    public function getAuthServer();

    public function setAuthServer($authServer);

    public function getToolPrivateKey();

    public function setToolPrivateKey($toolPrivateKey);

    public function getKid();

    public function setKid($kid);
}
