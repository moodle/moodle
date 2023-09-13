<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IServiceRequest;

abstract class LtiAbstractService
{
    private $serviceConnector;
    private $registration;
    private $serviceData;

    public function __construct(
        ILtiServiceConnector $serviceConnector,
        ILtiRegistration $registration,
        array $serviceData
    ) {
        $this->serviceConnector = $serviceConnector;
        $this->registration = $registration;
        $this->serviceData = $serviceData;
    }

    public function getServiceData(): array
    {
        return $this->serviceData;
    }

    public function setServiceData(array $serviceData): self
    {
        $this->serviceData = $serviceData;

        return $this;
    }

    abstract public function getScope(): array;

    protected function validateScopes(array $scopes): void
    {
        if (empty(array_intersect($scopes, $this->getScope()))) {
            throw new LtiException('Missing required scope', 1);
        }
    }

    protected function makeServiceRequest(IServiceRequest $request): array
    {
        return $this->serviceConnector->makeServiceRequest(
            $this->registration,
            $this->getScope(),
            $request,
        );
    }

    protected function getAll(IServiceRequest $request, string $key = null): array
    {
        return $this->serviceConnector->getAll(
            $this->registration,
            $this->getScope(),
            $request,
            $key
        );
    }
}
