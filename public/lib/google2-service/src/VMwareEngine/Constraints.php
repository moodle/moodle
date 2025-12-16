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

namespace Google\Service\VMwareEngine;

class Constraints extends \Google\Collection
{
  protected $collection_key = 'disallowedIntervals';
  protected $disallowedIntervalsType = WeeklyTimeInterval::class;
  protected $disallowedIntervalsDataType = 'array';
  /**
   * Output only. Minimum number of hours must be allotted for the upgrade
   * activities for each selected day. This is a minimum; the upgrade schedule
   * can allot more hours for the given day.
   *
   * @var int
   */
  public $minHoursDay;
  /**
   * Output only. The minimum number of weekly hours must be allotted for the
   * upgrade activities. This is just a minimum; the schedule can assign more
   * weekly hours.
   *
   * @var int
   */
  public $minHoursWeek;
  protected $rescheduleDateRangeType = Interval::class;
  protected $rescheduleDateRangeDataType = '';

  /**
   * Output only. Output Only. A list of intervals in which maintenance windows
   * are not allowed. Any time window that overlaps with any of these intervals
   * will be considered invalid.
   *
   * @param WeeklyTimeInterval[] $disallowedIntervals
   */
  public function setDisallowedIntervals($disallowedIntervals)
  {
    $this->disallowedIntervals = $disallowedIntervals;
  }
  /**
   * @return WeeklyTimeInterval[]
   */
  public function getDisallowedIntervals()
  {
    return $this->disallowedIntervals;
  }
  /**
   * Output only. Minimum number of hours must be allotted for the upgrade
   * activities for each selected day. This is a minimum; the upgrade schedule
   * can allot more hours for the given day.
   *
   * @param int $minHoursDay
   */
  public function setMinHoursDay($minHoursDay)
  {
    $this->minHoursDay = $minHoursDay;
  }
  /**
   * @return int
   */
  public function getMinHoursDay()
  {
    return $this->minHoursDay;
  }
  /**
   * Output only. The minimum number of weekly hours must be allotted for the
   * upgrade activities. This is just a minimum; the schedule can assign more
   * weekly hours.
   *
   * @param int $minHoursWeek
   */
  public function setMinHoursWeek($minHoursWeek)
  {
    $this->minHoursWeek = $minHoursWeek;
  }
  /**
   * @return int
   */
  public function getMinHoursWeek()
  {
    return $this->minHoursWeek;
  }
  /**
   * Output only. Output Only. The user can only reschedule an upgrade that
   * starts within this range.
   *
   * @param Interval $rescheduleDateRange
   */
  public function setRescheduleDateRange(Interval $rescheduleDateRange)
  {
    $this->rescheduleDateRange = $rescheduleDateRange;
  }
  /**
   * @return Interval
   */
  public function getRescheduleDateRange()
  {
    return $this->rescheduleDateRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Constraints::class, 'Google_Service_VMwareEngine_Constraints');
