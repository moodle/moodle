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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1WorkloadComplianceStatus extends \Google\Model
{
  /**
   * Number of current resource violations which are not acknowledged.
   *
   * @var int
   */
  public $acknowledgedResourceViolationCount;
  /**
   * Number of current orgPolicy violations which are acknowledged.
   *
   * @var int
   */
  public $acknowledgedViolationCount;
  /**
   * Number of current resource violations which are acknowledged.
   *
   * @var int
   */
  public $activeResourceViolationCount;
  /**
   * Number of current orgPolicy violations which are not acknowledged.
   *
   * @var int
   */
  public $activeViolationCount;

  /**
   * Number of current resource violations which are not acknowledged.
   *
   * @param int $acknowledgedResourceViolationCount
   */
  public function setAcknowledgedResourceViolationCount($acknowledgedResourceViolationCount)
  {
    $this->acknowledgedResourceViolationCount = $acknowledgedResourceViolationCount;
  }
  /**
   * @return int
   */
  public function getAcknowledgedResourceViolationCount()
  {
    return $this->acknowledgedResourceViolationCount;
  }
  /**
   * Number of current orgPolicy violations which are acknowledged.
   *
   * @param int $acknowledgedViolationCount
   */
  public function setAcknowledgedViolationCount($acknowledgedViolationCount)
  {
    $this->acknowledgedViolationCount = $acknowledgedViolationCount;
  }
  /**
   * @return int
   */
  public function getAcknowledgedViolationCount()
  {
    return $this->acknowledgedViolationCount;
  }
  /**
   * Number of current resource violations which are acknowledged.
   *
   * @param int $activeResourceViolationCount
   */
  public function setActiveResourceViolationCount($activeResourceViolationCount)
  {
    $this->activeResourceViolationCount = $activeResourceViolationCount;
  }
  /**
   * @return int
   */
  public function getActiveResourceViolationCount()
  {
    return $this->activeResourceViolationCount;
  }
  /**
   * Number of current orgPolicy violations which are not acknowledged.
   *
   * @param int $activeViolationCount
   */
  public function setActiveViolationCount($activeViolationCount)
  {
    $this->activeViolationCount = $activeViolationCount;
  }
  /**
   * @return int
   */
  public function getActiveViolationCount()
  {
    return $this->activeViolationCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1WorkloadComplianceStatus::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1WorkloadComplianceStatus');
