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

namespace Google\Service\CloudNaturalLanguage;

class XPSColumnSpec extends \Google\Collection
{
  protected $collection_key = 'topCorrelatedColumns';
  /**
   * The unique id of the column. When Preprocess, the Tables BE will popuate
   * the order id of the column, which reflects the order of the column inside
   * the table, i.e. 0 means the first column in the table, N-1 means the last
   * column. AutoML BE will persist this order id in Spanner and set the order
   * id here when calling RefreshTablesStats and Train. Note: it's different
   * than the column_spec_id that is generated in AutoML BE.
   *
   * @var int
   */
  public $columnId;
  protected $dataStatsType = XPSDataStats::class;
  protected $dataStatsDataType = '';
  protected $dataTypeType = XPSDataType::class;
  protected $dataTypeDataType = '';
  /**
   * The display name of the column. It's outputed in Preprocess and a required
   * input for RefreshTablesStats and Train.
   *
   * @var string
   */
  public $displayName;
  protected $forecastingMetadataType = XPSColumnSpecForecastingMetadata::class;
  protected $forecastingMetadataDataType = '';
  protected $topCorrelatedColumnsType = XPSColumnSpecCorrelatedColumn::class;
  protected $topCorrelatedColumnsDataType = 'array';

  /**
   * The unique id of the column. When Preprocess, the Tables BE will popuate
   * the order id of the column, which reflects the order of the column inside
   * the table, i.e. 0 means the first column in the table, N-1 means the last
   * column. AutoML BE will persist this order id in Spanner and set the order
   * id here when calling RefreshTablesStats and Train. Note: it's different
   * than the column_spec_id that is generated in AutoML BE.
   *
   * @param int $columnId
   */
  public function setColumnId($columnId)
  {
    $this->columnId = $columnId;
  }
  /**
   * @return int
   */
  public function getColumnId()
  {
    return $this->columnId;
  }
  /**
   * The data stats of the column. It's outputed in RefreshTablesStats and a
   * required input for Train.
   *
   * @param XPSDataStats $dataStats
   */
  public function setDataStats(XPSDataStats $dataStats)
  {
    $this->dataStats = $dataStats;
  }
  /**
   * @return XPSDataStats
   */
  public function getDataStats()
  {
    return $this->dataStats;
  }
  /**
   * The data type of the column. It's outputed in Preprocess rpc and a required
   * input for RefreshTablesStats and Train.
   *
   * @param XPSDataType $dataType
   */
  public function setDataType(XPSDataType $dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return XPSDataType
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * The display name of the column. It's outputed in Preprocess and a required
   * input for RefreshTablesStats and Train.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * @param XPSColumnSpecForecastingMetadata $forecastingMetadata
   */
  public function setForecastingMetadata(XPSColumnSpecForecastingMetadata $forecastingMetadata)
  {
    $this->forecastingMetadata = $forecastingMetadata;
  }
  /**
   * @return XPSColumnSpecForecastingMetadata
   */
  public function getForecastingMetadata()
  {
    return $this->forecastingMetadata;
  }
  /**
   * It's outputed in RefreshTablesStats, and a required input in Train.
   *
   * @param XPSColumnSpecCorrelatedColumn[] $topCorrelatedColumns
   */
  public function setTopCorrelatedColumns($topCorrelatedColumns)
  {
    $this->topCorrelatedColumns = $topCorrelatedColumns;
  }
  /**
   * @return XPSColumnSpecCorrelatedColumn[]
   */
  public function getTopCorrelatedColumns()
  {
    return $this->topCorrelatedColumns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSColumnSpec::class, 'Google_Service_CloudNaturalLanguage_XPSColumnSpec');
