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

namespace Google\Service\Compute;

class AllocationAggregateReservationReservedResourceInfoAccelerator extends \Google\Model
{
  /**
   * Number of accelerators of specified type.
   *
   * @var int
   */
  public $acceleratorCount;
  /**
   * Full or partial URL to accelerator type. e.g.
   * "projects/{PROJECT}/zones/{ZONE}/acceleratorTypes/ct4l"
   *
   * @var string
   */
  public $acceleratorType;

  /**
   * Number of accelerators of specified type.
   *
   * @param int $acceleratorCount
   */
  public function setAcceleratorCount($acceleratorCount)
  {
    $this->acceleratorCount = $acceleratorCount;
  }
  /**
   * @return int
   */
  public function getAcceleratorCount()
  {
    return $this->acceleratorCount;
  }
  /**
   * Full or partial URL to accelerator type. e.g.
   * "projects/{PROJECT}/zones/{ZONE}/acceleratorTypes/ct4l"
   *
   * @param string $acceleratorType
   */
  public function setAcceleratorType($acceleratorType)
  {
    $this->acceleratorType = $acceleratorType;
  }
  /**
   * @return string
   */
  public function getAcceleratorType()
  {
    return $this->acceleratorType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationAggregateReservationReservedResourceInfoAccelerator::class, 'Google_Service_Compute_AllocationAggregateReservationReservedResourceInfoAccelerator');
