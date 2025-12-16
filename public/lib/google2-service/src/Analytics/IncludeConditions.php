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

namespace Google\Service\Analytics;

class IncludeConditions extends \Google\Model
{
  /**
   * The look-back window lets you specify a time frame for evaluating the
   * behavior that qualifies users for your audience. For example, if your
   * filters include users from Central Asia, and Transactions Greater than 2,
   * and you set the look-back window to 14 days, then any user from Central
   * Asia whose cumulative transactions exceed 2 during the last 14 days is
   * added to the audience.
   *
   * @var int
   */
  public $daysToLookBack;
  /**
   * Boolean indicating whether this segment is a smart list.
   * https://support.google.com/analytics/answer/4628577
   *
   * @var bool
   */
  public $isSmartList;
  /**
   * Resource type for include conditions.
   *
   * @var string
   */
  public $kind;
  /**
   * Number of days (in the range 1 to 540) a user remains in the audience.
   *
   * @var int
   */
  public $membershipDurationDays;
  /**
   * The segment condition that will cause a user to be added to an audience.
   *
   * @var string
   */
  public $segment;

  /**
   * The look-back window lets you specify a time frame for evaluating the
   * behavior that qualifies users for your audience. For example, if your
   * filters include users from Central Asia, and Transactions Greater than 2,
   * and you set the look-back window to 14 days, then any user from Central
   * Asia whose cumulative transactions exceed 2 during the last 14 days is
   * added to the audience.
   *
   * @param int $daysToLookBack
   */
  public function setDaysToLookBack($daysToLookBack)
  {
    $this->daysToLookBack = $daysToLookBack;
  }
  /**
   * @return int
   */
  public function getDaysToLookBack()
  {
    return $this->daysToLookBack;
  }
  /**
   * Boolean indicating whether this segment is a smart list.
   * https://support.google.com/analytics/answer/4628577
   *
   * @param bool $isSmartList
   */
  public function setIsSmartList($isSmartList)
  {
    $this->isSmartList = $isSmartList;
  }
  /**
   * @return bool
   */
  public function getIsSmartList()
  {
    return $this->isSmartList;
  }
  /**
   * Resource type for include conditions.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Number of days (in the range 1 to 540) a user remains in the audience.
   *
   * @param int $membershipDurationDays
   */
  public function setMembershipDurationDays($membershipDurationDays)
  {
    $this->membershipDurationDays = $membershipDurationDays;
  }
  /**
   * @return int
   */
  public function getMembershipDurationDays()
  {
    return $this->membershipDurationDays;
  }
  /**
   * The segment condition that will cause a user to be added to an audience.
   *
   * @param string $segment
   */
  public function setSegment($segment)
  {
    $this->segment = $segment;
  }
  /**
   * @return string
   */
  public function getSegment()
  {
    return $this->segment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IncludeConditions::class, 'Google_Service_Analytics_IncludeConditions');
