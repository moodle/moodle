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

class GoogleCloudDataplexV1DataQualityResultAnomalyDetectionGeneratedAssets extends \Google\Model
{
  /**
   * Output only. The intermediate table for data anomaly detection. Format:
   * PROJECT_ID.DATASET_ID.TABLE_ID
   *
   * @var string
   */
  public $dataIntermediateTable;
  /**
   * Output only. The intermediate table for freshness anomaly detection.
   * Format: PROJECT_ID.DATASET_ID.TABLE_ID
   *
   * @var string
   */
  public $freshnessIntermediateTable;
  /**
   * Output only. The result table for anomaly detection. Format:
   * PROJECT_ID.DATASET_ID.TABLE_ID If the result table is set at
   * AnomalyDetectionAssets, the result table here would be the same as the one
   * set in the AnomalyDetectionAssets.result_table.
   *
   * @var string
   */
  public $resultTable;
  /**
   * Output only. The intermediate table for volume anomaly detection. Format:
   * PROJECT_ID.DATASET_ID.TABLE_ID
   *
   * @var string
   */
  public $volumeIntermediateTable;

  /**
   * Output only. The intermediate table for data anomaly detection. Format:
   * PROJECT_ID.DATASET_ID.TABLE_ID
   *
   * @param string $dataIntermediateTable
   */
  public function setDataIntermediateTable($dataIntermediateTable)
  {
    $this->dataIntermediateTable = $dataIntermediateTable;
  }
  /**
   * @return string
   */
  public function getDataIntermediateTable()
  {
    return $this->dataIntermediateTable;
  }
  /**
   * Output only. The intermediate table for freshness anomaly detection.
   * Format: PROJECT_ID.DATASET_ID.TABLE_ID
   *
   * @param string $freshnessIntermediateTable
   */
  public function setFreshnessIntermediateTable($freshnessIntermediateTable)
  {
    $this->freshnessIntermediateTable = $freshnessIntermediateTable;
  }
  /**
   * @return string
   */
  public function getFreshnessIntermediateTable()
  {
    return $this->freshnessIntermediateTable;
  }
  /**
   * Output only. The result table for anomaly detection. Format:
   * PROJECT_ID.DATASET_ID.TABLE_ID If the result table is set at
   * AnomalyDetectionAssets, the result table here would be the same as the one
   * set in the AnomalyDetectionAssets.result_table.
   *
   * @param string $resultTable
   */
  public function setResultTable($resultTable)
  {
    $this->resultTable = $resultTable;
  }
  /**
   * @return string
   */
  public function getResultTable()
  {
    return $this->resultTable;
  }
  /**
   * Output only. The intermediate table for volume anomaly detection. Format:
   * PROJECT_ID.DATASET_ID.TABLE_ID
   *
   * @param string $volumeIntermediateTable
   */
  public function setVolumeIntermediateTable($volumeIntermediateTable)
  {
    $this->volumeIntermediateTable = $volumeIntermediateTable;
  }
  /**
   * @return string
   */
  public function getVolumeIntermediateTable()
  {
    return $this->volumeIntermediateTable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualityResultAnomalyDetectionGeneratedAssets::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualityResultAnomalyDetectionGeneratedAssets');
