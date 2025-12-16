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

namespace Google\Service\AdExchangeBuyerII;

class RelativeDateRange extends \Google\Model
{
  /**
   * The number of days in the requested date range, for example, for a range
   * spanning today: 1. For a range spanning the last 7 days: 7.
   *
   * @var int
   */
  public $durationDays;
  /**
   * The end date of the filter set, specified as the number of days before
   * today, for example, for a range where the last date is today: 0.
   *
   * @var int
   */
  public $offsetDays;

  /**
   * The number of days in the requested date range, for example, for a range
   * spanning today: 1. For a range spanning the last 7 days: 7.
   *
   * @param int $durationDays
   */
  public function setDurationDays($durationDays)
  {
    $this->durationDays = $durationDays;
  }
  /**
   * @return int
   */
  public function getDurationDays()
  {
    return $this->durationDays;
  }
  /**
   * The end date of the filter set, specified as the number of days before
   * today, for example, for a range where the last date is today: 0.
   *
   * @param int $offsetDays
   */
  public function setOffsetDays($offsetDays)
  {
    $this->offsetDays = $offsetDays;
  }
  /**
   * @return int
   */
  public function getOffsetDays()
  {
    return $this->offsetDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RelativeDateRange::class, 'Google_Service_AdExchangeBuyerII_RelativeDateRange');
