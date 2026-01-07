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

class ValueRange extends \Google\Collection
{
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
  protected $collection_key = 'values';
  /**
   * The major dimension of the values. For output, if the spreadsheet data is:
   * `A1=1,B1=2,A2=3,B2=4`, then requesting `range=A1:B2,majorDimension=ROWS`
   * will return `[[1,2],[3,4]]`, whereas requesting
   * `range=A1:B2,majorDimension=COLUMNS` will return `[[1,3],[2,4]]`. For
   * input, with `range=A1:B2,majorDimension=ROWS` then `[[1,2],[3,4]]` will set
   * `A1=1,B1=2,A2=3,B2=4`. With `range=A1:B2,majorDimension=COLUMNS` then
   * `[[1,2],[3,4]]` will set `A1=1,B1=3,A2=2,B2=4`. When writing, if this field
   * is not set, it defaults to ROWS.
   *
   * @var string
   */
  public $majorDimension;
  /**
   * The range the values cover, in [A1 notation](https://developers.google.com/
   * workspace/sheets/api/guides/concepts#cell). For output, this range
   * indicates the entire requested range, even though the values will exclude
   * trailing rows and columns. When appending values, this field represents the
   * range to search for a table, after which values will be appended.
   *
   * @var string
   */
  public $range;
  /**
   * The data that was read or to be written. This is an array of arrays, the
   * outer array representing all the data and each inner array representing a
   * major dimension. Each item in the inner array corresponds with one cell.
   * For output, empty trailing rows and columns will not be included. For
   * input, supported value types are: bool, string, and double. Null values
   * will be skipped. To set a cell to an empty value, set the string value to
   * an empty string.
   *
   * @var array[]
   */
  public $values;

  /**
   * The major dimension of the values. For output, if the spreadsheet data is:
   * `A1=1,B1=2,A2=3,B2=4`, then requesting `range=A1:B2,majorDimension=ROWS`
   * will return `[[1,2],[3,4]]`, whereas requesting
   * `range=A1:B2,majorDimension=COLUMNS` will return `[[1,3],[2,4]]`. For
   * input, with `range=A1:B2,majorDimension=ROWS` then `[[1,2],[3,4]]` will set
   * `A1=1,B1=2,A2=3,B2=4`. With `range=A1:B2,majorDimension=COLUMNS` then
   * `[[1,2],[3,4]]` will set `A1=1,B1=3,A2=2,B2=4`. When writing, if this field
   * is not set, it defaults to ROWS.
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
   * The range the values cover, in [A1 notation](https://developers.google.com/
   * workspace/sheets/api/guides/concepts#cell). For output, this range
   * indicates the entire requested range, even though the values will exclude
   * trailing rows and columns. When appending values, this field represents the
   * range to search for a table, after which values will be appended.
   *
   * @param string $range
   */
  public function setRange($range)
  {
    $this->range = $range;
  }
  /**
   * @return string
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * The data that was read or to be written. This is an array of arrays, the
   * outer array representing all the data and each inner array representing a
   * major dimension. Each item in the inner array corresponds with one cell.
   * For output, empty trailing rows and columns will not be included. For
   * input, supported value types are: bool, string, and double. Null values
   * will be skipped. To set a cell to an empty value, set the string value to
   * an empty string.
   *
   * @param array[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return array[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValueRange::class, 'Google_Service_Sheets_ValueRange');
