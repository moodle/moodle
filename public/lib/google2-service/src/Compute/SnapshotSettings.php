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

class SnapshotSettings extends \Google\Model
{
  protected $storageLocationType = SnapshotSettingsStorageLocationSettings::class;
  protected $storageLocationDataType = '';

  /**
   * Policy of which storage location is going to be resolved, and additional
   * data that particularizes how the policy is going to be carried out.
   *
   * @param SnapshotSettingsStorageLocationSettings $storageLocation
   */
  public function setStorageLocation(SnapshotSettingsStorageLocationSettings $storageLocation)
  {
    $this->storageLocation = $storageLocation;
  }
  /**
   * @return SnapshotSettingsStorageLocationSettings
   */
  public function getStorageLocation()
  {
    return $this->storageLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SnapshotSettings::class, 'Google_Service_Compute_SnapshotSettings');
