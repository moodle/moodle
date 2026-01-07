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

namespace Google\Service\Dataproc;

class InstanceSelectionResult extends \Google\Model
{
  /**
   * Output only. Full machine-type names, e.g. "n1-standard-16".
   *
   * @var string
   */
  public $machineType;
  /**
   * Output only. Number of VM provisioned with the machine_type.
   *
   * @var int
   */
  public $vmCount;

  /**
   * Output only. Full machine-type names, e.g. "n1-standard-16".
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Output only. Number of VM provisioned with the machine_type.
   *
   * @param int $vmCount
   */
  public function setVmCount($vmCount)
  {
    $this->vmCount = $vmCount;
  }
  /**
   * @return int
   */
  public function getVmCount()
  {
    return $this->vmCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceSelectionResult::class, 'Google_Service_Dataproc_InstanceSelectionResult');
