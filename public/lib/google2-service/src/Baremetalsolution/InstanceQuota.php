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

namespace Google\Service\Baremetalsolution;

class InstanceQuota extends \Google\Model
{
  /**
   * Number of machines than can be created for the given location and
   * instance_type.
   *
   * @var int
   */
  public $availableMachineCount;
  /**
   * The gcp service of the provisioning quota.
   *
   * @var string
   */
  public $gcpService;
  /**
   * Instance type. Deprecated: use gcp_service.
   *
   * @deprecated
   * @var string
   */
  public $instanceType;
  /**
   * Location where the quota applies.
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The name of the instance quota.
   *
   * @var string
   */
  public $name;

  /**
   * Number of machines than can be created for the given location and
   * instance_type.
   *
   * @param int $availableMachineCount
   */
  public function setAvailableMachineCount($availableMachineCount)
  {
    $this->availableMachineCount = $availableMachineCount;
  }
  /**
   * @return int
   */
  public function getAvailableMachineCount()
  {
    return $this->availableMachineCount;
  }
  /**
   * The gcp service of the provisioning quota.
   *
   * @param string $gcpService
   */
  public function setGcpService($gcpService)
  {
    $this->gcpService = $gcpService;
  }
  /**
   * @return string
   */
  public function getGcpService()
  {
    return $this->gcpService;
  }
  /**
   * Instance type. Deprecated: use gcp_service.
   *
   * @deprecated
   * @param string $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * Location where the quota applies.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. The name of the instance quota.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceQuota::class, 'Google_Service_Baremetalsolution_InstanceQuota');
