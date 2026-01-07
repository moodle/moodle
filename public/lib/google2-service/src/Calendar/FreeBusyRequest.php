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

namespace Google\Service\Calendar;

class FreeBusyRequest extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Maximal number of calendars for which FreeBusy information is to be
   * provided. Optional. Maximum value is 50.
   *
   * @var int
   */
  public $calendarExpansionMax;
  /**
   * Maximal number of calendar identifiers to be provided for a single group.
   * Optional. An error is returned for a group with more members than this
   * value. Maximum value is 100.
   *
   * @var int
   */
  public $groupExpansionMax;
  protected $itemsType = FreeBusyRequestItem::class;
  protected $itemsDataType = 'array';
  /**
   * The end of the interval for the query formatted as per RFC3339.
   *
   * @var string
   */
  public $timeMax;
  /**
   * The start of the interval for the query formatted as per RFC3339.
   *
   * @var string
   */
  public $timeMin;
  /**
   * Time zone used in the response. Optional. The default is UTC.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Maximal number of calendars for which FreeBusy information is to be
   * provided. Optional. Maximum value is 50.
   *
   * @param int $calendarExpansionMax
   */
  public function setCalendarExpansionMax($calendarExpansionMax)
  {
    $this->calendarExpansionMax = $calendarExpansionMax;
  }
  /**
   * @return int
   */
  public function getCalendarExpansionMax()
  {
    return $this->calendarExpansionMax;
  }
  /**
   * Maximal number of calendar identifiers to be provided for a single group.
   * Optional. An error is returned for a group with more members than this
   * value. Maximum value is 100.
   *
   * @param int $groupExpansionMax
   */
  public function setGroupExpansionMax($groupExpansionMax)
  {
    $this->groupExpansionMax = $groupExpansionMax;
  }
  /**
   * @return int
   */
  public function getGroupExpansionMax()
  {
    return $this->groupExpansionMax;
  }
  /**
   * List of calendars and/or groups to query.
   *
   * @param FreeBusyRequestItem[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return FreeBusyRequestItem[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * The end of the interval for the query formatted as per RFC3339.
   *
   * @param string $timeMax
   */
  public function setTimeMax($timeMax)
  {
    $this->timeMax = $timeMax;
  }
  /**
   * @return string
   */
  public function getTimeMax()
  {
    return $this->timeMax;
  }
  /**
   * The start of the interval for the query formatted as per RFC3339.
   *
   * @param string $timeMin
   */
  public function setTimeMin($timeMin)
  {
    $this->timeMin = $timeMin;
  }
  /**
   * @return string
   */
  public function getTimeMin()
  {
    return $this->timeMin;
  }
  /**
   * Time zone used in the response. Optional. The default is UTC.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FreeBusyRequest::class, 'Google_Service_Calendar_FreeBusyRequest');
