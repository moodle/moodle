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

class LineItemFlight extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const FLIGHT_DATE_TYPE_LINE_ITEM_FLIGHT_DATE_TYPE_UNSPECIFIED = 'LINE_ITEM_FLIGHT_DATE_TYPE_UNSPECIFIED';
  /**
   * The line item's flight dates are inherited from its parent insertion order.
   */
  public const FLIGHT_DATE_TYPE_LINE_ITEM_FLIGHT_DATE_TYPE_INHERITED = 'LINE_ITEM_FLIGHT_DATE_TYPE_INHERITED';
  /**
   * The line item uses its own custom flight dates.
   */
  public const FLIGHT_DATE_TYPE_LINE_ITEM_FLIGHT_DATE_TYPE_CUSTOM = 'LINE_ITEM_FLIGHT_DATE_TYPE_CUSTOM';
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  /**
   * Required. The type of the line item's flight dates.
   *
   * @var string
   */
  public $flightDateType;

  /**
   * The flight start and end dates of the line item. They are resolved relative
   * to the parent advertiser's time zone. * Required when flight_date_type is
   * `LINE_ITEM_FLIGHT_DATE_TYPE_CUSTOM`. Output only otherwise. * When creating
   * a new flight, both `start_date` and `end_date` must be in the future. * An
   * existing flight with a `start_date` in the past has a mutable `end_date`
   * but an immutable `start_date`. * `end_date` must be the `start_date` or
   * later, both before the year 2037.
   *
   * @param DateRange $dateRange
   */
  public function setDateRange(DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }
  /**
   * @return DateRange
   */
  public function getDateRange()
  {
    return $this->dateRange;
  }
  /**
   * Required. The type of the line item's flight dates.
   *
   * Accepted values: LINE_ITEM_FLIGHT_DATE_TYPE_UNSPECIFIED,
   * LINE_ITEM_FLIGHT_DATE_TYPE_INHERITED, LINE_ITEM_FLIGHT_DATE_TYPE_CUSTOM
   *
   * @param self::FLIGHT_DATE_TYPE_* $flightDateType
   */
  public function setFlightDateType($flightDateType)
  {
    $this->flightDateType = $flightDateType;
  }
  /**
   * @return self::FLIGHT_DATE_TYPE_*
   */
  public function getFlightDateType()
  {
    return $this->flightDateType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LineItemFlight::class, 'Google_Service_DisplayVideo_LineItemFlight');
