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

class PivotTable extends \Google\Collection
{
  /**
   * Values are laid out horizontally (as columns).
   */
  public const VALUE_LAYOUT_HORIZONTAL = 'HORIZONTAL';
  /**
   * Values are laid out vertically (as rows).
   */
  public const VALUE_LAYOUT_VERTICAL = 'VERTICAL';
  protected $collection_key = 'values';
  protected $columnsType = PivotGroup::class;
  protected $columnsDataType = 'array';
  protected $criteriaType = PivotFilterCriteria::class;
  protected $criteriaDataType = 'map';
  protected $dataExecutionStatusType = DataExecutionStatus::class;
  protected $dataExecutionStatusDataType = '';
  /**
   * The ID of the data source the pivot table is reading data from.
   *
   * @var string
   */
  public $dataSourceId;
  protected $filterSpecsType = PivotFilterSpec::class;
  protected $filterSpecsDataType = 'array';
  protected $rowsType = PivotGroup::class;
  protected $rowsDataType = 'array';
  protected $sourceType = GridRange::class;
  protected $sourceDataType = '';
  /**
   * Whether values should be listed horizontally (as columns) or vertically (as
   * rows).
   *
   * @var string
   */
  public $valueLayout;
  protected $valuesType = PivotValue::class;
  protected $valuesDataType = 'array';

  /**
   * Each column grouping in the pivot table.
   *
   * @param PivotGroup[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return PivotGroup[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * An optional mapping of filters per source column offset. The filters are
   * applied before aggregating data into the pivot table. The map's key is the
   * column offset of the source range that you want to filter, and the value is
   * the criteria for that column. For example, if the source was `C10:E15`, a
   * key of `0` will have the filter for column `C`, whereas the key `1` is for
   * column `D`. This field is deprecated in favor of filter_specs.
   *
   * @deprecated
   * @param PivotFilterCriteria[] $criteria
   */
  public function setCriteria($criteria)
  {
    $this->criteria = $criteria;
  }
  /**
   * @deprecated
   * @return PivotFilterCriteria[]
   */
  public function getCriteria()
  {
    return $this->criteria;
  }
  /**
   * Output only. The data execution status for data source pivot tables.
   *
   * @param DataExecutionStatus $dataExecutionStatus
   */
  public function setDataExecutionStatus(DataExecutionStatus $dataExecutionStatus)
  {
    $this->dataExecutionStatus = $dataExecutionStatus;
  }
  /**
   * @return DataExecutionStatus
   */
  public function getDataExecutionStatus()
  {
    return $this->dataExecutionStatus;
  }
  /**
   * The ID of the data source the pivot table is reading data from.
   *
   * @param string $dataSourceId
   */
  public function setDataSourceId($dataSourceId)
  {
    $this->dataSourceId = $dataSourceId;
  }
  /**
   * @return string
   */
  public function getDataSourceId()
  {
    return $this->dataSourceId;
  }
  /**
   * The filters applied to the source columns before aggregating data for the
   * pivot table. Both criteria and filter_specs are populated in responses. If
   * both fields are specified in an update request, this field takes
   * precedence.
   *
   * @param PivotFilterSpec[] $filterSpecs
   */
  public function setFilterSpecs($filterSpecs)
  {
    $this->filterSpecs = $filterSpecs;
  }
  /**
   * @return PivotFilterSpec[]
   */
  public function getFilterSpecs()
  {
    return $this->filterSpecs;
  }
  /**
   * Each row grouping in the pivot table.
   *
   * @param PivotGroup[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return PivotGroup[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * The range the pivot table is reading data from.
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
  /**
   * Whether values should be listed horizontally (as columns) or vertically (as
   * rows).
   *
   * Accepted values: HORIZONTAL, VERTICAL
   *
   * @param self::VALUE_LAYOUT_* $valueLayout
   */
  public function setValueLayout($valueLayout)
  {
    $this->valueLayout = $valueLayout;
  }
  /**
   * @return self::VALUE_LAYOUT_*
   */
  public function getValueLayout()
  {
    return $this->valueLayout;
  }
  /**
   * A list of values to include in the pivot table.
   *
   * @param PivotValue[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return PivotValue[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PivotTable::class, 'Google_Service_Sheets_PivotTable');
