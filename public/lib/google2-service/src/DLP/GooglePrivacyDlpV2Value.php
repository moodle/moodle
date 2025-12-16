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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Value extends \Google\Model
{
  /**
   * The day of the week is unspecified.
   */
  public const DAY_OF_WEEK_VALUE_DAY_OF_WEEK_UNSPECIFIED = 'DAY_OF_WEEK_UNSPECIFIED';
  /**
   * Monday
   */
  public const DAY_OF_WEEK_VALUE_MONDAY = 'MONDAY';
  /**
   * Tuesday
   */
  public const DAY_OF_WEEK_VALUE_TUESDAY = 'TUESDAY';
  /**
   * Wednesday
   */
  public const DAY_OF_WEEK_VALUE_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday
   */
  public const DAY_OF_WEEK_VALUE_THURSDAY = 'THURSDAY';
  /**
   * Friday
   */
  public const DAY_OF_WEEK_VALUE_FRIDAY = 'FRIDAY';
  /**
   * Saturday
   */
  public const DAY_OF_WEEK_VALUE_SATURDAY = 'SATURDAY';
  /**
   * Sunday
   */
  public const DAY_OF_WEEK_VALUE_SUNDAY = 'SUNDAY';
  /**
   * boolean
   *
   * @var bool
   */
  public $booleanValue;
  protected $dateValueType = GoogleTypeDate::class;
  protected $dateValueDataType = '';
  /**
   * day of week
   *
   * @var string
   */
  public $dayOfWeekValue;
  /**
   * float
   *
   * @var 
   */
  public $floatValue;
  /**
   * integer
   *
   * @var string
   */
  public $integerValue;
  /**
   * string
   *
   * @var string
   */
  public $stringValue;
  protected $timeValueType = GoogleTypeTimeOfDay::class;
  protected $timeValueDataType = '';
  /**
   * timestamp
   *
   * @var string
   */
  public $timestampValue;

  /**
   * boolean
   *
   * @param bool $booleanValue
   */
  public function setBooleanValue($booleanValue)
  {
    $this->booleanValue = $booleanValue;
  }
  /**
   * @return bool
   */
  public function getBooleanValue()
  {
    return $this->booleanValue;
  }
  /**
   * date
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
   * day of week
   *
   * Accepted values: DAY_OF_WEEK_UNSPECIFIED, MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY
   *
   * @param self::DAY_OF_WEEK_VALUE_* $dayOfWeekValue
   */
  public function setDayOfWeekValue($dayOfWeekValue)
  {
    $this->dayOfWeekValue = $dayOfWeekValue;
  }
  /**
   * @return self::DAY_OF_WEEK_VALUE_*
   */
  public function getDayOfWeekValue()
  {
    return $this->dayOfWeekValue;
  }
  public function setFloatValue($floatValue)
  {
    $this->floatValue = $floatValue;
  }
  public function getFloatValue()
  {
    return $this->floatValue;
  }
  /**
   * integer
   *
   * @param string $integerValue
   */
  public function setIntegerValue($integerValue)
  {
    $this->integerValue = $integerValue;
  }
  /**
   * @return string
   */
  public function getIntegerValue()
  {
    return $this->integerValue;
  }
  /**
   * string
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
  /**
   * time of day
   *
   * @param GoogleTypeTimeOfDay $timeValue
   */
  public function setTimeValue(GoogleTypeTimeOfDay $timeValue)
  {
    $this->timeValue = $timeValue;
  }
  /**
   * @return GoogleTypeTimeOfDay
   */
  public function getTimeValue()
  {
    return $this->timeValue;
  }
  /**
   * timestamp
   *
   * @param string $timestampValue
   */
  public function setTimestampValue($timestampValue)
  {
    $this->timestampValue = $timestampValue;
  }
  /**
   * @return string
   */
  public function getTimestampValue()
  {
    return $this->timestampValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Value::class, 'Google_Service_DLP_GooglePrivacyDlpV2Value');
