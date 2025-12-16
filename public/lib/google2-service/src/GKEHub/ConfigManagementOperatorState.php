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

class ConfigManagementOperatorState extends \Google\Collection
{
  /**
   * Deployment's state cannot be determined.
   */
  public const DEPLOYMENT_STATE_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const DEPLOYMENT_STATE_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const DEPLOYMENT_STATE_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const DEPLOYMENT_STATE_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const DEPLOYMENT_STATE_PENDING = 'PENDING';
  protected $collection_key = 'errors';
  /**
   * The state of the Operator's deployment.
   *
   * @var string
   */
  public $deploymentState;
  protected $errorsType = ConfigManagementInstallError::class;
  protected $errorsDataType = 'array';
  /**
   * The semenatic version number of the operator.
   *
   * @var string
   */
  public $version;

  /**
   * The state of the Operator's deployment.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::DEPLOYMENT_STATE_* $deploymentState
   */
  public function setDeploymentState($deploymentState)
  {
    $this->deploymentState = $deploymentState;
  }
  /**
   * @return self::DEPLOYMENT_STATE_*
   */
  public function getDeploymentState()
  {
    return $this->deploymentState;
  }
  /**
   * Install errors.
   *
   * @param ConfigManagementInstallError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return ConfigManagementInstallError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The semenatic version number of the operator.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementOperatorState::class, 'Google_Service_GKEHub_ConfigManagementOperatorState');
