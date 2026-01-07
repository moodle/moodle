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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1ReportValue extends \Google\Model
{
  protected $dateTimeValueType = GoogleTypeDateTime::class;
  protected $dateTimeValueDataType = '';
  protected $dateValueType = GoogleTypeDate::class;
  protected $dateValueDataType = '';
  protected $decimalValueType = GoogleTypeDecimal::class;
  protected $decimalValueDataType = '';
  /**
   * A value of type `int`.
   *
   * @var string
   */
  public $intValue;
  protected $moneyValueType = GoogleTypeMoney::class;
  protected $moneyValueDataType = '';
  /**
   * A value of type `string`.
   *
   * @var string
   */
  public $stringValue;

  /**
   * A value of type `google.type.DateTime` (year, month, day, hour, minute,
   * second, and UTC offset or timezone.)
   *
   * @param GoogleTypeDateTime $dateTimeValue
   */
  public function setDateTimeValue(GoogleTypeDateTime $dateTimeValue)
  {
    $this->dateTimeValue = $dateTimeValue;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getDateTimeValue()
  {
    return $this->dateTimeValue;
  }
  /**
   * A value of type `google.type.Date` (year, month, day).
   *
   * @param GoogleTypeDate $dateValue
   */
  public function setDateValue(GoogleTypeDate $dateValue)
  {
    $this->dateValue = $dateValue;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getDateValue()
  {
    return $this->dateValue;
  }
  /**
   * A value of type `google.type.Decimal`, representing non-integer numeric
   * values.
   *
   * @param GoogleTypeDecimal $decimalValue
   */
  public function setDecimalValue(GoogleTypeDecimal $decimalValue)
  {
    $this->decimalValue = $decimalValue;
  }
  /**
   * @return GoogleTypeDecimal
   */
  public function getDecimalValue()
  {
    return $this->decimalValue;
  }
  /**
   * A value of type `int`.
   *
   * @param string $intValue
   */
  public function setIntValue($intValue)
  {
    $this->intValue = $intValue;
  }
  /**
   * @return string
   */
  public function getIntValue()
  {
    return $this->intValue;
  }
  /**
   * A value of type `google.type.Money` (currency code, whole units, decimal
   * units).
   *
   * @param GoogleTypeMoney $moneyValue
   */
  public function setMoneyValue(GoogleTypeMoney $moneyValue)
  {
    $this->moneyValue = $moneyValue;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getMoneyValue()
  {
    return $this->moneyValue;
  }
  /**
   * A value of type `string`.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1ReportValue::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ReportValue');
