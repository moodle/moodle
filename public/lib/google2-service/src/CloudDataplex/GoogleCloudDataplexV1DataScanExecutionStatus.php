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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataScanExecutionStatus extends \Google\Model
{
  /**
   * Optional. The time when the DataScanJob execution was created.
   *
   * @var string
   */
  public $latestJobCreateTime;
  /**
   * Optional. The time when the latest DataScanJob ended.
   *
   * @var string
   */
  public $latestJobEndTime;
  /**
   * Optional. The time when the latest DataScanJob started.
   *
   * @var string
   */
  public $latestJobStartTime;

  /**
   * Optional. The time when the DataScanJob execution was created.
   *
   * @param string $latestJobCreateTime
   */
  public function setLatestJobCreateTime($latestJobCreateTime)
  {
    $this->latestJobCreateTime = $latestJobCreateTime;
  }
  /**
   * @return string
   */
  public function getLatestJobCreateTime()
  {
    return $this->latestJobCreateTime;
  }
  /**
   * Optional. The time when the latest DataScanJob ended.
   *
   * @param string $latestJobEndTime
   */
  public function setLatestJobEndTime($latestJobEndTime)
  {
    $this->latestJobEndTime = $latestJobEndTime;
  }
  /**
   * @return string
   */
  public function getLatestJobEndTime()
  {
    return $this->latestJobEndTime;
  }
  /**
   * Optional. The time when the latest DataScanJob started.
   *
   * @param string $latestJobStartTime
   */
  public function setLatestJobStartTime($latestJobStartTime)
  {
    $this->latestJobStartTime = $latestJobStartTime;
  }
  /**
   * @return string
   */
  public function getLatestJobStartTime()
  {
    return $this->latestJobStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataScanExecutionStatus::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataScanExecutionStatus');
