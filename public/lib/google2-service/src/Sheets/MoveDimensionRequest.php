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

class MoveDimensionRequest extends \Google\Model
{
  /**
   * The zero-based start index of where to move the source data to, based on
   * the coordinates *before* the source data is removed from the grid. Existing
   * data will be shifted down or right (depending on the dimension) to make
   * room for the moved dimensions. The source dimensions are removed from the
   * grid, so the the data may end up in a different index than specified. For
   * example, given `A1..A5` of `0, 1, 2, 3, 4` and wanting to move `"1"` and
   * `"2"` to between `"3"` and `"4"`, the source would be `ROWS [1..3)`,and the
   * destination index would be `"4"` (the zero-based index of row 5). The end
   * result would be `A1..A5` of `0, 3, 1, 2, 4`.
   *
   * @var int
   */
  public $destinationIndex;
  protected $sourceType = DimensionRange::class;
  protected $sourceDataType = '';

  /**
   * The zero-based start index of where to move the source data to, based on
   * the coordinates *before* the source data is removed from the grid. Existing
   * data will be shifted down or right (depending on the dimension) to make
   * room for the moved dimensions. The source dimensions are removed from the
   * grid, so the the data may end up in a different index than specified. For
   * example, given `A1..A5` of `0, 1, 2, 3, 4` and wanting to move `"1"` and
   * `"2"` to between `"3"` and `"4"`, the source would be `ROWS [1..3)`,and the
   * destination index would be `"4"` (the zero-based index of row 5). The end
   * result would be `A1..A5` of `0, 3, 1, 2, 4`.
   *
   * @param int $destinationIndex
   */
  public function setDestinationIndex($destinationIndex)
  {
    $this->destinationIndex = $destinationIndex;
  }
  /**
   * @return int
   */
  public function getDestinationIndex()
  {
    return $this->destinationIndex;
  }
  /**
   * The source dimensions to move.
   *
   * @param DimensionRange $source
   */
  public function setSource(DimensionRange $source)
  {
    $this->source = $source;
  }
  /**
   * @return DimensionRange
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MoveDimensionRequest::class, 'Google_Service_Sheets_MoveDimensionRequest');
