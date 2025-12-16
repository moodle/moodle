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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionExportEvaluatedDataItemsConfig extends \Google\Model
{
  /**
   * URI of desired destination BigQuery table. Expected format:
   * `bq://{project_id}:{dataset_id}:{table}` If not specified, then results are
   * exported to the following auto-created BigQuery table: `{project_id}:export
   * _evaluated_examples_{model_name}_{yyyy_MM_dd'T'HH_mm_ss_SSS'Z'}.evaluated_e
   * xamples`
   *
   * @var string
   */
  public $destinationBigqueryUri;
  /**
   * If true and an export destination is specified, then the contents of the
   * destination are overwritten. Otherwise, if the export destination already
   * exists, then the export operation fails.
   *
   * @var bool
   */
  public $overrideExistingTable;

  /**
   * URI of desired destination BigQuery table. Expected format:
   * `bq://{project_id}:{dataset_id}:{table}` If not specified, then results are
   * exported to the following auto-created BigQuery table: `{project_id}:export
   * _evaluated_examples_{model_name}_{yyyy_MM_dd'T'HH_mm_ss_SSS'Z'}.evaluated_e
   * xamples`
   *
   * @param string $destinationBigqueryUri
   */
  public function setDestinationBigqueryUri($destinationBigqueryUri)
  {
    $this->destinationBigqueryUri = $destinationBigqueryUri;
  }
  /**
   * @return string
   */
  public function getDestinationBigqueryUri()
  {
    return $this->destinationBigqueryUri;
  }
  /**
   * If true and an export destination is specified, then the contents of the
   * destination are overwritten. Otherwise, if the export destination already
   * exists, then the export operation fails.
   *
   * @param bool $overrideExistingTable
   */
  public function setOverrideExistingTable($overrideExistingTable)
  {
    $this->overrideExistingTable = $overrideExistingTable;
  }
  /**
   * @return bool
   */
  public function getOverrideExistingTable()
  {
    return $this->overrideExistingTable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionExportEvaluatedDataItemsConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionExportEvaluatedDataItemsConfig');
