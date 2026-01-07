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

class ResourcePolicySnapshotSchedulePolicySnapshotProperties extends \Google\Collection
{
  protected $collection_key = 'storageLocations';
  /**
   * Chain name that the snapshot is created in.
   *
   * @var string
   */
  public $chainName;
  /**
   * Indication to perform a 'guest aware' snapshot.
   *
   * @var bool
   */
  public $guestFlush;
  /**
   * Labels to apply to scheduled snapshots. These can be later modified by the
   * setLabels method. Label values may be empty.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Cloud Storage bucket storage location of the auto snapshot (regional or
   * multi-regional).
   *
   * @var string[]
   */
  public $storageLocations;

  /**
   * Chain name that the snapshot is created in.
   *
   * @param string $chainName
   */
  public function setChainName($chainName)
  {
    $this->chainName = $chainName;
  }
  /**
   * @return string
   */
  public function getChainName()
  {
    return $this->chainName;
  }
  /**
   * Indication to perform a 'guest aware' snapshot.
   *
   * @param bool $guestFlush
   */
  public function setGuestFlush($guestFlush)
  {
    $this->guestFlush = $guestFlush;
  }
  /**
   * @return bool
   */
  public function getGuestFlush()
  {
    return $this->guestFlush;
  }
  /**
   * Labels to apply to scheduled snapshots. These can be later modified by the
   * setLabels method. Label values may be empty.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Cloud Storage bucket storage location of the auto snapshot (regional or
   * multi-regional).
   *
   * @param string[] $storageLocations
   */
  public function setStorageLocations($storageLocations)
  {
    $this->storageLocations = $storageLocations;
  }
  /**
   * @return string[]
   */
  public function getStorageLocations()
  {
    return $this->storageLocations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicySnapshotSchedulePolicySnapshotProperties::class, 'Google_Service_Compute_ResourcePolicySnapshotSchedulePolicySnapshotProperties');
