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

namespace Google\Service\VMwareEngine;

class HcxActivationKey extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * State of a newly generated activation key.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * State of key when it has been used to activate HCX appliance.
   */
  public const STATE_CONSUMED = 'CONSUMED';
  /**
   * State of key when it is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Output only. HCX activation key.
   *
   * @var string
   */
  public $activationKey;
  /**
   * Output only. Creation time of HCX activation key.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The resource name of this HcxActivationKey. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/privateClouds/my-
   * cloud/hcxActivationKeys/my-key`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of HCX activation key.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. HCX activation key.
   *
   * @param string $activationKey
   */
  public function setActivationKey($activationKey)
  {
    $this->activationKey = $activationKey;
  }
  /**
   * @return string
   */
  public function getActivationKey()
  {
    return $this->activationKey;
  }
  /**
   * Output only. Creation time of HCX activation key.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The resource name of this HcxActivationKey. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/privateClouds/my-
   * cloud/hcxActivationKeys/my-key`
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
   * Output only. State of HCX activation key.
   *
   * Accepted values: STATE_UNSPECIFIED, AVAILABLE, CONSUMED, CREATING
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
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HcxActivationKey::class, 'Google_Service_VMwareEngine_HcxActivationKey');
