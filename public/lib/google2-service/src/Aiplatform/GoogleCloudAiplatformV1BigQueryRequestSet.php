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

class GoogleCloudAiplatformV1BigQueryRequestSet extends \Google\Model
{
  /**
   * Optional. Map of candidate name to candidate response column name. The
   * column will be in evaluation_item.CandidateResponse format.
   *
   * @var string[]
   */
  public $candidateResponseColumns;
  /**
   * Optional. The name of the column that contains the requests to evaluate.
   * This will be in evaluation_item.EvalPrompt format.
   *
   * @var string
   */
  public $promptColumn;
  /**
   * Optional. The name of the column that contains the rubrics. This is in
   * evaluation_rubric.RubricGroup format.
   *
   * @var string
   */
  public $rubricsColumn;
  protected $samplingConfigType = GoogleCloudAiplatformV1BigQueryRequestSetSamplingConfig::class;
  protected $samplingConfigDataType = '';
  /**
   * Required. The URI of a BigQuery table. e.g.
   * bq://projectId.bqDatasetId.bqTableId
   *
   * @var string
   */
  public $uri;

  /**
   * Optional. Map of candidate name to candidate response column name. The
   * column will be in evaluation_item.CandidateResponse format.
   *
   * @param string[] $candidateResponseColumns
   */
  public function setCandidateResponseColumns($candidateResponseColumns)
  {
    $this->candidateResponseColumns = $candidateResponseColumns;
  }
  /**
   * @return string[]
   */
  public function getCandidateResponseColumns()
  {
    return $this->candidateResponseColumns;
  }
  /**
   * Optional. The name of the column that contains the requests to evaluate.
   * This will be in evaluation_item.EvalPrompt format.
   *
   * @param string $promptColumn
   */
  public function setPromptColumn($promptColumn)
  {
    $this->promptColumn = $promptColumn;
  }
  /**
   * @return string
   */
  public function getPromptColumn()
  {
    return $this->promptColumn;
  }
  /**
   * Optional. The name of the column that contains the rubrics. This is in
   * evaluation_rubric.RubricGroup format.
   *
   * @param string $rubricsColumn
   */
  public function setRubricsColumn($rubricsColumn)
  {
    $this->rubricsColumn = $rubricsColumn;
  }
  /**
   * @return string
   */
  public function getRubricsColumn()
  {
    return $this->rubricsColumn;
  }
  /**
   * Optional. The sampling config for the bigquery resource.
   *
   * @param GoogleCloudAiplatformV1BigQueryRequestSetSamplingConfig $samplingConfig
   */
  public function setSamplingConfig(GoogleCloudAiplatformV1BigQueryRequestSetSamplingConfig $samplingConfig)
  {
    $this->samplingConfig = $samplingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQueryRequestSetSamplingConfig
   */
  public function getSamplingConfig()
  {
    return $this->samplingConfig;
  }
  /**
   * Required. The URI of a BigQuery table. e.g.
   * bq://projectId.bqDatasetId.bqTableId
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BigQueryRequestSet::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BigQueryRequestSet');
