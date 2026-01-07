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

class BatchUpdateValuesByDataFilterRequest extends \Google\Collection
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
  public const RESPONSE_DATE_TIME_RENDER_OPTION_SERIAL_NUMBER = 'SERIAL_NUMBER';
  /**
   * Instructs date, time, datetime, and duration fields to be output as strings
   * in their given number format (which depends on the spreadsheet locale).
   */
  public const RESPONSE_DATE_TIME_RENDER_OPTION_FORMATTED_STRING = 'FORMATTED_STRING';
  /**
   * Values will be calculated & formatted in the response according to the
   * cell's formatting. Formatting is based on the spreadsheet's locale, not the
   * requesting user's locale. For example, if `A1` is `1.23` and `A2` is `=A1`
   * and formatted as currency, then `A2` would return `"$1.23"`.
   */
  public const RESPONSE_VALUE_RENDER_OPTION_FORMATTED_VALUE = 'FORMATTED_VALUE';
  /**
   * Values will be calculated, but not formatted in the reply. For example, if
   * `A1` is `1.23` and `A2` is `=A1` and formatted as currency, then `A2` would
   * return the number `1.23`.
   */
  public const RESPONSE_VALUE_RENDER_OPTION_UNFORMATTED_VALUE = 'UNFORMATTED_VALUE';
  /**
   * Values will not be calculated. The reply will include the formulas. For
   * example, if `A1` is `1.23` and `A2` is `=A1` and formatted as currency,
   * then A2 would return `"=A1"`. Sheets treats date and time values as decimal
   * values. This lets you perform arithmetic on them in formulas. For more
   * information on interpreting date and time values, see [About date & time va
   * lues](https://developers.google.com/workspace/sheets/api/guides/formats#abo
   * ut_date_time_values).
   */
  public const RESPONSE_VALUE_RENDER_OPTION_FORMULA = 'FORMULA';
  /**
   * Default input value. This value must not be used.
   */
  public const VALUE_INPUT_OPTION_INPUT_VALUE_OPTION_UNSPECIFIED = 'INPUT_VALUE_OPTION_UNSPECIFIED';
  /**
   * The values the user has entered will not be parsed and will be stored as-
   * is.
   */
  public const VALUE_INPUT_OPTION_RAW = 'RAW';
  /**
   * The values will be parsed as if the user typed them into the UI. Numbers
   * will stay as numbers, but strings may be converted to numbers, dates, etc.
   * following the same rules that are applied when entering text into a cell
   * via the Google Sheets UI.
   */
  public const VALUE_INPUT_OPTION_USER_ENTERED = 'USER_ENTERED';
  protected $collection_key = 'data';
  protected $dataType = DataFilterValueRange::class;
  protected $dataDataType = 'array';
  /**
   * Determines if the update response should include the values of the cells
   * that were updated. By default, responses do not include the updated values.
   * The `updatedData` field within each of the
   * BatchUpdateValuesResponse.responses contains the updated values. If the
   * range to write was larger than the range actually written, the response
   * includes all values in the requested range (excluding trailing empty rows
   * and columns).
   *
   * @var bool
   */
  public $includeValuesInResponse;
  /**
   * Determines how dates, times, and durations in the response should be
   * rendered. This is ignored if response_value_render_option is
   * FORMATTED_VALUE. The default dateTime render option is SERIAL_NUMBER.
   *
   * @var string
   */
  public $responseDateTimeRenderOption;
  /**
   * Determines how values in the response should be rendered. The default
   * render option is FORMATTED_VALUE.
   *
   * @var string
   */
  public $responseValueRenderOption;
  /**
   * How the input data should be interpreted.
   *
   * @var string
   */
  public $valueInputOption;

  /**
   * The new values to apply to the spreadsheet. If more than one range is
   * matched by the specified DataFilter the specified values are applied to all
   * of those ranges.
   *
   * @param DataFilterValueRange[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return DataFilterValueRange[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Determines if the update response should include the values of the cells
   * that were updated. By default, responses do not include the updated values.
   * The `updatedData` field within each of the
   * BatchUpdateValuesResponse.responses contains the updated values. If the
   * range to write was larger than the range actually written, the response
   * includes all values in the requested range (excluding trailing empty rows
   * and columns).
   *
   * @param bool $includeValuesInResponse
   */
  public function setIncludeValuesInResponse($includeValuesInResponse)
  {
    $this->includeValuesInResponse = $includeValuesInResponse;
  }
  /**
   * @return bool
   */
  public function getIncludeValuesInResponse()
  {
    return $this->includeValuesInResponse;
  }
  /**
   * Determines how dates, times, and durations in the response should be
   * rendered. This is ignored if response_value_render_option is
   * FORMATTED_VALUE. The default dateTime render option is SERIAL_NUMBER.
   *
   * Accepted values: SERIAL_NUMBER, FORMATTED_STRING
   *
   * @param self::RESPONSE_DATE_TIME_RENDER_OPTION_* $responseDateTimeRenderOption
   */
  public function setResponseDateTimeRenderOption($responseDateTimeRenderOption)
  {
    $this->responseDateTimeRenderOption = $responseDateTimeRenderOption;
  }
  /**
   * @return self::RESPONSE_DATE_TIME_RENDER_OPTION_*
   */
  public function getResponseDateTimeRenderOption()
  {
    return $this->responseDateTimeRenderOption;
  }
  /**
   * Determines how values in the response should be rendered. The default
   * render option is FORMATTED_VALUE.
   *
   * Accepted values: FORMATTED_VALUE, UNFORMATTED_VALUE, FORMULA
   *
   * @param self::RESPONSE_VALUE_RENDER_OPTION_* $responseValueRenderOption
   */
  public function setResponseValueRenderOption($responseValueRenderOption)
  {
    $this->responseValueRenderOption = $responseValueRenderOption;
  }
  /**
   * @return self::RESPONSE_VALUE_RENDER_OPTION_*
   */
  public function getResponseValueRenderOption()
  {
    return $this->responseValueRenderOption;
  }
  /**
   * How the input data should be interpreted.
   *
   * Accepted values: INPUT_VALUE_OPTION_UNSPECIFIED, RAW, USER_ENTERED
   *
   * @param self::VALUE_INPUT_OPTION_* $valueInputOption
   */
  public function setValueInputOption($valueInputOption)
  {
    $this->valueInputOption = $valueInputOption;
  }
  /**
   * @return self::VALUE_INPUT_OPTION_*
   */
  public function getValueInputOption()
  {
    return $this->valueInputOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUpdateValuesByDataFilterRequest::class, 'Google_Service_Sheets_BatchUpdateValuesByDataFilterRequest');
