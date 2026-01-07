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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ExportFeatureValuesRequestSnapshotExport extends \Google\Model
{
  /**
   * Exports Feature values as of this timestamp. If not set, retrieve values as
   * of now. Timestamp, if present, must not have higher than millisecond
   * precision.
   *
   * @var string
   */
  public $snapshotTime;
  /**
   * Excludes Feature values with feature generation timestamp before this
   * timestamp. If not set, retrieve oldest values kept in Feature Store.
   * Timestamp, if present, must not have higher than millisecond precision.
   *
   * @var string
   */
  public $startTime;

  /**
   * Exports Feature values as of this timestamp. If not set, retrieve values as
   * of now. Timestamp, if present, must not have higher than millisecond
   * precision.
   *
   * @param string $snapshotTime
   */
  public function setSnapshotTime($snapshotTime)
  {
    $this->snapshotTime = $snapshotTime;
  }
  /**
   * @return string
   */
  public function getSnapshotTime()
  {
    return $this->snapshotTime;
  }
  /**
   * Excludes Feature values with feature generation timestamp before this
   * timestamp. If not set, retrieve oldest values kept in Feature Store.
   * Timestamp, if present, must not have higher than millisecond precision.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportFeatureValuesRequestSnapshotExport::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportFeatureValuesRequestSnapshotExport');
