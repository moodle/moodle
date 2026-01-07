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

namespace Google\Service\OnDemandScanning;

class DeploymentOccurrence extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const PLATFORM_PLATFORM_UNSPECIFIED = 'PLATFORM_UNSPECIFIED';
  /**
   * Google Container Engine.
   */
  public const PLATFORM_GKE = 'GKE';
  /**
   * Google App Engine: Flexible Environment.
   */
  public const PLATFORM_FLEX = 'FLEX';
  /**
   * Custom user-defined platform.
   */
  public const PLATFORM_CUSTOM = 'CUSTOM';
  protected $collection_key = 'resourceUri';
  /**
   * Address of the runtime element hosting this deployment.
   *
   * @var string
   */
  public $address;
  /**
   * Configuration used to create this deployment.
   *
   * @var string
   */
  public $config;
  /**
   * Required. Beginning of the lifetime of this deployment.
   *
   * @var string
   */
  public $deployTime;
  /**
   * Platform hosting this deployment.
   *
   * @var string
   */
  public $platform;
  /**
   * Output only. Resource URI for the artifact being deployed taken from the
   * deployable field with the same name.
   *
   * @var string[]
   */
  public $resourceUri;
  /**
   * End of the lifetime of this deployment.
   *
   * @var string
   */
  public $undeployTime;
  /**
   * Identity of the user that triggered this deployment.
   *
   * @var string
   */
  public $userEmail;

  /**
   * Address of the runtime element hosting this deployment.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Configuration used to create this deployment.
   *
   * @param string $config
   */
  public function setConfig($config)
  {
    $this->config = $config;
  }
  /**
   * @return string
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Required. Beginning of the lifetime of this deployment.
   *
   * @param string $deployTime
   */
  public function setDeployTime($deployTime)
  {
    $this->deployTime = $deployTime;
  }
  /**
   * @return string
   */
  public function getDeployTime()
  {
    return $this->deployTime;
  }
  /**
   * Platform hosting this deployment.
   *
   * Accepted values: PLATFORM_UNSPECIFIED, GKE, FLEX, CUSTOM
   *
   * @param self::PLATFORM_* $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return self::PLATFORM_*
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * Output only. Resource URI for the artifact being deployed taken from the
   * deployable field with the same name.
   *
   * @param string[] $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string[]
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * End of the lifetime of this deployment.
   *
   * @param string $undeployTime
   */
  public function setUndeployTime($undeployTime)
  {
    $this->undeployTime = $undeployTime;
  }
  /**
   * @return string
   */
  public function getUndeployTime()
  {
    return $this->undeployTime;
  }
  /**
   * Identity of the user that triggered this deployment.
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeploymentOccurrence::class, 'Google_Service_OnDemandScanning_DeploymentOccurrence');
