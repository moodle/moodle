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

class BandedRange extends \Google\Model
{
  /**
   * The ID of the banded range. If unset, refer to banded_range_reference.
   *
   * @var int
   */
  public $bandedRangeId;
  /**
   * Output only. The reference of the banded range, used to identify the ID
   * that is not supported by the banded_range_id.
   *
   * @var string
   */
  public $bandedRangeReference;
  protected $columnPropertiesType = BandingProperties::class;
  protected $columnPropertiesDataType = '';
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';
  protected $rowPropertiesType = BandingProperties::class;
  protected $rowPropertiesDataType = '';

  /**
   * The ID of the banded range. If unset, refer to banded_range_reference.
   *
   * @param int $bandedRangeId
   */
  public function setBandedRangeId($bandedRangeId)
  {
    $this->bandedRangeId = $bandedRangeId;
  }
  /**
   * @return int
   */
  public function getBandedRangeId()
  {
    return $this->bandedRangeId;
  }
  /**
   * Output only. The reference of the banded range, used to identify the ID
   * that is not supported by the banded_range_id.
   *
   * @param string $bandedRangeReference
   */
  public function setBandedRangeReference($bandedRangeReference)
  {
    $this->bandedRangeReference = $bandedRangeReference;
  }
  /**
   * @return string
   */
  public function getBandedRangeReference()
  {
    return $this->bandedRangeReference;
  }
  /**
   * Properties for column bands. These properties are applied on a column- by-
   * column basis throughout all the columns in the range. At least one of
   * row_properties or column_properties must be specified.
   *
   * @param BandingProperties $columnProperties
   */
  public function setColumnProperties(BandingProperties $columnProperties)
  {
    $this->columnProperties = $columnProperties;
  }
  /**
   * @return BandingProperties
   */
  public function getColumnProperties()
  {
    return $this->columnProperties;
  }
  /**
   * The range over which these properties are applied.
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
   * Properties for row bands. These properties are applied on a row-by-row
   * basis throughout all the rows in the range. At least one of row_properties
   * or column_properties must be specified.
   *
   * @param BandingProperties $rowProperties
   */
  public function setRowProperties(BandingProperties $rowProperties)
  {
    $this->rowProperties = $rowProperties;
  }
  /**
   * @return BandingProperties
   */
  public function getRowProperties()
  {
    return $this->rowProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BandedRange::class, 'Google_Service_Sheets_BandedRange');
