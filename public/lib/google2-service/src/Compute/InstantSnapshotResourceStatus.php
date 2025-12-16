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

class InstantSnapshotResourceStatus extends \Google\Model
{
  /**
   * [Output Only] The storage size of this instant snapshot.
   *
   * @var string
   */
  public $storageSizeBytes;

  /**
   * [Output Only] The storage size of this instant snapshot.
   *
   * @param string $storageSizeBytes
   */
  public function setStorageSizeBytes($storageSizeBytes)
  {
    $this->storageSizeBytes = $storageSizeBytes;
  }
  /**
   * @return string
   */
  public function getStorageSizeBytes()
  {
    return $this->storageSizeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstantSnapshotResourceStatus::class, 'Google_Service_Compute_InstantSnapshotResourceStatus');
