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

class DataFilterValueRange extends \Google\Collection
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
  protected $dataFilterType = DataFilter::class;
  protected $dataFilterDataType = '';
  /**
   * The major dimension of the values.
   *
   * @var string
   */
  public $majorDimension;
  /**
   * The data to be written. If the provided values exceed any of the ranges
   * matched by the data filter then the request fails. If the provided values
   * are less than the matched ranges only the specified values are written,
   * existing values in the matched ranges remain unaffected.
   *
   * @var array[]
   */
  public $values;

  /**
   * The data filter describing the location of the values in the spreadsheet.
   *
   * @param DataFilter $dataFilter
   */
  public function setDataFilter(DataFilter $dataFilter)
  {
    $this->dataFilter = $dataFilter;
  }
  /**
   * @return DataFilter
   */
  public function getDataFilter()
  {
    return $this->dataFilter;
  }
  /**
   * The major dimension of the values.
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
   * The data to be written. If the provided values exceed any of the ranges
   * matched by the data filter then the request fails. If the provided values
   * are less than the matched ranges only the specified values are written,
   * existing values in the matched ranges remain unaffected.
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
class_alias(DataFilterValueRange::class, 'Google_Service_Sheets_DataFilterValueRange');
