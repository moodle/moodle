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

class IdentityServiceState extends \Google\Model
{
  /**
   * Unspecified state
   */
  public const STATE_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * deployment succeeds
   */
  public const STATE_OK = 'OK';
  /**
   * Failure with error.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The reason of the failure.
   *
   * @var string
   */
  public $failureReason;
  /**
   * Installed AIS version. This is the AIS version installed on this member.
   * The values makes sense iff state is OK.
   *
   * @var string
   */
  public $installedVersion;
  protected $memberConfigType = IdentityServiceSpec::class;
  protected $memberConfigDataType = '';
  /**
   * Deployment state on this member
   *
   * @var string
   */
  public $state;

  /**
   * The reason of the failure.
   *
   * @param string $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return string
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * Installed AIS version. This is the AIS version installed on this member.
   * The values makes sense iff state is OK.
   *
   * @param string $installedVersion
   */
  public function setInstalledVersion($installedVersion)
  {
    $this->installedVersion = $installedVersion;
  }
  /**
   * @return string
   */
  public function getInstalledVersion()
  {
    return $this->installedVersion;
  }
  /**
   * Last reconciled membership configuration
   *
   * @param IdentityServiceSpec $memberConfig
   */
  public function setMemberConfig(IdentityServiceSpec $memberConfig)
  {
    $this->memberConfig = $memberConfig;
  }
  /**
   * @return IdentityServiceSpec
   */
  public function getMemberConfig()
  {
    return $this->memberConfig;
  }
  /**
   * Deployment state on this member
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, OK, ERROR
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
class_alias(IdentityServiceState::class, 'Google_Service_GKEHub_IdentityServiceState');
