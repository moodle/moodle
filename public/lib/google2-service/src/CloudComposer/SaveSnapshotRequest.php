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

namespace Google\Service\CloudComposer;

class SaveSnapshotRequest extends \Google\Model
{
  /**
   * Location in a Cloud Storage where the snapshot is going to be stored, e.g.:
   * "gs://my-bucket/snapshots".
   *
   * @var string
   */
  public $snapshotLocation;

  /**
   * Location in a Cloud Storage where the snapshot is going to be stored, e.g.:
   * "gs://my-bucket/snapshots".
   *
   * @param string $snapshotLocation
   */
  public function setSnapshotLocation($snapshotLocation)
  {
    $this->snapshotLocation = $snapshotLocation;
  }
  /**
   * @return string
   */
  public function getSnapshotLocation()
  {
    return $this->snapshotLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SaveSnapshotRequest::class, 'Google_Service_CloudComposer_SaveSnapshotRequest');
