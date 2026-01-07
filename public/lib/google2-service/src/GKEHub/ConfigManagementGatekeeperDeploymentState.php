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

namespace Google\Service\GKEHub;

class ConfigManagementGatekeeperDeploymentState extends \Google\Model
{
  /**
   * Deployment's state cannot be determined.
   */
  public const GATEKEEPER_AUDIT_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const GATEKEEPER_AUDIT_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const GATEKEEPER_AUDIT_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const GATEKEEPER_AUDIT_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const GATEKEEPER_AUDIT_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const GATEKEEPER_CONTROLLER_MANAGER_STATE_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const GATEKEEPER_CONTROLLER_MANAGER_STATE_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const GATEKEEPER_CONTROLLER_MANAGER_STATE_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const GATEKEEPER_CONTROLLER_MANAGER_STATE_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const GATEKEEPER_CONTROLLER_MANAGER_STATE_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const GATEKEEPER_MUTATION_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const GATEKEEPER_MUTATION_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const GATEKEEPER_MUTATION_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const GATEKEEPER_MUTATION_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const GATEKEEPER_MUTATION_PENDING = 'PENDING';
  /**
   * Status of gatekeeper-audit deployment.
   *
   * @var string
   */
  public $gatekeeperAudit;
  /**
   * Status of gatekeeper-controller-manager pod.
   *
   * @var string
   */
  public $gatekeeperControllerManagerState;
  /**
   * Status of the pod serving the mutation webhook.
   *
   * @var string
   */
  public $gatekeeperMutation;

  /**
   * Status of gatekeeper-audit deployment.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::GATEKEEPER_AUDIT_* $gatekeeperAudit
   */
  public function setGatekeeperAudit($gatekeeperAudit)
  {
    $this->gatekeeperAudit = $gatekeeperAudit;
  }
  /**
   * @return self::GATEKEEPER_AUDIT_*
   */
  public function getGatekeeperAudit()
  {
    return $this->gatekeeperAudit;
  }
  /**
   * Status of gatekeeper-controller-manager pod.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::GATEKEEPER_CONTROLLER_MANAGER_STATE_* $gatekeeperControllerManagerState
   */
  public function setGatekeeperControllerManagerState($gatekeeperControllerManagerState)
  {
    $this->gatekeeperControllerManagerState = $gatekeeperControllerManagerState;
  }
  /**
   * @return self::GATEKEEPER_CONTROLLER_MANAGER_STATE_*
   */
  public function getGatekeeperControllerManagerState()
  {
    return $this->gatekeeperControllerManagerState;
  }
  /**
   * Status of the pod serving the mutation webhook.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::GATEKEEPER_MUTATION_* $gatekeeperMutation
   */
  public function setGatekeeperMutation($gatekeeperMutation)
  {
    $this->gatekeeperMutation = $gatekeeperMutation;
  }
  /**
   * @return self::GATEKEEPER_MUTATION_*
   */
  public function getGatekeeperMutation()
  {
    return $this->gatekeeperMutation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementGatekeeperDeploymentState::class, 'Google_Service_GKEHub_ConfigManagementGatekeeperDeploymentState');
