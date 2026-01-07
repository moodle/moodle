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

namespace Google\Service\Slides;

class Range extends \Google\Model
{
  /**
   * Unspecified range type. This value must not be used.
   */
  public const TYPE_RANGE_TYPE_UNSPECIFIED = 'RANGE_TYPE_UNSPECIFIED';
  /**
   * A fixed range. Both the `start_index` and `end_index` must be specified.
   */
  public const TYPE_FIXED_RANGE = 'FIXED_RANGE';
  /**
   * Starts the range at `start_index` and continues until the end of the
   * collection. The `end_index` must not be specified.
   */
  public const TYPE_FROM_START_INDEX = 'FROM_START_INDEX';
  /**
   * Sets the range to be the whole length of the collection. Both the
   * `start_index` and the `end_index` must not be specified.
   */
  public const TYPE_ALL = 'ALL';
  /**
   * The optional zero-based index of the end of the collection. Required for
   * `FIXED_RANGE` ranges.
   *
   * @var int
   */
  public $endIndex;
  /**
   * The optional zero-based index of the beginning of the collection. Required
   * for `FIXED_RANGE` and `FROM_START_INDEX` ranges.
   *
   * @var int
   */
  public $startIndex;
  /**
   * The type of range.
   *
   * @var string
   */
  public $type;

  /**
   * The optional zero-based index of the end of the collection. Required for
   * `FIXED_RANGE` ranges.
   *
   * @param int $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return int
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * The optional zero-based index of the beginning of the collection. Required
   * for `FIXED_RANGE` and `FROM_START_INDEX` ranges.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * The type of range.
   *
   * Accepted values: RANGE_TYPE_UNSPECIFIED, FIXED_RANGE, FROM_START_INDEX, ALL
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
class_alias(Range::class, 'Google_Service_Slides_Range');
