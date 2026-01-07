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

class Daily extends \Google\Model
{
  protected $executionTimeType = TimeOfDay::class;
  protected $executionTimeDataType = '';
  /**
   * Required. The number of days between runs. Must be greater than or equal to
   * 1 day and less than or equal to 31 days.
   *
   * @var int
   */
  public $periodicity;

  /**
   * Optional. The time of day (in UTC) at which the query should run. If left
   * unspecified, the server picks an arbitrary time of day and runs the query
   * at the same time each day.
   *
   * @param TimeOfDay $executionTime
   */
  public function setExecutionTime(TimeOfDay $executionTime)
  {
    $this->executionTime = $executionTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getExecutionTime()
  {
    return $this->executionTime;
  }
  /**
   * Required. The number of days between runs. Must be greater than or equal to
   * 1 day and less than or equal to 31 days.
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
class_alias(Daily::class, 'Google_Service_Monitoring_Daily');
