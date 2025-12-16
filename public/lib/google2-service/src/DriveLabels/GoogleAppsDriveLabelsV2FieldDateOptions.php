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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2FieldDateOptions extends \Google\Model
{
  /**
   * Date format unspecified.
   */
  public const DATE_FORMAT_TYPE_DATE_FORMAT_UNSPECIFIED = 'DATE_FORMAT_UNSPECIFIED';
  /**
   * Includes full month name. For example, January 12, 1999 (MMMM d, y)
   */
  public const DATE_FORMAT_TYPE_LONG_DATE = 'LONG_DATE';
  /**
   * Short, numeric, representation. For example, 12/13/99 (M/d/yy)
   */
  public const DATE_FORMAT_TYPE_SHORT_DATE = 'SHORT_DATE';
  /**
   * Output only. ICU date format.
   *
   * @var string
   */
  public $dateFormat;
  /**
   * Localized date formatting option. Field values are rendered in this format
   * according to their locale.
   *
   * @var string
   */
  public $dateFormatType;
  protected $maxValueType = GoogleTypeDate::class;
  protected $maxValueDataType = '';
  protected $minValueType = GoogleTypeDate::class;
  protected $minValueDataType = '';

  /**
   * Output only. ICU date format.
   *
   * @param string $dateFormat
   */
  public function setDateFormat($dateFormat)
  {
    $this->dateFormat = $dateFormat;
  }
  /**
   * @return string
   */
  public function getDateFormat()
  {
    return $this->dateFormat;
  }
  /**
   * Localized date formatting option. Field values are rendered in this format
   * according to their locale.
   *
   * Accepted values: DATE_FORMAT_UNSPECIFIED, LONG_DATE, SHORT_DATE
   *
   * @param self::DATE_FORMAT_TYPE_* $dateFormatType
   */
  public function setDateFormatType($dateFormatType)
  {
    $this->dateFormatType = $dateFormatType;
  }
  /**
   * @return self::DATE_FORMAT_TYPE_*
   */
  public function getDateFormatType()
  {
    return $this->dateFormatType;
  }
  /**
   * Output only. Maximum valid value (year, month, day).
   *
   * @param GoogleTypeDate $maxValue
   */
  public function setMaxValue(GoogleTypeDate $maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * Output only. Minimum valid value (year, month, day).
   *
   * @param GoogleTypeDate $minValue
   */
  public function setMinValue(GoogleTypeDate $minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2FieldDateOptions::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2FieldDateOptions');
