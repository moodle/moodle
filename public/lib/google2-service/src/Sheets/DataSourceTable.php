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

class DataSourceTable extends \Google\Collection
{
  /**
   * The default column selection type, do not use.
   */
  public const COLUMN_SELECTION_TYPE_DATA_SOURCE_TABLE_COLUMN_SELECTION_TYPE_UNSPECIFIED = 'DATA_SOURCE_TABLE_COLUMN_SELECTION_TYPE_UNSPECIFIED';
  /**
   * Select columns specified by columns field.
   */
  public const COLUMN_SELECTION_TYPE_SELECTED = 'SELECTED';
  /**
   * Sync all current and future columns in the data source. If set, the data
   * source table fetches all the columns in the data source at the time of
   * refresh.
   */
  public const COLUMN_SELECTION_TYPE_SYNC_ALL = 'SYNC_ALL';
  protected $collection_key = 'sortSpecs';
  /**
   * The type to select columns for the data source table. Defaults to SELECTED.
   *
   * @var string
   */
  public $columnSelectionType;
  protected $columnsType = DataSourceColumnReference::class;
  protected $columnsDataType = 'array';
  protected $dataExecutionStatusType = DataExecutionStatus::class;
  protected $dataExecutionStatusDataType = '';
  /**
   * The ID of the data source the data source table is associated with.
   *
   * @var string
   */
  public $dataSourceId;
  protected $filterSpecsType = FilterSpec::class;
  protected $filterSpecsDataType = 'array';
  /**
   * The limit of rows to return. If not set, a default limit is applied. Please
   * refer to the Sheets editor for the default and max limit.
   *
   * @var int
   */
  public $rowLimit;
  protected $sortSpecsType = SortSpec::class;
  protected $sortSpecsDataType = 'array';

  /**
   * The type to select columns for the data source table. Defaults to SELECTED.
   *
   * Accepted values: DATA_SOURCE_TABLE_COLUMN_SELECTION_TYPE_UNSPECIFIED,
   * SELECTED, SYNC_ALL
   *
   * @param self::COLUMN_SELECTION_TYPE_* $columnSelectionType
   */
  public function setColumnSelectionType($columnSelectionType)
  {
    $this->columnSelectionType = $columnSelectionType;
  }
  /**
   * @return self::COLUMN_SELECTION_TYPE_*
   */
  public function getColumnSelectionType()
  {
    return $this->columnSelectionType;
  }
  /**
   * Columns selected for the data source table. The column_selection_type must
   * be SELECTED.
   *
   * @param DataSourceColumnReference[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return DataSourceColumnReference[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Output only. The data execution status.
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
   * The ID of the data source the data source table is associated with.
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
   * Filter specifications in the data source table.
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
   * The limit of rows to return. If not set, a default limit is applied. Please
   * refer to the Sheets editor for the default and max limit.
   *
   * @param int $rowLimit
   */
  public function setRowLimit($rowLimit)
  {
    $this->rowLimit = $rowLimit;
  }
  /**
   * @return int
   */
  public function getRowLimit()
  {
    return $this->rowLimit;
  }
  /**
   * Sort specifications in the data source table. The result of the data source
   * table is sorted based on the sort specifications in order.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSourceTable::class, 'Google_Service_Sheets_DataSourceTable');
