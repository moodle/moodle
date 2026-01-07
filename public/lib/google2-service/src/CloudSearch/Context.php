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

namespace Google\Service\CloudSearch;

class Context extends \Google\Collection
{
  protected $collection_key = 'type';
  /**
   * [Optional] App where the card should be shown. If missing, the card will be
   * shown in TOPAZ.
   *
   * @var string[]
   */
  public $app;
  /**
   * [Optional] Day of week when the card should be shown, where 0 is Monday.
   *
   * @var int[]
   */
  public $dayOfWeek;
  /**
   * [Optional] Date (in seconds since epoch) when the card should stop being
   * shown. If missing, end_date_sec will be set to Jan 1st, 2100.
   *
   * @var string
   */
  public $endDateSec;
  /**
   * [Optional] End time in seconds, within a day, when the card should stop
   * being shown if it's within [start_date_sec, end_date_sec]. If missing, this
   * is set to 86400 (24 hours x 3600 sec/hour), i.e., midnight next day.
   *
   * @var string
   */
  public $endDayOffsetSec;
  /**
   * [Optional] The locales for which the card should be triggered (e.g., en_US
   * and en_CA). If missing, the card is going to show to clients regardless of
   * their locale.
   *
   * @var string[]
   */
  public $locale;
  /**
   * [Optional] Text-free locations where the card should be shown. This is
   * expected to match the user's location in focus. If no location is
   * specified, the card will be shown for any location.
   *
   * @var string[]
   */
  public $location;
  /**
   * [Required only for Answer and RHS cards - will be ignored for Homepage]
   * cards. It's the exact case-insensitive queries that will trigger the Answer
   * or RHS card.
   *
   * @var string[]
   */
  public $query;
  /**
   * [Optional] Date (in seconds since epoch) when the card should start being
   * shown. If missing, start_date_sec will be Jan 1st, 1970 UTC.
   *
   * @var string
   */
  public $startDateSec;
  /**
   * [Optional] Start time in seconds, within a day, when the card should be
   * shown if it's within [start_date_sec, end_date_sec]. If 0, the card will be
   * shown from 12:00am on.
   *
   * @var string
   */
  public $startDayOffsetSec;
  /**
   * [Optional] Surface where the card should be shown in. If missing, the card
   * will be shown in any surface.
   *
   * @var string[]
   */
  public $surface;
  /**
   * [Required] Type of the card (homepage, Answer or RHS).
   *
   * @var string[]
   */
  public $type;

  /**
   * [Optional] App where the card should be shown. If missing, the card will be
   * shown in TOPAZ.
   *
   * @param string[] $app
   */
  public function setApp($app)
  {
    $this->app = $app;
  }
  /**
   * @return string[]
   */
  public function getApp()
  {
    return $this->app;
  }
  /**
   * [Optional] Day of week when the card should be shown, where 0 is Monday.
   *
   * @param int[] $dayOfWeek
   */
  public function setDayOfWeek($dayOfWeek)
  {
    $this->dayOfWeek = $dayOfWeek;
  }
  /**
   * @return int[]
   */
  public function getDayOfWeek()
  {
    return $this->dayOfWeek;
  }
  /**
   * [Optional] Date (in seconds since epoch) when the card should stop being
   * shown. If missing, end_date_sec will be set to Jan 1st, 2100.
   *
   * @param string $endDateSec
   */
  public function setEndDateSec($endDateSec)
  {
    $this->endDateSec = $endDateSec;
  }
  /**
   * @return string
   */
  public function getEndDateSec()
  {
    return $this->endDateSec;
  }
  /**
   * [Optional] End time in seconds, within a day, when the card should stop
   * being shown if it's within [start_date_sec, end_date_sec]. If missing, this
   * is set to 86400 (24 hours x 3600 sec/hour), i.e., midnight next day.
   *
   * @param string $endDayOffsetSec
   */
  public function setEndDayOffsetSec($endDayOffsetSec)
  {
    $this->endDayOffsetSec = $endDayOffsetSec;
  }
  /**
   * @return string
   */
  public function getEndDayOffsetSec()
  {
    return $this->endDayOffsetSec;
  }
  /**
   * [Optional] The locales for which the card should be triggered (e.g., en_US
   * and en_CA). If missing, the card is going to show to clients regardless of
   * their locale.
   *
   * @param string[] $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string[]
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * [Optional] Text-free locations where the card should be shown. This is
   * expected to match the user's location in focus. If no location is
   * specified, the card will be shown for any location.
   *
   * @param string[] $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string[]
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * [Required only for Answer and RHS cards - will be ignored for Homepage]
   * cards. It's the exact case-insensitive queries that will trigger the Answer
   * or RHS card.
   *
   * @param string[] $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string[]
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * [Optional] Date (in seconds since epoch) when the card should start being
   * shown. If missing, start_date_sec will be Jan 1st, 1970 UTC.
   *
   * @param string $startDateSec
   */
  public function setStartDateSec($startDateSec)
  {
    $this->startDateSec = $startDateSec;
  }
  /**
   * @return string
   */
  public function getStartDateSec()
  {
    return $this->startDateSec;
  }
  /**
   * [Optional] Start time in seconds, within a day, when the card should be
   * shown if it's within [start_date_sec, end_date_sec]. If 0, the card will be
   * shown from 12:00am on.
   *
   * @param string $startDayOffsetSec
   */
  public function setStartDayOffsetSec($startDayOffsetSec)
  {
    $this->startDayOffsetSec = $startDayOffsetSec;
  }
  /**
   * @return string
   */
  public function getStartDayOffsetSec()
  {
    return $this->startDayOffsetSec;
  }
  /**
   * [Optional] Surface where the card should be shown in. If missing, the card
   * will be shown in any surface.
   *
   * @param string[] $surface
   */
  public function setSurface($surface)
  {
    $this->surface = $surface;
  }
  /**
   * @return string[]
   */
  public function getSurface()
  {
    return $this->surface;
  }
  /**
   * [Required] Type of the card (homepage, Answer or RHS).
   *
   * @param string[] $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string[]
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Context::class, 'Google_Service_CloudSearch_Context');
