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

namespace Google\Service\Dfareporting;

class TvCampaignTimepoint extends \Google\Model
{
  /**
   * Default value, should never be set.
   */
  public const DATE_WINDOW_WEEKS_UNSPECIFIED = 'WEEKS_UNSPECIFIED';
  /**
   * One week.
   */
  public const DATE_WINDOW_WEEKS_ONE = 'WEEKS_ONE';
  /**
   * Four weeks.
   */
  public const DATE_WINDOW_WEEKS_FOUR = 'WEEKS_FOUR';
  /**
   * Eight weeks.
   */
  public const DATE_WINDOW_WEEKS_EIGHT = 'WEEKS_EIGHT';
  /**
   * Twelve weeks.
   */
  public const DATE_WINDOW_WEEKS_TWELVE = 'WEEKS_TWELVE';
  /**
   * The date window of the timepoint.
   *
   * @var string
   */
  public $dateWindow;
  /**
   * The spend within the time range of the timepoint.
   *
   * @var 
   */
  public $spend;
  /**
   * The start date of the timepoint. A string in the format of "yyyy-MM-dd".
   *
   * @var string
   */
  public $startDate;

  /**
   * The date window of the timepoint.
   *
   * Accepted values: WEEKS_UNSPECIFIED, WEEKS_ONE, WEEKS_FOUR, WEEKS_EIGHT,
   * WEEKS_TWELVE
   *
   * @param self::DATE_WINDOW_* $dateWindow
   */
  public function setDateWindow($dateWindow)
  {
    $this->dateWindow = $dateWindow;
  }
  /**
   * @return self::DATE_WINDOW_*
   */
  public function getDateWindow()
  {
    return $this->dateWindow;
  }
  public function setSpend($spend)
  {
    $this->spend = $spend;
  }
  public function getSpend()
  {
    return $this->spend;
  }
  /**
   * The start date of the timepoint. A string in the format of "yyyy-MM-dd".
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TvCampaignTimepoint::class, 'Google_Service_Dfareporting_TvCampaignTimepoint');
