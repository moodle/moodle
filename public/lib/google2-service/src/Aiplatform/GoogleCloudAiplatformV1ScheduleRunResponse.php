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

class GoogleCloudAiplatformV1ScheduleRunResponse extends \Google\Model
{
  /**
   * The response of the scheduled run.
   *
   * @var string
   */
  public $runResponse;
  /**
   * The scheduled run time based on the user-specified schedule.
   *
   * @var string
   */
  public $scheduledRunTime;

  /**
   * The response of the scheduled run.
   *
   * @param string $runResponse
   */
  public function setRunResponse($runResponse)
  {
    $this->runResponse = $runResponse;
  }
  /**
   * @return string
   */
  public function getRunResponse()
  {
    return $this->runResponse;
  }
  /**
   * The scheduled run time based on the user-specified schedule.
   *
   * @param string $scheduledRunTime
   */
  public function setScheduledRunTime($scheduledRunTime)
  {
    $this->scheduledRunTime = $scheduledRunTime;
  }
  /**
   * @return string
   */
  public function getScheduledRunTime()
  {
    return $this->scheduledRunTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ScheduleRunResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ScheduleRunResponse');
