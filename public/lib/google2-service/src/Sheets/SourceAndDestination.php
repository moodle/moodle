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

class SourceAndDestination extends \Google\Model
{
  /**
   * The default value, do not use.
   */
  public const DIMENSION_DIMENSION_UNSPECIFIED = 'DIMENSION_UNSPECIFIED';
  /**
   * Operates on the rows of a sheet.
   */
  public const DIMENSION_ROWS = 'ROWS';
  /**
   * Operates on the columns of a sheet.
   */
  public const DIMENSION_COLUMNS = 'COLUMNS';
  /**
   * The dimension that data should be filled into.
   *
   * @var string
   */
  public $dimension;
  /**
   * The number of rows or columns that data should be filled into. Positive
   * numbers expand beyond the last row or last column of the source. Negative
   * numbers expand before the first row or first column of the source.
   *
   * @var int
   */
  public $fillLength;
  protected $sourceType = GridRange::class;
  protected $sourceDataType = '';

  /**
   * The dimension that data should be filled into.
   *
   * Accepted values: DIMENSION_UNSPECIFIED, ROWS, COLUMNS
   *
   * @param self::DIMENSION_* $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return self::DIMENSION_*
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * The number of rows or columns that data should be filled into. Positive
   * numbers expand beyond the last row or last column of the source. Negative
   * numbers expand before the first row or first column of the source.
   *
   * @param int $fillLength
   */
  public function setFillLength($fillLength)
  {
    $this->fillLength = $fillLength;
  }
  /**
   * @return int
   */
  public function getFillLength()
  {
    return $this->fillLength;
  }
  /**
   * The location of the data to use as the source of the autofill.
   *
   * @param GridRange $source
   */
  public function setSource(GridRange $source)
  {
    $this->source = $source;
  }
  /**
   * @return GridRange
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceAndDestination::class, 'Google_Service_Sheets_SourceAndDestination');
