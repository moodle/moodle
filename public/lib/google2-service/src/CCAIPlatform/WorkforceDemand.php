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

namespace Google\Service\CCAIPlatform;

class WorkforceDemand extends \Google\Model
{
  /**
   * Optional. Number of employees needed to cover the demand for this interval.
   *
   * @var int
   */
  public $employeeCount;
  protected $endTimeType = DateTime::class;
  protected $endTimeDataType = '';
  protected $startTimeType = DateTime::class;
  protected $startTimeDataType = '';

  /**
   * Optional. Number of employees needed to cover the demand for this interval.
   *
   * @param int $employeeCount
   */
  public function setEmployeeCount($employeeCount)
  {
    $this->employeeCount = $employeeCount;
  }
  /**
   * @return int
   */
  public function getEmployeeCount()
  {
    return $this->employeeCount;
  }
  /**
   * Required. End of the time interval for the given demand (exclusive). These
   * values are read down to the minute; seconds and all smaller units are
   * ignored.
   *
   * @param DateTime $endTime
   */
  public function setEndTime(DateTime $endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return DateTime
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Required. Start of the time interval for the given demand (inclusive).
   * These values are read down to the minute; seconds and all smaller units are
   * ignored.
   *
   * @param DateTime $startTime
   */
  public function setStartTime(DateTime $startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return DateTime
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkforceDemand::class, 'Google_Service_CCAIPlatform_WorkforceDemand');
