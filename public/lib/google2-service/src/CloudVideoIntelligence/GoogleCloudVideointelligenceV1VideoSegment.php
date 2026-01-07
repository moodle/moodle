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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1VideoSegment extends \Google\Model
{
  /**
   * Time-offset, relative to the beginning of the video, corresponding to the
   * end of the segment (inclusive).
   *
   * @var string
   */
  public $endTimeOffset;
  /**
   * Time-offset, relative to the beginning of the video, corresponding to the
   * start of the segment (inclusive).
   *
   * @var string
   */
  public $startTimeOffset;

  /**
   * Time-offset, relative to the beginning of the video, corresponding to the
   * end of the segment (inclusive).
   *
   * @param string $endTimeOffset
   */
  public function setEndTimeOffset($endTimeOffset)
  {
    $this->endTimeOffset = $endTimeOffset;
  }
  /**
   * @return string
   */
  public function getEndTimeOffset()
  {
    return $this->endTimeOffset;
  }
  /**
   * Time-offset, relative to the beginning of the video, corresponding to the
   * start of the segment (inclusive).
   *
   * @param string $startTimeOffset
   */
  public function setStartTimeOffset($startTimeOffset)
  {
    $this->startTimeOffset = $startTimeOffset;
  }
  /**
   * @return string
   */
  public function getStartTimeOffset()
  {
    return $this->startTimeOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1VideoSegment::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1VideoSegment');
