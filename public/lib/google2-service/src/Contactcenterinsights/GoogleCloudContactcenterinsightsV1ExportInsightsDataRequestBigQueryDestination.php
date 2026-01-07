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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1ExportInsightsDataRequestBigQueryDestination extends \Google\Model
{
  /**
   * Required. The name of the BigQuery dataset that the snapshot result should
   * be exported to. If this dataset does not exist, the export call returns an
   * INVALID_ARGUMENT error.
   *
   * @var string
   */
  public $dataset;
  /**
   * A project ID or number. If specified, then export will attempt to write
   * data to this project instead of the resource project. Otherwise, the
   * resource project will be used.
   *
   * @var string
   */
  public $projectId;
  /**
   * The BigQuery table name to which the insights data should be written. If
   * this table does not exist, the export call returns an INVALID_ARGUMENT
   * error.
   *
   * @var string
   */
  public $table;

  /**
   * Required. The name of the BigQuery dataset that the snapshot result should
   * be exported to. If this dataset does not exist, the export call returns an
   * INVALID_ARGUMENT error.
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * A project ID or number. If specified, then export will attempt to write
   * data to this project instead of the resource project. Otherwise, the
   * resource project will be used.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * The BigQuery table name to which the insights data should be written. If
   * this table does not exist, the export call returns an INVALID_ARGUMENT
   * error.
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1ExportInsightsDataRequestBigQueryDestination::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1ExportInsightsDataRequestBigQueryDestination');
