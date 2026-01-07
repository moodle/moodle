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

class XPSTablesTrainResponse extends \Google\Collection
{
  protected $collection_key = 'tablesModelColumnInfo';
  protected $modelStructureType = XPSTablesModelStructure::class;
  protected $modelStructureDataType = '';
  protected $predictionSampleRowsType = XPSRow::class;
  protected $predictionSampleRowsDataType = 'array';
  protected $tablesModelColumnInfoType = XPSTablesModelColumnInfo::class;
  protected $tablesModelColumnInfoDataType = 'array';
  /**
   * The actual training cost of the model, expressed in milli node hours, i.e.
   * 1,000 value in this field means 1 node hour. Guaranteed to not exceed the
   * train budget.
   *
   * @var string
   */
  public $trainCostMilliNodeHours;

  /**
   * @param XPSTablesModelStructure $modelStructure
   */
  public function setModelStructure(XPSTablesModelStructure $modelStructure)
  {
    $this->modelStructure = $modelStructure;
  }
  /**
   * @return XPSTablesModelStructure
   */
  public function getModelStructure()
  {
    return $this->modelStructure;
  }
  /**
   * Sample rows from the dataset this model was trained.
   *
   * @param XPSRow[] $predictionSampleRows
   */
  public function setPredictionSampleRows($predictionSampleRows)
  {
    $this->predictionSampleRows = $predictionSampleRows;
  }
  /**
   * @return XPSRow[]
   */
  public function getPredictionSampleRows()
  {
    return $this->predictionSampleRows;
  }
  /**
   * Output only. Auxiliary information for each of the
   * input_feature_column_specs, with respect to this particular model.
   *
   * @param XPSTablesModelColumnInfo[] $tablesModelColumnInfo
   */
  public function setTablesModelColumnInfo($tablesModelColumnInfo)
  {
    $this->tablesModelColumnInfo = $tablesModelColumnInfo;
  }
  /**
   * @return XPSTablesModelColumnInfo[]
   */
  public function getTablesModelColumnInfo()
  {
    return $this->tablesModelColumnInfo;
  }
  /**
   * The actual training cost of the model, expressed in milli node hours, i.e.
   * 1,000 value in this field means 1 node hour. Guaranteed to not exceed the
   * train budget.
   *
   * @param string $trainCostMilliNodeHours
   */
  public function setTrainCostMilliNodeHours($trainCostMilliNodeHours)
  {
    $this->trainCostMilliNodeHours = $trainCostMilliNodeHours;
  }
  /**
   * @return string
   */
  public function getTrainCostMilliNodeHours()
  {
    return $this->trainCostMilliNodeHours;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTablesTrainResponse::class, 'Google_Service_CloudNaturalLanguage_XPSTablesTrainResponse');
