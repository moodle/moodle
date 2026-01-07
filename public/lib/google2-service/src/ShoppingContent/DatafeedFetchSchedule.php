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

class DatafeedFetchSchedule extends \Google\Model
{
  /**
   * The day of the month the feed file should be fetched (1-31).
   *
   * @var string
   */
  public $dayOfMonth;
  /**
   * The URL where the feed file can be fetched. Google Merchant Center will
   * support automatic scheduled uploads using the HTTP, HTTPS, FTP, or SFTP
   * protocols, so the value will need to be a valid link using one of those
   * four protocols.
   *
   * @var string
   */
  public $fetchUrl;
  /**
   * The hour of the day the feed file should be fetched (0-23).
   *
   * @var string
   */
  public $hour;
  /**
   * The minute of the hour the feed file should be fetched (0-59). Read-only.
   *
   * @var string
   */
  public $minuteOfHour;
  /**
   * An optional password for fetch_url.
   *
   * @var string
   */
  public $password;
  /**
   * Whether the scheduled fetch is paused or not.
   *
   * @var bool
   */
  public $paused;
  /**
   * Time zone used for schedule. UTC by default. For example,
   * "America/Los_Angeles".
   *
   * @var string
   */
  public $timeZone;
  /**
   * An optional user name for fetch_url.
   *
   * @var string
   */
  public $username;
  /**
   * The day of the week the feed file should be fetched. Acceptable values are:
   * - "`monday`" - "`tuesday`" - "`wednesday`" - "`thursday`" - "`friday`" -
   * "`saturday`" - "`sunday`"
   *
   * @var string
   */
  public $weekday;

  /**
   * The day of the month the feed file should be fetched (1-31).
   *
   * @param string $dayOfMonth
   */
  public function setDayOfMonth($dayOfMonth)
  {
    $this->dayOfMonth = $dayOfMonth;
  }
  /**
   * @return string
   */
  public function getDayOfMonth()
  {
    return $this->dayOfMonth;
  }
  /**
   * The URL where the feed file can be fetched. Google Merchant Center will
   * support automatic scheduled uploads using the HTTP, HTTPS, FTP, or SFTP
   * protocols, so the value will need to be a valid link using one of those
   * four protocols.
   *
   * @param string $fetchUrl
   */
  public function setFetchUrl($fetchUrl)
  {
    $this->fetchUrl = $fetchUrl;
  }
  /**
   * @return string
   */
  public function getFetchUrl()
  {
    return $this->fetchUrl;
  }
  /**
   * The hour of the day the feed file should be fetched (0-23).
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
   * The minute of the hour the feed file should be fetched (0-59). Read-only.
   *
   * @param string $minuteOfHour
   */
  public function setMinuteOfHour($minuteOfHour)
  {
    $this->minuteOfHour = $minuteOfHour;
  }
  /**
   * @return string
   */
  public function getMinuteOfHour()
  {
    return $this->minuteOfHour;
  }
  /**
   * An optional password for fetch_url.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Whether the scheduled fetch is paused or not.
   *
   * @param bool $paused
   */
  public function setPaused($paused)
  {
    $this->paused = $paused;
  }
  /**
   * @return bool
   */
  public function getPaused()
  {
    return $this->paused;
  }
  /**
   * Time zone used for schedule. UTC by default. For example,
   * "America/Los_Angeles".
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
  /**
   * An optional user name for fetch_url.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
  /**
   * The day of the week the feed file should be fetched. Acceptable values are:
   * - "`monday`" - "`tuesday`" - "`wednesday`" - "`thursday`" - "`friday`" -
   * "`saturday`" - "`sunday`"
   *
   * @param string $weekday
   */
  public function setWeekday($weekday)
  {
    $this->weekday = $weekday;
  }
  /**
   * @return string
   */
  public function getWeekday()
  {
    return $this->weekday;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatafeedFetchSchedule::class, 'Google_Service_ShoppingContent_DatafeedFetchSchedule');
