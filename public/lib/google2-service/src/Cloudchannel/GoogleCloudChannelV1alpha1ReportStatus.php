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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1alpha1ReportStatus extends \Google\Model
{
  /**
   * Not used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Report processing started.
   */
  public const STATE_STARTED = 'STARTED';
  /**
   * Data generated from the report is being staged.
   */
  public const STATE_WRITING = 'WRITING';
  /**
   * Report data is available for access.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Report failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The report generation's completion time.
   *
   * @var string
   */
  public $endTime;
  /**
   * The report generation's start time.
   *
   * @var string
   */
  public $startTime;
  /**
   * The current state of the report generation process.
   *
   * @var string
   */
  public $state;

  /**
   * The report generation's completion time.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The report generation's start time.
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
  /**
   * The current state of the report generation process.
   *
   * Accepted values: STATE_UNSPECIFIED, STARTED, WRITING, AVAILABLE, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1ReportStatus::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1ReportStatus');
