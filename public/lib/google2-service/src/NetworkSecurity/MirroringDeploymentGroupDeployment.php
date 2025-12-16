<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\NetworkSecurity;

class MirroringDeploymentGroupDeployment extends \Google\Model
{
  /**
   * State not set (this is not a valid state).
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The deployment is ready and in sync with the parent group.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The deployment is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The deployment is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The deployment is out of sync with the parent group. In most cases, this is
   * a result of a transient issue within the system (e.g. a delayed data-path
   * config) and the system is expected to recover automatically. See the parent
   * deployment group's state for more details.
   */
  public const STATE_OUT_OF_SYNC = 'OUT_OF_SYNC';
  /**
   * An attempt to delete the deployment has failed. This is a terminal state
   * and the deployment is not expected to recover. The only permitted operation
   * is to retry deleting the deployment.
   */
  public const STATE_DELETE_FAILED = 'DELETE_FAILED';
  /**
   * Output only. The name of the Mirroring Deployment, in the format: `projects
   * /{project}/locations/{location}/mirroringDeployments/{mirroring_deployment}
   * `.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Most recent known state of the deployment.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The name of the Mirroring Deployment, in the format: `projects
   * /{project}/locations/{location}/mirroringDeployments/{mirroring_deployment}
   * `.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Most recent known state of the deployment.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING,
   * OUT_OF_SYNC, DELETE_FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MirroringDeploymentGroupDeployment::class, 'Google_Service_NetworkSecurity_MirroringDeploymentGroupDeployment');
