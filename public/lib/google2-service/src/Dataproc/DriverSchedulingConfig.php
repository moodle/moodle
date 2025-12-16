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

class DriverSchedulingConfig extends \Google\Model
{
  /**
   * Required. The amount of memory in MB the driver is requesting.
   *
   * @var int
   */
  public $memoryMb;
  /**
   * Required. The number of vCPUs the driver is requesting.
   *
   * @var int
   */
  public $vcores;

  /**
   * Required. The amount of memory in MB the driver is requesting.
   *
   * @param int $memoryMb
   */
  public function setMemoryMb($memoryMb)
  {
    $this->memoryMb = $memoryMb;
  }
  /**
   * @return int
   */
  public function getMemoryMb()
  {
    return $this->memoryMb;
  }
  /**
   * Required. The number of vCPUs the driver is requesting.
   *
   * @param int $vcores
   */
  public function setVcores($vcores)
  {
    $this->vcores = $vcores;
  }
  /**
   * @return int
   */
  public function getVcores()
  {
    return $this->vcores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriverSchedulingConfig::class, 'Google_Service_Dataproc_DriverSchedulingConfig');
