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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1CheckTrialEarlyStoppingStateResponse extends \Google\Model
{
  /**
   * The time at which operation processing completed.
   *
   * @var string
   */
  public $endTime;
  /**
   * True if the Trial should stop.
   *
   * @var bool
   */
  public $shouldStop;
  /**
   * The time at which the operation was started.
   *
   * @var string
   */
  public $startTime;

  /**
   * The time at which operation processing completed.
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
   * True if the Trial should stop.
   *
   * @param bool $shouldStop
   */
  public function setShouldStop($shouldStop)
  {
    $this->shouldStop = $shouldStop;
  }
  /**
   * @return bool
   */
  public function getShouldStop()
  {
    return $this->shouldStop;
  }
  /**
   * The time at which the operation was started.
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
class_alias(GoogleCloudMlV1CheckTrialEarlyStoppingStateResponse::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1CheckTrialEarlyStoppingStateResponse');
