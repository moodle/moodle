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

class DimensionGroup extends \Google\Model
{
  /**
   * This field is true if this group is collapsed. A collapsed group remains
   * collapsed if an overlapping group at a shallower depth is expanded. A true
   * value does not imply that all dimensions within the group are hidden, since
   * a dimension's visibility can change independently from this group property.
   * However, when this property is updated, all dimensions within it are set to
   * hidden if this field is true, or set to visible if this field is false.
   *
   * @var bool
   */
  public $collapsed;
  /**
   * The depth of the group, representing how many groups have a range that
   * wholly contains the range of this group.
   *
   * @var int
   */
  public $depth;
  protected $rangeType = DimensionRange::class;
  protected $rangeDataType = '';

  /**
   * This field is true if this group is collapsed. A collapsed group remains
   * collapsed if an overlapping group at a shallower depth is expanded. A true
   * value does not imply that all dimensions within the group are hidden, since
   * a dimension's visibility can change independently from this group property.
   * However, when this property is updated, all dimensions within it are set to
   * hidden if this field is true, or set to visible if this field is false.
   *
   * @param bool $collapsed
   */
  public function setCollapsed($collapsed)
  {
    $this->collapsed = $collapsed;
  }
  /**
   * @return bool
   */
  public function getCollapsed()
  {
    return $this->collapsed;
  }
  /**
   * The depth of the group, representing how many groups have a range that
   * wholly contains the range of this group.
   *
   * @param int $depth
   */
  public function setDepth($depth)
  {
    $this->depth = $depth;
  }
  /**
   * @return int
   */
  public function getDepth()
  {
    return $this->depth;
  }
  /**
   * The range over which this group exists.
   *
   * @param DimensionRange $range
   */
  public function setRange(DimensionRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return DimensionRange
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DimensionGroup::class, 'Google_Service_Sheets_DimensionGroup');
