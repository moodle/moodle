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

namespace Google\Service\DisplayVideo;

class CampaignFlight extends \Google\Model
{
  protected $plannedDatesType = DateRange::class;
  protected $plannedDatesDataType = '';
  /**
   * The amount the campaign is expected to spend for its given planned_dates.
   * This will not limit serving, but will be used for tracking spend in the
   * DV360 UI. The amount is in micros. Must be greater than or equal to 0. For
   * example, 500000000 represents 500 standard units of the currency.
   *
   * @var string
   */
  public $plannedSpendAmountMicros;

  /**
   * Required. The dates that the campaign is expected to run. They are resolved
   * relative to the parent advertiser's time zone. * The dates specified here
   * will not affect serving. They are used to generate alerts and warnings. For
   * example, if the flight date of any child insertion order is outside the
   * range of these dates, the user interface will show a warning. *
   * `start_date` is required and must be the current date or later. *
   * `end_date` is optional. If specified, it must be the `start_date` or later.
   * * Any specified date must be before the year 2037.
   *
   * @param DateRange $plannedDates
   */
  public function setPlannedDates(DateRange $plannedDates)
  {
    $this->plannedDates = $plannedDates;
  }
  /**
   * @return DateRange
   */
  public function getPlannedDates()
  {
    return $this->plannedDates;
  }
  /**
   * The amount the campaign is expected to spend for its given planned_dates.
   * This will not limit serving, but will be used for tracking spend in the
   * DV360 UI. The amount is in micros. Must be greater than or equal to 0. For
   * example, 500000000 represents 500 standard units of the currency.
   *
   * @param string $plannedSpendAmountMicros
   */
  public function setPlannedSpendAmountMicros($plannedSpendAmountMicros)
  {
    $this->plannedSpendAmountMicros = $plannedSpendAmountMicros;
  }
  /**
   * @return string
   */
  public function getPlannedSpendAmountMicros()
  {
    return $this->plannedSpendAmountMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CampaignFlight::class, 'Google_Service_DisplayVideo_CampaignFlight');
