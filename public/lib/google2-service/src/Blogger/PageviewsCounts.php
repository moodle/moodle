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

namespace Google\Service\Blogger;

class PageviewsCounts extends \Google\Model
{
  public const TIME_RANGE_ALL_TIME = 'ALL_TIME';
  public const TIME_RANGE_THIRTY_DAYS = 'THIRTY_DAYS';
  public const TIME_RANGE_SEVEN_DAYS = 'SEVEN_DAYS';
  /**
   * Count of page views for the given time range.
   *
   * @var string
   */
  public $count;
  /**
   * Time range the given count applies to.
   *
   * @var string
   */
  public $timeRange;

  /**
   * Count of page views for the given time range.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Time range the given count applies to.
   *
   * Accepted values: ALL_TIME, THIRTY_DAYS, SEVEN_DAYS
   *
   * @param self::TIME_RANGE_* $timeRange
   */
  public function setTimeRange($timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return self::TIME_RANGE_*
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PageviewsCounts::class, 'Google_Service_Blogger_PageviewsCounts');
