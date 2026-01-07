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

class HolidaysHoliday extends \Google\Model
{
  /**
   * The CLDR territory code of the country in which the holiday is available.
   * For example, "US", "DE", "GB". A holiday cutoff can only be configured in a
   * shipping settings service with matching delivery country. Always present.
   *
   * @var string
   */
  public $countryCode;
  /**
   * Date of the holiday, in ISO 8601 format. For example, "2016-12-25" for
   * Christmas 2016. Always present.
   *
   * @var string
   */
  public $date;
  /**
   * Date on which the order has to arrive at the customer's, in ISO 8601
   * format. For example, "2016-12-24" for 24th December 2016. Always present.
   *
   * @var string
   */
  public $deliveryGuaranteeDate;
  /**
   * Hour of the day in the delivery location's timezone on the guaranteed
   * delivery date by which the order has to arrive at the customer's. Possible
   * values are: 0 (midnight), 1, ..., 12 (noon), 13, ..., 23. Always present.
   *
   * @var string
   */
  public $deliveryGuaranteeHour;
  /**
   * Unique identifier for the holiday to be used when configuring holiday
   * cutoffs. Always present.
   *
   * @var string
   */
  public $id;
  /**
   * The holiday type. Always present. Acceptable values are: - "`Christmas`" -
   * "`Easter`" - "`Father's Day`" - "`Halloween`" - "`Independence Day (USA)`"
   * - "`Mother's Day`" - "`Thanksgiving`" - "`Valentine's Day`"
   *
   * @var string
   */
  public $type;

  /**
   * The CLDR territory code of the country in which the holiday is available.
   * For example, "US", "DE", "GB". A holiday cutoff can only be configured in a
   * shipping settings service with matching delivery country. Always present.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * Date of the holiday, in ISO 8601 format. For example, "2016-12-25" for
   * Christmas 2016. Always present.
   *
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * Date on which the order has to arrive at the customer's, in ISO 8601
   * format. For example, "2016-12-24" for 24th December 2016. Always present.
   *
   * @param string $deliveryGuaranteeDate
   */
  public function setDeliveryGuaranteeDate($deliveryGuaranteeDate)
  {
    $this->deliveryGuaranteeDate = $deliveryGuaranteeDate;
  }
  /**
   * @return string
   */
  public function getDeliveryGuaranteeDate()
  {
    return $this->deliveryGuaranteeDate;
  }
  /**
   * Hour of the day in the delivery location's timezone on the guaranteed
   * delivery date by which the order has to arrive at the customer's. Possible
   * values are: 0 (midnight), 1, ..., 12 (noon), 13, ..., 23. Always present.
   *
   * @param string $deliveryGuaranteeHour
   */
  public function setDeliveryGuaranteeHour($deliveryGuaranteeHour)
  {
    $this->deliveryGuaranteeHour = $deliveryGuaranteeHour;
  }
  /**
   * @return string
   */
  public function getDeliveryGuaranteeHour()
  {
    return $this->deliveryGuaranteeHour;
  }
  /**
   * Unique identifier for the holiday to be used when configuring holiday
   * cutoffs. Always present.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The holiday type. Always present. Acceptable values are: - "`Christmas`" -
   * "`Easter`" - "`Father's Day`" - "`Halloween`" - "`Independence Day (USA)`"
   * - "`Mother's Day`" - "`Thanksgiving`" - "`Valentine's Day`"
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HolidaysHoliday::class, 'Google_Service_ShoppingContent_HolidaysHoliday');
