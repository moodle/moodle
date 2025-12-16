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

class GoogleCloudAiplatformV1ModelEvaluation extends \Google\Collection
{
  protected $collection_key = 'sliceDimensions';
  /**
   * Points to a YAML file stored on Google Cloud Storage describing
   * EvaluatedDataItemView.predictions, EvaluatedDataItemView.ground_truths,
   * EvaluatedAnnotation.predictions, and EvaluatedAnnotation.ground_truths. The
   * schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). This field is not
   * populated if there are neither EvaluatedDataItemViews nor
   * EvaluatedAnnotations under this ModelEvaluation.
   *
   * @var string
   */
  public $annotationSchemaUri;
  /**
   * Output only. Timestamp when this ModelEvaluation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Points to a YAML file stored on Google Cloud Storage describing
   * EvaluatedDataItemView.data_item_payload and
   * EvaluatedAnnotation.data_item_payload. The schema is defined as an OpenAPI
   * 3.0.2 [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). This field is not
   * populated if there are neither EvaluatedDataItemViews nor
   * EvaluatedAnnotations under this ModelEvaluation.
   *
   * @var string
   */
  public $dataItemSchemaUri;
  /**
   * The display name of the ModelEvaluation.
   *
   * @var string
   */
  public $displayName;
  protected $explanationSpecsType = GoogleCloudAiplatformV1ModelEvaluationModelEvaluationExplanationSpec::class;
  protected $explanationSpecsDataType = 'array';
  /**
   * The metadata of the ModelEvaluation. For the ModelEvaluation uploaded from
   * Managed Pipeline, metadata contains a structured value with keys of
   * "pipeline_job_id", "evaluation_dataset_type", "evaluation_dataset_path",
   * "row_based_metrics_path".
   *
   * @var array
   */
  public $metadata;
  /**
   * Evaluation metrics of the Model. The schema of the metrics is stored in
   * metrics_schema_uri
   *
   * @var array
   */
  public $metrics;
  /**
   * Points to a YAML file stored on Google Cloud Storage describing the metrics
   * of this ModelEvaluation. The schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject).
   *
   * @var string
   */
  public $metricsSchemaUri;
  protected $modelExplanationType = GoogleCloudAiplatformV1ModelExplanation::class;
  protected $modelExplanationDataType = '';
  /**
   * Output only. The resource name of the ModelEvaluation.
   *
   * @var string
   */
  public $name;
  /**
   * All possible dimensions of ModelEvaluationSlices. The dimensions can be
   * used as the filter of the ModelService.ListModelEvaluationSlices request,
   * in the form of `slice.dimension = `.
   *
   * @var string[]
   */
  public $sliceDimensions;

  /**
   * Points to a YAML file stored on Google Cloud Storage describing
   * EvaluatedDataItemView.predictions, EvaluatedDataItemView.ground_truths,
   * EvaluatedAnnotation.predictions, and EvaluatedAnnotation.ground_truths. The
   * schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). This field is not
   * populated if there are neither EvaluatedDataItemViews nor
   * EvaluatedAnnotations under this ModelEvaluation.
   *
   * @param string $annotationSchemaUri
   */
  public function setAnnotationSchemaUri($annotationSchemaUri)
  {
    $this->annotationSchemaUri = $annotationSchemaUri;
  }
  /**
   * @return string
   */
  public function getAnnotationSchemaUri()
  {
    return $this->annotationSchemaUri;
  }
  /**
   * Output only. Timestamp when this ModelEvaluation was created.
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
   * Points to a YAML file stored on Google Cloud Storage describing
   * EvaluatedDataItemView.data_item_payload and
   * EvaluatedAnnotation.data_item_payload. The schema is defined as an OpenAPI
   * 3.0.2 [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). This field is not
   * populated if there are neither EvaluatedDataItemViews nor
   * EvaluatedAnnotations under this ModelEvaluation.
   *
   * @param string $dataItemSchemaUri
   */
  public function setDataItemSchemaUri($dataItemSchemaUri)
  {
    $this->dataItemSchemaUri = $dataItemSchemaUri;
  }
  /**
   * @return string
   */
  public function getDataItemSchemaUri()
  {
    return $this->dataItemSchemaUri;
  }
  /**
   * The display name of the ModelEvaluation.
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
   * Describes the values of ExplanationSpec that are used for explaining the
   * predicted values on the evaluated data.
   *
   * @param GoogleCloudAiplatformV1ModelEvaluationModelEvaluationExplanationSpec[] $explanationSpecs
   */
  public function setExplanationSpecs($explanationSpecs)
  {
    $this->explanationSpecs = $explanationSpecs;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelEvaluationModelEvaluationExplanationSpec[]
   */
  public function getExplanationSpecs()
  {
    return $this->explanationSpecs;
  }
  /**
   * The metadata of the ModelEvaluation. For the ModelEvaluation uploaded from
   * Managed Pipeline, metadata contains a structured value with keys of
   * "pipeline_job_id", "evaluation_dataset_type", "evaluation_dataset_path",
   * "row_based_metrics_path".
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Evaluation metrics of the Model. The schema of the metrics is stored in
   * metrics_schema_uri
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
   * Points to a YAML file stored on Google Cloud Storage describing the metrics
   * of this ModelEvaluation. The schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
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
   * Aggregated explanation metrics for the Model's prediction output over the
   * data this ModelEvaluation uses. This field is populated only if the Model
   * is evaluated with explanations, and only for AutoML tabular Models.
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
   * Output only. The resource name of the ModelEvaluation.
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
   * All possible dimensions of ModelEvaluationSlices. The dimensions can be
   * used as the filter of the ModelService.ListModelEvaluationSlices request,
   * in the form of `slice.dimension = `.
   *
   * @param string[] $sliceDimensions
   */
  public function setSliceDimensions($sliceDimensions)
  {
    $this->sliceDimensions = $sliceDimensions;
  }
  /**
   * @return string[]
   */
  public function getSliceDimensions()
  {
    return $this->sliceDimensions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelEvaluation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelEvaluation');
