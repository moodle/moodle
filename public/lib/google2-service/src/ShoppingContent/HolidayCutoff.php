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

namespace Google\Service\ShoppingContent;

class HolidayCutoff extends \Google\Model
{
  /**
   * Date of the order deadline, in ISO 8601 format. For example, "2016-11-29"
   * for 29th November 2016. Required.
   *
   * @var string
   */
  public $deadlineDate;
  /**
   * Hour of the day on the deadline date until which the order has to be placed
   * to qualify for the delivery guarantee. Possible values are: 0 (midnight),
   * 1, ..., 12 (noon), 13, ..., 23. Required.
   *
   * @var string
   */
  public $deadlineHour;
  /**
   * Timezone identifier for the deadline hour (for example, "Europe/Zurich").
   * List of identifiers. Required.
   *
   * @var string
   */
  public $deadlineTimezone;
  /**
   * Unique identifier for the holiday. Required.
   *
   * @var string
   */
  public $holidayId;
  /**
   * Date on which the deadline will become visible to consumers in ISO 8601
   * format. For example, "2016-10-31" for 31st October 2016. Required.
   *
   * @var string
   */
  public $visibleFromDate;

  /**
   * Date of the order deadline, in ISO 8601 format. For example, "2016-11-29"
   * for 29th November 2016. Required.
   *
   * @param string $deadlineDate
   */
  public function setDeadlineDate($deadlineDate)
  {
    $this->deadlineDate = $deadlineDate;
  }
  /**
   * @return string
   */
  public function getDeadlineDate()
  {
    return $this->deadlineDate;
  }
  /**
   * Hour of the day on the deadline date until which the order has to be placed
   * to qualify for the delivery guarantee. Possible values are: 0 (midnight),
   * 1, ..., 12 (noon), 13, ..., 23. Required.
   *
   * @param string $deadlineHour
   */
  public function setDeadlineHour($deadlineHour)
  {
    $this->deadlineHour = $deadlineHour;
  }
  /**
   * @return string
   */
  public function getDeadlineHour()
  {
    return $this->deadlineHour;
  }
  /**
   * Timezone identifier for the deadline hour (for example, "Europe/Zurich").
   * List of identifiers. Required.
   *
   * @param string $deadlineTimezone
   */
  public function setDeadlineTimezone($deadlineTimezone)
  {
    $this->deadlineTimezone = $deadlineTimezone;
  }
  /**
   * @return string
   */
  public function getDeadlineTimezone()
  {
    return $this->deadlineTimezone;
  }
  /**
   * Unique identifier for the holiday. Required.
   *
   * @param string $holidayId
   */
  public function setHolidayId($holidayId)
  {
    $this->holidayId = $holidayId;
  }
  /**
   * @return string
   */
  public function getHolidayId()
  {
    return $this->holidayId;
  }
  /**
   * Date on which the deadline will become visible to consumers in ISO 8601
   * format. For example, "2016-10-31" for 31st October 2016. Required.
   *
   * @param string $visibleFromDate
   */
  public function setVisibleFromDate($visibleFromDate)
  {
    $this->visibleFromDate = $visibleFromDate;
  }
  /**
   * @return string
   */
  public function getVisibleFromDate()
  {
    return $this->visibleFromDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HolidayCutoff::class, 'Google_Service_ShoppingContent_HolidayCutoff');
