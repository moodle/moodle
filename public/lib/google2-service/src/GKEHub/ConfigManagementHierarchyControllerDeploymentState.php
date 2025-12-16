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

class ConfigManagementHierarchyControllerDeploymentState extends \Google\Model
{
  /**
   * Deployment's state cannot be determined.
   */
  public const EXTENSION_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const EXTENSION_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const EXTENSION_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const EXTENSION_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const EXTENSION_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const HNC_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const HNC_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const HNC_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const HNC_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const HNC_PENDING = 'PENDING';
  /**
   * The deployment state for Hierarchy Controller extension (e.g. v0.7.0-hc.1).
   *
   * @var string
   */
  public $extension;
  /**
   * The deployment state for open source HNC (e.g. v0.7.0-hc.0).
   *
   * @var string
   */
  public $hnc;

  /**
   * The deployment state for Hierarchy Controller extension (e.g. v0.7.0-hc.1).
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::EXTENSION_* $extension
   */
  public function setExtension($extension)
  {
    $this->extension = $extension;
  }
  /**
   * @return self::EXTENSION_*
   */
  public function getExtension()
  {
    return $this->extension;
  }
  /**
   * The deployment state for open source HNC (e.g. v0.7.0-hc.0).
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::HNC_* $hnc
   */
  public function setHnc($hnc)
  {
    $this->hnc = $hnc;
  }
  /**
   * @return self::HNC_*
   */
  public function getHnc()
  {
    return $this->hnc;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementHierarchyControllerDeploymentState::class, 'Google_Service_GKEHub_ConfigManagementHierarchyControllerDeploymentState');
