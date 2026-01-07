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

namespace Google\Service\Monitoring;

class Hourly extends \Google\Model
{
  /**
   * Optional. The number of minutes after the hour (in UTC) to run the query.
   * Must be greater than or equal to 0 minutes and less than or equal to 59
   * minutes. If left unspecified, then an arbitrary offset is used.
   *
   * @var int
   */
  public $minuteOffset;
  /**
   * Required. The number of hours between runs. Must be greater than or equal
   * to 1 hour and less than or equal to 48 hours.
   *
   * @var int
   */
  public $periodicity;

  /**
   * Optional. The number of minutes after the hour (in UTC) to run the query.
   * Must be greater than or equal to 0 minutes and less than or equal to 59
   * minutes. If left unspecified, then an arbitrary offset is used.
   *
   * @param int $minuteOffset
   */
  public function setMinuteOffset($minuteOffset)
  {
    $this->minuteOffset = $minuteOffset;
  }
  /**
   * @return int
   */
  public function getMinuteOffset()
  {
    return $this->minuteOffset;
  }
  /**
   * Required. The number of hours between runs. Must be greater than or equal
   * to 1 hour and less than or equal to 48 hours.
   *
   * @param int $periodicity
   */
  public function setPeriodicity($periodicity)
  {
    $this->periodicity = $periodicity;
  }
  /**
   * @return int
   */
  public function getPeriodicity()
  {
    return $this->periodicity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Hourly::class, 'Google_Service_Monitoring_Hourly');
