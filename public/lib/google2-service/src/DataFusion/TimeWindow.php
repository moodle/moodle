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

namespace Google\Service\DataFusion;

class TimeWindow extends \Google\Model
{
  /**
   * Required. The end time of the time window provided in [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. The end time should
   * take place after the start time. Example: "2024-01-02T12:04:06-06:00"
   *
   * @var string
   */
  public $endTime;
  /**
   * Required. The start time of the time window provided in [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. Example:
   * "2024-01-01T12:04:06-04:00"
   *
   * @var string
   */
  public $startTime;

  /**
   * Required. The end time of the time window provided in [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. The end time should
   * take place after the start time. Example: "2024-01-02T12:04:06-06:00"
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
   * Required. The start time of the time window provided in [RFC
   * 3339](https://www.ietf.org/rfc/rfc3339.txt) format. Example:
   * "2024-01-01T12:04:06-04:00"
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
class_alias(TimeWindow::class, 'Google_Service_DataFusion_TimeWindow');
