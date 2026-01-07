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

namespace Google\Service\Container;

class EphemeralStorageLocalSsdConfig extends \Google\Model
{
  /**
   * Number of local SSDs to use for GKE Data Cache.
   *
   * @var int
   */
  public $dataCacheCount;
  /**
   * Number of local SSDs to use to back ephemeral storage. Uses NVMe
   * interfaces. A zero (or unset) value has different meanings depending on
   * machine type being used: 1. For pre-Gen3 machines, which support flexible
   * numbers of local ssds, zero (or unset) means to disable using local SSDs as
   * ephemeral storage. The limit for this value is dependent upon the maximum
   * number of disk available on a machine per zone. See:
   * https://cloud.google.com/compute/docs/disks/local-ssd for more information.
   * 2. For Gen3 machines which dictate a specific number of local ssds, zero
   * (or unset) means to use the default number of local ssds that goes with
   * that machine type. For example, for a c3-standard-8-lssd machine, 2 local
   * ssds would be provisioned. For c3-standard-8 (which doesn't support local
   * ssds), 0 will be provisioned. See
   * https://cloud.google.com/compute/docs/disks/local-
   * ssd#choose_number_local_ssds for more info.
   *
   * @var int
   */
  public $localSsdCount;

  /**
   * Number of local SSDs to use for GKE Data Cache.
   *
   * @param int $dataCacheCount
   */
  public function setDataCacheCount($dataCacheCount)
  {
    $this->dataCacheCount = $dataCacheCount;
  }
  /**
   * @return int
   */
  public function getDataCacheCount()
  {
    return $this->dataCacheCount;
  }
  /**
   * Number of local SSDs to use to back ephemeral storage. Uses NVMe
   * interfaces. A zero (or unset) value has different meanings depending on
   * machine type being used: 1. For pre-Gen3 machines, which support flexible
   * numbers of local ssds, zero (or unset) means to disable using local SSDs as
   * ephemeral storage. The limit for this value is dependent upon the maximum
   * number of disk available on a machine per zone. See:
   * https://cloud.google.com/compute/docs/disks/local-ssd for more information.
   * 2. For Gen3 machines which dictate a specific number of local ssds, zero
   * (or unset) means to use the default number of local ssds that goes with
   * that machine type. For example, for a c3-standard-8-lssd machine, 2 local
   * ssds would be provisioned. For c3-standard-8 (which doesn't support local
   * ssds), 0 will be provisioned. See
   * https://cloud.google.com/compute/docs/disks/local-
   * ssd#choose_number_local_ssds for more info.
   *
   * @param int $localSsdCount
   */
  public function setLocalSsdCount($localSsdCount)
  {
    $this->localSsdCount = $localSsdCount;
  }
  /**
   * @return int
   */
  public function getLocalSsdCount()
  {
    return $this->localSsdCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EphemeralStorageLocalSsdConfig::class, 'Google_Service_Container_EphemeralStorageLocalSsdConfig');
