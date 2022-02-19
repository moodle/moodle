<?php

namespace Packback\Lti1p3;

class LtiDeployment
{
    private $deployment_id;

    public static function new()
    {
        return new LtiDeployment();
    }

    public function getDeploymentId()
    {
        return $this->deployment_id;
    }

    public function setDeploymentId($deployment_id)
    {
        $this->deployment_id = $deployment_id;

        return $this;
    }
}
