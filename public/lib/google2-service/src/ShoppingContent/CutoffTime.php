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

class CutoffTime extends \Google\Model
{
  /**
   * Hour of the cutoff time until which an order has to be placed to be
   * processed in the same day. Required.
   *
   * @var string
   */
  public $hour;
  /**
   * Minute of the cutoff time until which an order has to be placed to be
   * processed in the same day. Required.
   *
   * @var string
   */
  public $minute;
  /**
   * Timezone identifier for the cutoff time (for example, "Europe/Zurich").
   * List of identifiers. Required.
   *
   * @var string
   */
  public $timezone;

  /**
   * Hour of the cutoff time until which an order has to be placed to be
   * processed in the same day. Required.
   *
   * @param string $hour
   */
  public function setHour($hour)
  {
    $this->hour = $hour;
  }
  /**
   * @return string
   */
  public function getHour()
  {
    return $this->hour;
  }
  /**
   * Minute of the cutoff time until which an order has to be placed to be
   * processed in the same day. Required.
   *
   * @param string $minute
   */
  public function setMinute($minute)
  {
    $this->minute = $minute;
  }
  /**
   * @return string
   */
  public function getMinute()
  {
    return $this->minute;
  }
  /**
   * Timezone identifier for the cutoff time (for example, "Europe/Zurich").
   * List of identifiers. Required.
   *
   * @param string $timezone
   */
  public function setTimezone($timezone)
  {
    $this->timezone = $timezone;
  }
  /**
   * @return string
   */
  public function getTimezone()
  {
    return $this->timezone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CutoffTime::class, 'Google_Service_ShoppingContent_CutoffTime');
