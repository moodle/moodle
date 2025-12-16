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

namespace Google\Service\Sheets;

class NumberFormat extends \Google\Model
{
  /**
   * The number format is not specified and is based on the contents of the
   * cell. Do not explicitly use this.
   */
  public const TYPE_NUMBER_FORMAT_TYPE_UNSPECIFIED = 'NUMBER_FORMAT_TYPE_UNSPECIFIED';
  /**
   * Text formatting, e.g `1000.12`
   */
  public const TYPE_TEXT = 'TEXT';
  /**
   * Number formatting, e.g, `1,000.12`
   */
  public const TYPE_NUMBER = 'NUMBER';
  /**
   * Percent formatting, e.g `10.12%`
   */
  public const TYPE_PERCENT = 'PERCENT';
  /**
   * Currency formatting, e.g `$1,000.12`
   */
  public const TYPE_CURRENCY = 'CURRENCY';
  /**
   * Date formatting, e.g `9/26/2008`
   */
  public const TYPE_DATE = 'DATE';
  /**
   * Time formatting, e.g `3:59:00 PM`
   */
  public const TYPE_TIME = 'TIME';
  /**
   * Date+Time formatting, e.g `9/26/08 15:59:00`
   */
  public const TYPE_DATE_TIME = 'DATE_TIME';
  /**
   * Scientific number formatting, e.g `1.01E+03`
   */
  public const TYPE_SCIENTIFIC = 'SCIENTIFIC';
  /**
   * Pattern string used for formatting. If not set, a default pattern based on
   * the spreadsheet's locale will be used if necessary for the given type. See
   * the [Date and Number Formats
   * guide](https://developers.google.com/workspace/sheets/api/guides/formats)
   * for more information about the supported patterns.
   *
   * @var string
   */
  public $pattern;
  /**
   * The type of the number format. When writing, this field must be set.
   *
   * @var string
   */
  public $type;

  /**
   * Pattern string used for formatting. If not set, a default pattern based on
   * the spreadsheet's locale will be used if necessary for the given type. See
   * the [Date and Number Formats
   * guide](https://developers.google.com/workspace/sheets/api/guides/formats)
   * for more information about the supported patterns.
   *
   * @param string $pattern
   */
  public function setPattern($pattern)
  {
    $this->pattern = $pattern;
  }
  /**
   * @return string
   */
  public function getPattern()
  {
    return $this->pattern;
  }
  /**
   * The type of the number format. When writing, this field must be set.
   *
   * Accepted values: NUMBER_FORMAT_TYPE_UNSPECIFIED, TEXT, NUMBER, PERCENT,
   * CURRENCY, DATE, TIME, DATE_TIME, SCIENTIFIC
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NumberFormat::class, 'Google_Service_Sheets_NumberFormat');
