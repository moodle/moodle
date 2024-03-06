<?php

namespace Packback\Lti1p3\Interfaces;

/** @internal */
interface ILtiRegistration
{
    public function getIssuer();

    public function setIssuer(string $issuer): ILtiRegistration;

    public function getClientId();

    public function setClientId(string $clientId): ILtiRegistration;

    public function getKeySetUrl(): ?string;

    public function setKeySetUrl(string $keySetUrl): ILtiRegistration;

    public function getAuthTokenUrl(): ?string;

    public function setAuthTokenUrl(?string $authTokenUrl): ILtiRegistration;

    public function getAuthLoginUrl(): ?string;

    public function setAuthLoginUrl(string $authLoginUrl): ILtiRegistration;

    public function getAuthServer(): ?string;

    public function setAuthServer(string $authServer): ILtiRegistration;

    public function getToolPrivateKey();

    public function setToolPrivateKey(string $toolPrivateKey): ILtiRegistration;

    public function getKid();

    public function setKid(string $kid): ILtiRegistration;
}
