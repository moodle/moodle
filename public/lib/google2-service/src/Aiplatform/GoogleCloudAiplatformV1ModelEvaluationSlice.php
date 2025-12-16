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

class GoogleCloudAiplatformV1ModelEvaluationSlice extends \Google\Model
{
  /**
   * Output only. Timestamp when this ModelEvaluationSlice was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Sliced evaluation metrics of the Model. The schema of the
   * metrics is stored in metrics_schema_uri
   *
   * @var array
   */
  public $metrics;
  /**
   * Output only. Points to a YAML file stored on Google Cloud Storage
   * describing the metrics of this ModelEvaluationSlice. The schema is defined
   * as an OpenAPI 3.0.2 [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject).
   *
   * @var string
   */
  public $metricsSchemaUri;
  protected $modelExplanationType = GoogleCloudAiplatformV1ModelExplanation::class;
  protected $modelExplanationDataType = '';
  /**
   * Output only. The resource name of the ModelEvaluationSlice.
   *
   * @var string
   */
  public $name;
  protected $sliceType = GoogleCloudAiplatformV1ModelEvaluationSliceSlice::class;
  protected $sliceDataType = '';

  /**
   * Output only. Timestamp when this ModelEvaluationSlice was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Sliced evaluation metrics of the Model. The schema of the
   * metrics is stored in metrics_schema_uri
   *
   * @param array $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return array
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Output only. Points to a YAML file stored on Google Cloud Storage
   * describing the metrics of this ModelEvaluationSlice. The schema is defined
   * as an OpenAPI 3.0.2 [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject).
   *
   * @param string $metricsSchemaUri
   */
  public function setMetricsSchemaUri($metricsSchemaUri)
  {
    $this->metricsSchemaUri = $metricsSchemaUri;
  }
  /**
   * @return string
   */
  public function getMetricsSchemaUri()
  {
    return $this->metricsSchemaUri;
  }
  /**
   * Output only. Aggregated explanation metrics for the Model's prediction
   * output over the data this ModelEvaluation uses. This field is populated
   * only if the Model is evaluated with explanations, and only for tabular
   * Models.
   *
   * @param GoogleCloudAiplatformV1ModelExplanation $modelExplanation
   */
  public function setModelExplanation(GoogleCloudAiplatformV1ModelExplanation $modelExplanation)
  {
    $this->modelExplanation = $modelExplanation;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelExplanation
   */
  public function getModelExplanation()
  {
    return $this->modelExplanation;
  }
  /**
   * Output only. The resource name of the ModelEvaluationSlice.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The slice of the test data that is used to evaluate the Model.
   *
   * @param GoogleCloudAiplatformV1ModelEvaluationSliceSlice $slice
   */
  public function setSlice(GoogleCloudAiplatformV1ModelEvaluationSliceSlice $slice)
  {
    $this->slice = $slice;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelEvaluationSliceSlice
   */
  public function getSlice()
  {
    return $this->slice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelEvaluationSlice::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelEvaluationSlice');
