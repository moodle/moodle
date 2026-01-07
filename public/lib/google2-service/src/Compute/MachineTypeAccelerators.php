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

class MachineTypeAccelerators extends \Google\Model
{
  /**
   * Number of accelerator cards exposed to the guest.
   *
   * @var int
   */
  public $guestAcceleratorCount;
  /**
   * The accelerator type resource name, not a full URL, e.g.nvidia-tesla-t4.
   *
   * @var string
   */
  public $guestAcceleratorType;

  /**
   * Number of accelerator cards exposed to the guest.
   *
   * @param int $guestAcceleratorCount
   */
  public function setGuestAcceleratorCount($guestAcceleratorCount)
  {
    $this->guestAcceleratorCount = $guestAcceleratorCount;
  }
  /**
   * @return int
   */
  public function getGuestAcceleratorCount()
  {
    return $this->guestAcceleratorCount;
  }
  /**
   * The accelerator type resource name, not a full URL, e.g.nvidia-tesla-t4.
   *
   * @param string $guestAcceleratorType
   */
  public function setGuestAcceleratorType($guestAcceleratorType)
  {
    $this->guestAcceleratorType = $guestAcceleratorType;
  }
  /**
   * @return string
   */
  public function getGuestAcceleratorType()
  {
    return $this->guestAcceleratorType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachineTypeAccelerators::class, 'Google_Service_Compute_MachineTypeAccelerators');
