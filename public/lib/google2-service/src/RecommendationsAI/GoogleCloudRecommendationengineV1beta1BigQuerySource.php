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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1BigQuerySource extends \Google\Model
{
  /**
   * Optional. The schema to use when parsing the data from the source.
   * Supported values for catalog imports: 1: "catalog_recommendations_ai" using
   * https://cloud.google.com/recommendations-ai/docs/upload-catalog#json
   * (Default for catalogItems.import) 2: "catalog_merchant_center" using
   * https://cloud.google.com/recommendations-ai/docs/upload-catalog#mc
   * Supported values for user event imports: 1:
   * "user_events_recommendations_ai" using
   * https://cloud.google.com/recommendations-ai/docs/manage-user-events#import
   * (Default for userEvents.import) 2. "user_events_ga360" using
   * https://support.google.com/analytics/answer/3437719?hl=en
   *
   * @var string
   */
  public $dataSchema;
  /**
   * Required. The BigQuery data set to copy the data from.
   *
   * @var string
   */
  public $datasetId;
  /**
   * Optional. Intermediate Cloud Storage directory used for the import. Can be
   * specified if one wants to have the BigQuery export to a specific Cloud
   * Storage directory.
   *
   * @var string
   */
  public $gcsStagingDir;
  /**
   * Optional. The project id (can be project # or id) that the BigQuery source
   * is in. If not specified, inherits the project id from the parent request.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. The BigQuery table to copy the data from.
   *
   * @var string
   */
  public $tableId;

  /**
   * Optional. The schema to use when parsing the data from the source.
   * Supported values for catalog imports: 1: "catalog_recommendations_ai" using
   * https://cloud.google.com/recommendations-ai/docs/upload-catalog#json
   * (Default for catalogItems.import) 2: "catalog_merchant_center" using
   * https://cloud.google.com/recommendations-ai/docs/upload-catalog#mc
   * Supported values for user event imports: 1:
   * "user_events_recommendations_ai" using
   * https://cloud.google.com/recommendations-ai/docs/manage-user-events#import
   * (Default for userEvents.import) 2. "user_events_ga360" using
   * https://support.google.com/analytics/answer/3437719?hl=en
   *
   * @param string $dataSchema
   */
  public function setDataSchema($dataSchema)
  {
    $this->dataSchema = $dataSchema;
  }
  /**
   * @return string
   */
  public function getDataSchema()
  {
    return $this->dataSchema;
  }
  /**
   * Required. The BigQuery data set to copy the data from.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * Optional. Intermediate Cloud Storage directory used for the import. Can be
   * specified if one wants to have the BigQuery export to a specific Cloud
   * Storage directory.
   *
   * @param string $gcsStagingDir
   */
  public function setGcsStagingDir($gcsStagingDir)
  {
    $this->gcsStagingDir = $gcsStagingDir;
  }
  /**
   * @return string
   */
  public function getGcsStagingDir()
  {
    return $this->gcsStagingDir;
  }
  /**
   * Optional. The project id (can be project # or id) that the BigQuery source
   * is in. If not specified, inherits the project id from the parent request.
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
   * Required. The BigQuery table to copy the data from.
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
class_alias(GoogleCloudRecommendationengineV1beta1BigQuerySource::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1BigQuerySource');
