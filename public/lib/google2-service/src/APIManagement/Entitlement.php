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

namespace Google\Service\APIManagement;

class Entitlement extends \Google\Model
{
  /**
   * Whether API Observation is entitled.
   *
   * @var bool
   */
  public $apiObservationEntitled;
  /**
   * Project number of associated billing project that has Apigee and Advanced
   * API Security entitled.
   *
   * @var string
   */
  public $billingProjectNumber;
  /**
   * Output only. The time of the entitlement creation.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The entitlement resource name
   * `projects/{project}/locations/{location}/entitlement`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time of the entitlement update.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Whether API Observation is entitled.
   *
   * @param bool $apiObservationEntitled
   */
  public function setApiObservationEntitled($apiObservationEntitled)
  {
    $this->apiObservationEntitled = $apiObservationEntitled;
  }
  /**
   * @return bool
   */
  public function getApiObservationEntitled()
  {
    return $this->apiObservationEntitled;
  }
  /**
   * Project number of associated billing project that has Apigee and Advanced
   * API Security entitled.
   *
   * @param string $billingProjectNumber
   */
  public function setBillingProjectNumber($billingProjectNumber)
  {
    $this->billingProjectNumber = $billingProjectNumber;
  }
  /**
   * @return string
   */
  public function getBillingProjectNumber()
  {
    return $this->billingProjectNumber;
  }
  /**
   * Output only. The time of the entitlement creation.
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
   * Identifier. The entitlement resource name
   * `projects/{project}/locations/{location}/entitlement`
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
   * Output only. The time of the entitlement update.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entitlement::class, 'Google_Service_APIManagement_Entitlement');
