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

class SaveSnapshotResponse extends \Google\Model
{
  /**
   * The fully-resolved Cloud Storage path of the created snapshot, e.g.:
   * "gs://my-bucket/snapshots/project_location_environment_timestamp". This
   * field is populated only if the snapshot creation was successful.
   *
   * @var string
   */
  public $snapshotPath;

  /**
   * The fully-resolved Cloud Storage path of the created snapshot, e.g.:
   * "gs://my-bucket/snapshots/project_location_environment_timestamp". This
   * field is populated only if the snapshot creation was successful.
   *
   * @param string $snapshotPath
   */
  public function setSnapshotPath($snapshotPath)
  {
    $this->snapshotPath = $snapshotPath;
  }
  /**
   * @return string
   */
  public function getSnapshotPath()
  {
    return $this->snapshotPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SaveSnapshotResponse::class, 'Google_Service_CloudComposer_SaveSnapshotResponse');
