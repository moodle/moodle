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

class BatchGetValuesByDataFilterRequest extends \Google\Collection
{
  /**
   * Instructs date, time, datetime, and duration fields to be output as doubles
   * in "serial number" format, as popularized by Lotus 1-2-3. The whole number
   * portion of the value (left of the decimal) counts the days since December
   * 30th 1899. The fractional portion (right of the decimal) counts the time as
   * a fraction of the day. For example, January 1st 1900 at noon would be 2.5,
   * 2 because it's 2 days after December 30th 1899, and .5 because noon is half
   * a day. February 1st 1900 at 3pm would be 33.625. This correctly treats the
   * year 1900 as not a leap year.
   */
  public const DATE_TIME_RENDER_OPTION_SERIAL_NUMBER = 'SERIAL_NUMBER';
  /**
   * Instructs date, time, datetime, and duration fields to be output as strings
   * in their given number format (which depends on the spreadsheet locale).
   */
  public const DATE_TIME_RENDER_OPTION_FORMATTED_STRING = 'FORMATTED_STRING';
  /**
   * The default value, do not use.
   */
  public const MAJOR_DIMENSION_DIMENSION_UNSPECIFIED = 'DIMENSION_UNSPECIFIED';
  /**
   * Operates on the rows of a sheet.
   */
  public const MAJOR_DIMENSION_ROWS = 'ROWS';
  /**
   * Operates on the columns of a sheet.
   */
  public const MAJOR_DIMENSION_COLUMNS = 'COLUMNS';
  /**
   * Values will be calculated & formatted in the response according to the
   * cell's formatting. Formatting is based on the spreadsheet's locale, not the
   * requesting user's locale. For example, if `A1` is `1.23` and `A2` is `=A1`
   * and formatted as currency, then `A2` would return `"$1.23"`.
   */
  public const VALUE_RENDER_OPTION_FORMATTED_VALUE = 'FORMATTED_VALUE';
  /**
   * Values will be calculated, but not formatted in the reply. For example, if
   * `A1` is `1.23` and `A2` is `=A1` and formatted as currency, then `A2` would
   * return the number `1.23`.
   */
  public const VALUE_RENDER_OPTION_UNFORMATTED_VALUE = 'UNFORMATTED_VALUE';
  /**
   * Values will not be calculated. The reply will include the formulas. For
   * example, if `A1` is `1.23` and `A2` is `=A1` and formatted as currency,
   * then A2 would return `"=A1"`. Sheets treats date and time values as decimal
   * values. This lets you perform arithmetic on them in formulas. For more
   * information on interpreting date and time values, see [About date & time va
   * lues](https://developers.google.com/workspace/sheets/api/guides/formats#abo
   * ut_date_time_values).
   */
  public const VALUE_RENDER_OPTION_FORMULA = 'FORMULA';
  protected $collection_key = 'dataFilters';
  protected $dataFiltersType = DataFilter::class;
  protected $dataFiltersDataType = 'array';
  /**
   * How dates, times, and durations should be represented in the output. This
   * is ignored if value_render_option is FORMATTED_VALUE. The default dateTime
   * render option is SERIAL_NUMBER.
   *
   * @var string
   */
  public $dateTimeRenderOption;
  /**
   * The major dimension that results should use. For example, if the
   * spreadsheet data is: `A1=1,B1=2,A2=3,B2=4`, then a request that selects
   * that range and sets `majorDimension=ROWS` returns `[[1,2],[3,4]]`, whereas
   * a request that sets `majorDimension=COLUMNS` returns `[[1,3],[2,4]]`.
   *
   * @var string
   */
  public $majorDimension;
  /**
   * How values should be represented in the output. The default render option
   * is FORMATTED_VALUE.
   *
   * @var string
   */
  public $valueRenderOption;

  /**
   * The data filters used to match the ranges of values to retrieve. Ranges
   * that match any of the specified data filters are included in the response.
   *
   * @param DataFilter[] $dataFilters
   */
  public function setDataFilters($dataFilters)
  {
    $this->dataFilters = $dataFilters;
  }
  /**
   * @return DataFilter[]
   */
  public function getDataFilters()
  {
    return $this->dataFilters;
  }
  /**
   * How dates, times, and durations should be represented in the output. This
   * is ignored if value_render_option is FORMATTED_VALUE. The default dateTime
   * render option is SERIAL_NUMBER.
   *
   * Accepted values: SERIAL_NUMBER, FORMATTED_STRING
   *
   * @param self::DATE_TIME_RENDER_OPTION_* $dateTimeRenderOption
   */
  public function setDateTimeRenderOption($dateTimeRenderOption)
  {
    $this->dateTimeRenderOption = $dateTimeRenderOption;
  }
  /**
   * @return self::DATE_TIME_RENDER_OPTION_*
   */
  public function getDateTimeRenderOption()
  {
    return $this->dateTimeRenderOption;
  }
  /**
   * The major dimension that results should use. For example, if the
   * spreadsheet data is: `A1=1,B1=2,A2=3,B2=4`, then a request that selects
   * that range and sets `majorDimension=ROWS` returns `[[1,2],[3,4]]`, whereas
   * a request that sets `majorDimension=COLUMNS` returns `[[1,3],[2,4]]`.
   *
   * Accepted values: DIMENSION_UNSPECIFIED, ROWS, COLUMNS
   *
   * @param self::MAJOR_DIMENSION_* $majorDimension
   */
  public function setMajorDimension($majorDimension)
  {
    $this->majorDimension = $majorDimension;
  }
  /**
   * @return self::MAJOR_DIMENSION_*
   */
  public function getMajorDimension()
  {
    return $this->majorDimension;
  }
  /**
   * How values should be represented in the output. The default render option
   * is FORMATTED_VALUE.
   *
   * Accepted values: FORMATTED_VALUE, UNFORMATTED_VALUE, FORMULA
   *
   * @param self::VALUE_RENDER_OPTION_* $valueRenderOption
   */
  public function setValueRenderOption($valueRenderOption)
  {
    $this->valueRenderOption = $valueRenderOption;
  }
  /**
   * @return self::VALUE_RENDER_OPTION_*
   */
  public function getValueRenderOption()
  {
    return $this->valueRenderOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchGetValuesByDataFilterRequest::class, 'Google_Service_Sheets_BatchGetValuesByDataFilterRequest');
