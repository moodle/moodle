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

class GoogleCloudAiplatformV1ResumeScheduleRequest extends \Google\Model
{
  /**
   * Optional. Whether to backfill missed runs when the schedule is resumed from
   * PAUSED state. If set to true, all missed runs will be scheduled. New runs
   * will be scheduled after the backfill is complete. This will also update
   * Schedule.catch_up field. Default to false.
   *
   * @var bool
   */
  public $catchUp;

  /**
   * Optional. Whether to backfill missed runs when the schedule is resumed from
   * PAUSED state. If set to true, all missed runs will be scheduled. New runs
   * will be scheduled after the backfill is complete. This will also update
   * Schedule.catch_up field. Default to false.
   *
   * @param bool $catchUp
   */
  public function setCatchUp($catchUp)
  {
    $this->catchUp = $catchUp;
  }
  /**
   * @return bool
   */
  public function getCatchUp()
  {
    return $this->catchUp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ResumeScheduleRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ResumeScheduleRequest');
