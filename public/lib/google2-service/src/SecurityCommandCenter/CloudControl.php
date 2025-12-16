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

namespace Google\Service\SecurityCommandCenter;

class CloudControl extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_CLOUD_CONTROL_TYPE_UNSPECIFIED = 'CLOUD_CONTROL_TYPE_UNSPECIFIED';
  /**
   * Built in Cloud Control.
   */
  public const TYPE_BUILT_IN = 'BUILT_IN';
  /**
   * Custom Cloud Control.
   */
  public const TYPE_CUSTOM = 'CUSTOM';
  /**
   * Name of the CloudControl associated with the finding.
   *
   * @var string
   */
  public $cloudControlName;
  /**
   * Policy type of the CloudControl
   *
   * @var string
   */
  public $policyType;
  /**
   * Type of cloud control.
   *
   * @var string
   */
  public $type;
  /**
   * Version of the Cloud Control
   *
   * @var int
   */
  public $version;

  /**
   * Name of the CloudControl associated with the finding.
   *
   * @param string $cloudControlName
   */
  public function setCloudControlName($cloudControlName)
  {
    $this->cloudControlName = $cloudControlName;
  }
  /**
   * @return string
   */
  public function getCloudControlName()
  {
    return $this->cloudControlName;
  }
  /**
   * Policy type of the CloudControl
   *
   * @param string $policyType
   */
  public function setPolicyType($policyType)
  {
    $this->policyType = $policyType;
  }
  /**
   * @return string
   */
  public function getPolicyType()
  {
    return $this->policyType;
  }
  /**
   * Type of cloud control.
   *
   * Accepted values: CLOUD_CONTROL_TYPE_UNSPECIFIED, BUILT_IN, CUSTOM
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Version of the Cloud Control
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudControl::class, 'Google_Service_SecurityCommandCenter_CloudControl');
