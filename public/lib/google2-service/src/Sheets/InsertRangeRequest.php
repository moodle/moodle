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

class InsertRangeRequest extends \Google\Model
{
  /**
   * The default value, do not use.
   */
  public const SHIFT_DIMENSION_DIMENSION_UNSPECIFIED = 'DIMENSION_UNSPECIFIED';
  /**
   * Operates on the rows of a sheet.
   */
  public const SHIFT_DIMENSION_ROWS = 'ROWS';
  /**
   * Operates on the columns of a sheet.
   */
  public const SHIFT_DIMENSION_COLUMNS = 'COLUMNS';
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';
  /**
   * The dimension which will be shifted when inserting cells. If ROWS, existing
   * cells will be shifted down. If COLUMNS, existing cells will be shifted
   * right.
   *
   * @var string
   */
  public $shiftDimension;

  /**
   * The range to insert new cells into. The range is constrained to the current
   * sheet boundaries.
   *
   * @param GridRange $range
   */
  public function setRange(GridRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return GridRange
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * The dimension which will be shifted when inserting cells. If ROWS, existing
   * cells will be shifted down. If COLUMNS, existing cells will be shifted
   * right.
   *
   * Accepted values: DIMENSION_UNSPECIFIED, ROWS, COLUMNS
   *
   * @param self::SHIFT_DIMENSION_* $shiftDimension
   */
  public function setShiftDimension($shiftDimension)
  {
    $this->shiftDimension = $shiftDimension;
  }
  /**
   * @return self::SHIFT_DIMENSION_*
   */
  public function getShiftDimension()
  {
    return $this->shiftDimension;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertRangeRequest::class, 'Google_Service_Sheets_InsertRangeRequest');
