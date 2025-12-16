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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataQualityResult extends \Google\Collection
{
  protected $collection_key = 'rules';
  protected $anomalyDetectionGeneratedAssetsType = GoogleCloudDataplexV1DataQualityResultAnomalyDetectionGeneratedAssets::class;
  protected $anomalyDetectionGeneratedAssetsDataType = '';
  protected $catalogPublishingStatusType = GoogleCloudDataplexV1DataScanCatalogPublishingStatus::class;
  protected $catalogPublishingStatusDataType = '';
  protected $columnsType = GoogleCloudDataplexV1DataQualityColumnResult::class;
  protected $columnsDataType = 'array';
  protected $dimensionsType = GoogleCloudDataplexV1DataQualityDimensionResult::class;
  protected $dimensionsDataType = 'array';
  /**
   * Output only. Overall data quality result -- true if all rules passed.
   *
   * @var bool
   */
  public $passed;
  protected $postScanActionsResultType = GoogleCloudDataplexV1DataQualityResultPostScanActionsResult::class;
  protected $postScanActionsResultDataType = '';
  /**
   * Output only. The count of rows processed.
   *
   * @var string
   */
  public $rowCount;
  protected $rulesType = GoogleCloudDataplexV1DataQualityRuleResult::class;
  protected $rulesDataType = 'array';
  protected $scannedDataType = GoogleCloudDataplexV1ScannedData::class;
  protected $scannedDataDataType = '';
  /**
   * Output only. The overall data quality score.The score ranges between 0, 100
   * (up to two decimal points).
   *
   * @var float
   */
  public $score;

  /**
   * Output only. The generated assets for anomaly detection.
   *
   * @param GoogleCloudDataplexV1DataQualityResultAnomalyDetectionGeneratedAssets $anomalyDetectionGeneratedAssets
   */
  public function setAnomalyDetectionGeneratedAssets(GoogleCloudDataplexV1DataQualityResultAnomalyDetectionGeneratedAssets $anomalyDetectionGeneratedAssets)
  {
    $this->anomalyDetectionGeneratedAssets = $anomalyDetectionGeneratedAssets;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityResultAnomalyDetectionGeneratedAssets
   */
  public function getAnomalyDetectionGeneratedAssets()
  {
    return $this->anomalyDetectionGeneratedAssets;
  }
  /**
   * Output only. The status of publishing the data scan as Dataplex Universal
   * Catalog metadata.
   *
   * @param GoogleCloudDataplexV1DataScanCatalogPublishingStatus $catalogPublishingStatus
   */
  public function setCatalogPublishingStatus(GoogleCloudDataplexV1DataScanCatalogPublishingStatus $catalogPublishingStatus)
  {
    $this->catalogPublishingStatus = $catalogPublishingStatus;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanCatalogPublishingStatus
   */
  public function getCatalogPublishingStatus()
  {
    return $this->catalogPublishingStatus;
  }
  /**
   * Output only. A list of results at the column level.A column will have a
   * corresponding DataQualityColumnResult if and only if there is at least one
   * rule with the 'column' field set to it.
   *
   * @param GoogleCloudDataplexV1DataQualityColumnResult[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityColumnResult[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Output only. A list of results at the dimension level.A dimension will have
   * a corresponding DataQualityDimensionResult if and only if there is at least
   * one rule with the 'dimension' field set to it.
   *
   * @param GoogleCloudDataplexV1DataQualityDimensionResult[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityDimensionResult[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Output only. Overall data quality result -- true if all rules passed.
   *
   * @param bool $passed
   */
  public function setPassed($passed)
  {
    $this->passed = $passed;
  }
  /**
   * @return bool
   */
  public function getPassed()
  {
    return $this->passed;
  }
  /**
   * Output only. The result of post scan actions.
   *
   * @param GoogleCloudDataplexV1DataQualityResultPostScanActionsResult $postScanActionsResult
   */
  public function setPostScanActionsResult(GoogleCloudDataplexV1DataQualityResultPostScanActionsResult $postScanActionsResult)
  {
    $this->postScanActionsResult = $postScanActionsResult;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityResultPostScanActionsResult
   */
  public function getPostScanActionsResult()
  {
    return $this->postScanActionsResult;
  }
  /**
   * Output only. The count of rows processed.
   *
   * @param string $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return string
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
  /**
   * Output only. A list of all the rules in a job, and their results.
   *
   * @param GoogleCloudDataplexV1DataQualityRuleResult[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRuleResult[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Output only. The data scanned for this result.
   *
   * @param GoogleCloudDataplexV1ScannedData $scannedData
   */
  public function setScannedData(GoogleCloudDataplexV1ScannedData $scannedData)
  {
    $this->scannedData = $scannedData;
  }
  /**
   * @return GoogleCloudDataplexV1ScannedData
   */
  public function getScannedData()
  {
    return $this->scannedData;
  }
  /**
   * Output only. The overall data quality score.The score ranges between 0, 100
   * (up to two decimal points).
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualityResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualityResult');
