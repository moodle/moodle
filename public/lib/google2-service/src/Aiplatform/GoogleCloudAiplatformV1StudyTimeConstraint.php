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

class GoogleCloudAiplatformV1StudyTimeConstraint extends \Google\Model
{
  /**
   * Compares the wallclock time to this time. Must use UTC timezone.
   *
   * @var string
   */
  public $endTime;
  /**
   * Counts the wallclock time passed since the creation of this Study.
   *
   * @var string
   */
  public $maxDuration;

  /**
   * Compares the wallclock time to this time. Must use UTC timezone.
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
   * Counts the wallclock time passed since the creation of this Study.
   *
   * @param string $maxDuration
   */
  public function setMaxDuration($maxDuration)
  {
    $this->maxDuration = $maxDuration;
  }
  /**
   * @return string
   */
  public function getMaxDuration()
  {
    return $this->maxDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudyTimeConstraint::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudyTimeConstraint');
