<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiDeployment;

class LtiDeployment implements ILtiDeployment
{
    public function __construct(
        private $deployment_id
    ) {
    }

    public static function new($deployment_id): self
    {
        return new LtiDeployment($deployment_id);
    }

    public function getDeploymentId()
    {
        return $this->deployment_id;
    }

    public function setDeploymentId($deployment_id): self
    {
        $this->deployment_id = $deployment_id;

        return $this;
    }
}
