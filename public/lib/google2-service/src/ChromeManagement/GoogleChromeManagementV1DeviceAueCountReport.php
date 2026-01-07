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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1DeviceAueCountReport extends \Google\Model
{
  /**
   * The unspecified month.
   */
  public const AUE_MONTH_MONTH_UNSPECIFIED = 'MONTH_UNSPECIFIED';
  /**
   * The month of January.
   */
  public const AUE_MONTH_JANUARY = 'JANUARY';
  /**
   * The month of February.
   */
  public const AUE_MONTH_FEBRUARY = 'FEBRUARY';
  /**
   * The month of March.
   */
  public const AUE_MONTH_MARCH = 'MARCH';
  /**
   * The month of April.
   */
  public const AUE_MONTH_APRIL = 'APRIL';
  /**
   * The month of May.
   */
  public const AUE_MONTH_MAY = 'MAY';
  /**
   * The month of June.
   */
  public const AUE_MONTH_JUNE = 'JUNE';
  /**
   * The month of July.
   */
  public const AUE_MONTH_JULY = 'JULY';
  /**
   * The month of August.
   */
  public const AUE_MONTH_AUGUST = 'AUGUST';
  /**
   * The month of September.
   */
  public const AUE_MONTH_SEPTEMBER = 'SEPTEMBER';
  /**
   * The month of October.
   */
  public const AUE_MONTH_OCTOBER = 'OCTOBER';
  /**
   * The month of November.
   */
  public const AUE_MONTH_NOVEMBER = 'NOVEMBER';
  /**
   * The month of December.
   */
  public const AUE_MONTH_DECEMBER = 'DECEMBER';
  /**
   * Enum value of month corresponding to the auto update expiration date in UTC
   * time zone. If the device is already expired, this field is empty.
   *
   * @var string
   */
  public $aueMonth;
  /**
   * Int value of year corresponding to the Auto Update Expiration date in UTC
   * time zone. If the device is already expired, this field is empty.
   *
   * @var string
   */
  public $aueYear;
  /**
   * Count of devices of this model.
   *
   * @var string
   */
  public $count;
  /**
   * Boolean value for whether or not the device has already expired.
   *
   * @var bool
   */
  public $expired;
  /**
   * Public model name of the devices.
   *
   * @var string
   */
  public $model;

  /**
   * Enum value of month corresponding to the auto update expiration date in UTC
   * time zone. If the device is already expired, this field is empty.
   *
   * Accepted values: MONTH_UNSPECIFIED, JANUARY, FEBRUARY, MARCH, APRIL, MAY,
   * JUNE, JULY, AUGUST, SEPTEMBER, OCTOBER, NOVEMBER, DECEMBER
   *
   * @param self::AUE_MONTH_* $aueMonth
   */
  public function setAueMonth($aueMonth)
  {
    $this->aueMonth = $aueMonth;
  }
  /**
   * @return self::AUE_MONTH_*
   */
  public function getAueMonth()
  {
    return $this->aueMonth;
  }
  /**
   * Int value of year corresponding to the Auto Update Expiration date in UTC
   * time zone. If the device is already expired, this field is empty.
   *
   * @param string $aueYear
   */
  public function setAueYear($aueYear)
  {
    $this->aueYear = $aueYear;
  }
  /**
   * @return string
   */
  public function getAueYear()
  {
    return $this->aueYear;
  }
  /**
   * Count of devices of this model.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Boolean value for whether or not the device has already expired.
   *
   * @param bool $expired
   */
  public function setExpired($expired)
  {
    $this->expired = $expired;
  }
  /**
   * @return bool
   */
  public function getExpired()
  {
    return $this->expired;
  }
  /**
   * Public model name of the devices.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1DeviceAueCountReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1DeviceAueCountReport');
