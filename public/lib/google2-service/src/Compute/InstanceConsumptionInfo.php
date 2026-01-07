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

class InstanceConsumptionInfo extends \Google\Model
{
  /**
   * Output only. The number of virtual CPUs that are available to the instance.
   *
   * @var int
   */
  public $guestCpus;
  /**
   * Output only. The amount of local SSD storage available to the instance,
   * defined in GiB.
   *
   * @var int
   */
  public $localSsdGb;
  /**
   * Output only. The amount of physical memory available to the instance,
   * defined in MiB.
   *
   * @var int
   */
  public $memoryMb;
  /**
   * Output only. The minimal guaranteed number of virtual CPUs that are
   * reserved.
   *
   * @var int
   */
  public $minNodeCpus;

  /**
   * Output only. The number of virtual CPUs that are available to the instance.
   *
   * @param int $guestCpus
   */
  public function setGuestCpus($guestCpus)
  {
    $this->guestCpus = $guestCpus;
  }
  /**
   * @return int
   */
  public function getGuestCpus()
  {
    return $this->guestCpus;
  }
  /**
   * Output only. The amount of local SSD storage available to the instance,
   * defined in GiB.
   *
   * @param int $localSsdGb
   */
  public function setLocalSsdGb($localSsdGb)
  {
    $this->localSsdGb = $localSsdGb;
  }
  /**
   * @return int
   */
  public function getLocalSsdGb()
  {
    return $this->localSsdGb;
  }
  /**
   * Output only. The amount of physical memory available to the instance,
   * defined in MiB.
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
   * Output only. The minimal guaranteed number of virtual CPUs that are
   * reserved.
   *
   * @param int $minNodeCpus
   */
  public function setMinNodeCpus($minNodeCpus)
  {
    $this->minNodeCpus = $minNodeCpus;
  }
  /**
   * @return int
   */
  public function getMinNodeCpus()
  {
    return $this->minNodeCpus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceConsumptionInfo::class, 'Google_Service_Compute_InstanceConsumptionInfo');
