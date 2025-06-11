<?php

namespace Packback\Lti1p3\Interfaces;

/** @internal */
interface ILtiDeployment
{
    public function getDeploymentId();

    public function setDeploymentId($deployment_id): ILtiDeployment;
}
