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

namespace Google\Service\Logging;

class BigQueryDataset extends \Google\Model
{
  /**
   * Output only. The full resource name of the BigQuery dataset. The DATASET_ID
   * will match the ID of the link, so the link must match the naming
   * restrictions of BigQuery datasets (alphanumeric characters and underscores
   * only).The dataset will have a resource path of
   * "bigquery.googleapis.com/projects/PROJECT_ID/datasets/DATASET_ID"
   *
   * @var string
   */
  public $datasetId;

  /**
   * Output only. The full resource name of the BigQuery dataset. The DATASET_ID
   * will match the ID of the link, so the link must match the naming
   * restrictions of BigQuery datasets (alphanumeric characters and underscores
   * only).The dataset will have a resource path of
   * "bigquery.googleapis.com/projects/PROJECT_ID/datasets/DATASET_ID"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryDataset::class, 'Google_Service_Logging_BigQueryDataset');
