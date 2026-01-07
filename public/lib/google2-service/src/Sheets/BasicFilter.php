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

class BasicFilter extends \Google\Collection
{
  protected $collection_key = 'sortSpecs';
  protected $criteriaType = FilterCriteria::class;
  protected $criteriaDataType = 'map';
  protected $filterSpecsType = FilterSpec::class;
  protected $filterSpecsDataType = 'array';
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';
  protected $sortSpecsType = SortSpec::class;
  protected $sortSpecsDataType = 'array';
  /**
   * The table this filter is backed by, if any. When writing, only one of range
   * or table_id may be set.
   *
   * @var string
   */
  public $tableId;

  /**
   * The criteria for showing/hiding values per column. The map's key is the
   * column index, and the value is the criteria for that column. This field is
   * deprecated in favor of filter_specs.
   *
   * @deprecated
   * @param FilterCriteria[] $criteria
   */
  public function setCriteria($criteria)
  {
    $this->criteria = $criteria;
  }
  /**
   * @deprecated
   * @return FilterCriteria[]
   */
  public function getCriteria()
  {
    return $this->criteria;
  }
  /**
   * The filter criteria per column. Both criteria and filter_specs are
   * populated in responses. If both fields are specified in an update request,
   * this field takes precedence.
   *
   * @param FilterSpec[] $filterSpecs
   */
  public function setFilterSpecs($filterSpecs)
  {
    $this->filterSpecs = $filterSpecs;
  }
  /**
   * @return FilterSpec[]
   */
  public function getFilterSpecs()
  {
    return $this->filterSpecs;
  }
  /**
   * The range the filter covers.
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
   * The sort order per column. Later specifications are used when values are
   * equal in the earlier specifications.
   *
   * @param SortSpec[] $sortSpecs
   */
  public function setSortSpecs($sortSpecs)
  {
    $this->sortSpecs = $sortSpecs;
  }
  /**
   * @return SortSpec[]
   */
  public function getSortSpecs()
  {
    return $this->sortSpecs;
  }
  /**
   * The table this filter is backed by, if any. When writing, only one of range
   * or table_id may be set.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicFilter::class, 'Google_Service_Sheets_BasicFilter');
