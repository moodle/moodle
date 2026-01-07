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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1EvaluationJobConfig extends \Google\Model
{
  /**
   * Required. Prediction keys that tell Data Labeling Service where to find the
   * data for evaluation in your BigQuery table. When the service samples
   * prediction input and output from your model version and saves it to
   * BigQuery, the data gets stored as JSON strings in the BigQuery table. These
   * keys tell Data Labeling Service how to parse the JSON. You can provide the
   * following entries in this field: * `data_json_key`: the data key for
   * prediction input. You must provide either this key or `reference_json_key`.
   * * `reference_json_key`: the data reference key for prediction input. You
   * must provide either this key or `data_json_key`. * `label_json_key`: the
   * label key for prediction output. Required. * `label_score_json_key`: the
   * score key for prediction output. Required. * `bounding_box_json_key`: the
   * bounding box key for prediction output. Required if your model version
   * perform image object detection. Learn [how to configure prediction
   * keys](/ml-engine/docs/continuous-evaluation/create-job#prediction-keys).
   *
   * @var string[]
   */
  public $bigqueryImportKeys;
  protected $boundingPolyConfigType = GoogleCloudDatalabelingV1beta1BoundingPolyConfig::class;
  protected $boundingPolyConfigDataType = '';
  protected $evaluationConfigType = GoogleCloudDatalabelingV1beta1EvaluationConfig::class;
  protected $evaluationConfigDataType = '';
  protected $evaluationJobAlertConfigType = GoogleCloudDatalabelingV1beta1EvaluationJobAlertConfig::class;
  protected $evaluationJobAlertConfigDataType = '';
  /**
   * Required. The maximum number of predictions to sample and save to BigQuery
   * during each evaluation interval. This limit overrides
   * `example_sample_percentage`: even if the service has not sampled enough
   * predictions to fulfill `example_sample_perecentage` during an interval, it
   * stops sampling predictions when it meets this limit.
   *
   * @var int
   */
  public $exampleCount;
  /**
   * Required. Fraction of predictions to sample and save to BigQuery during
   * each evaluation interval. For example, 0.1 means 10% of predictions served
   * by your model version get saved to BigQuery.
   *
   * @var 
   */
  public $exampleSamplePercentage;
  protected $humanAnnotationConfigType = GoogleCloudDatalabelingV1beta1HumanAnnotationConfig::class;
  protected $humanAnnotationConfigDataType = '';
  protected $imageClassificationConfigType = GoogleCloudDatalabelingV1beta1ImageClassificationConfig::class;
  protected $imageClassificationConfigDataType = '';
  protected $inputConfigType = GoogleCloudDatalabelingV1beta1InputConfig::class;
  protected $inputConfigDataType = '';
  protected $textClassificationConfigType = GoogleCloudDatalabelingV1beta1TextClassificationConfig::class;
  protected $textClassificationConfigDataType = '';

  /**
   * Required. Prediction keys that tell Data Labeling Service where to find the
   * data for evaluation in your BigQuery table. When the service samples
   * prediction input and output from your model version and saves it to
   * BigQuery, the data gets stored as JSON strings in the BigQuery table. These
   * keys tell Data Labeling Service how to parse the JSON. You can provide the
   * following entries in this field: * `data_json_key`: the data key for
   * prediction input. You must provide either this key or `reference_json_key`.
   * * `reference_json_key`: the data reference key for prediction input. You
   * must provide either this key or `data_json_key`. * `label_json_key`: the
   * label key for prediction output. Required. * `label_score_json_key`: the
   * score key for prediction output. Required. * `bounding_box_json_key`: the
   * bounding box key for prediction output. Required if your model version
   * perform image object detection. Learn [how to configure prediction
   * keys](/ml-engine/docs/continuous-evaluation/create-job#prediction-keys).
   *
   * @param string[] $bigqueryImportKeys
   */
  public function setBigqueryImportKeys($bigqueryImportKeys)
  {
    $this->bigqueryImportKeys = $bigqueryImportKeys;
  }
  /**
   * @return string[]
   */
  public function getBigqueryImportKeys()
  {
    return $this->bigqueryImportKeys;
  }
  /**
   * Specify this field if your model version performs image object detection
   * (bounding box detection). `annotationSpecSet` in this configuration must
   * match EvaluationJob.annotationSpecSet.
   *
   * @param GoogleCloudDatalabelingV1beta1BoundingPolyConfig $boundingPolyConfig
   */
  public function setBoundingPolyConfig(GoogleCloudDatalabelingV1beta1BoundingPolyConfig $boundingPolyConfig)
  {
    $this->boundingPolyConfig = $boundingPolyConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1BoundingPolyConfig
   */
  public function getBoundingPolyConfig()
  {
    return $this->boundingPolyConfig;
  }
  /**
   * Required. Details for calculating evaluation metrics and creating
   * Evaulations. If your model version performs image object detection, you
   * must specify the `boundingBoxEvaluationOptions` field within this
   * configuration. Otherwise, provide an empty object for this configuration.
   *
   * @param GoogleCloudDatalabelingV1beta1EvaluationConfig $evaluationConfig
   */
  public function setEvaluationConfig(GoogleCloudDatalabelingV1beta1EvaluationConfig $evaluationConfig)
  {
    $this->evaluationConfig = $evaluationConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1EvaluationConfig
   */
  public function getEvaluationConfig()
  {
    return $this->evaluationConfig;
  }
  /**
   * Optional. Configuration details for evaluation job alerts. Specify this
   * field if you want to receive email alerts if the evaluation job finds that
   * your predictions have low mean average precision during a run.
   *
   * @param GoogleCloudDatalabelingV1beta1EvaluationJobAlertConfig $evaluationJobAlertConfig
   */
  public function setEvaluationJobAlertConfig(GoogleCloudDatalabelingV1beta1EvaluationJobAlertConfig $evaluationJobAlertConfig)
  {
    $this->evaluationJobAlertConfig = $evaluationJobAlertConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1EvaluationJobAlertConfig
   */
  public function getEvaluationJobAlertConfig()
  {
    return $this->evaluationJobAlertConfig;
  }
  /**
   * Required. The maximum number of predictions to sample and save to BigQuery
   * during each evaluation interval. This limit overrides
   * `example_sample_percentage`: even if the service has not sampled enough
   * predictions to fulfill `example_sample_perecentage` during an interval, it
   * stops sampling predictions when it meets this limit.
   *
   * @param int $exampleCount
   */
  public function setExampleCount($exampleCount)
  {
    $this->exampleCount = $exampleCount;
  }
  /**
   * @return int
   */
  public function getExampleCount()
  {
    return $this->exampleCount;
  }
  public function setExampleSamplePercentage($exampleSamplePercentage)
  {
    $this->exampleSamplePercentage = $exampleSamplePercentage;
  }
  public function getExampleSamplePercentage()
  {
    return $this->exampleSamplePercentage;
  }
  /**
   * Optional. Details for human annotation of your data. If you set
   * labelMissingGroundTruth to `true` for this evaluation job, then you must
   * specify this field. If you plan to provide your own ground truth labels,
   * then omit this field. Note that you must create an Instruction resource
   * before you can specify this field. Provide the name of the instruction
   * resource in the `instruction` field within this configuration.
   *
   * @param GoogleCloudDatalabelingV1beta1HumanAnnotationConfig $humanAnnotationConfig
   */
  public function setHumanAnnotationConfig(GoogleCloudDatalabelingV1beta1HumanAnnotationConfig $humanAnnotationConfig)
  {
    $this->humanAnnotationConfig = $humanAnnotationConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1HumanAnnotationConfig
   */
  public function getHumanAnnotationConfig()
  {
    return $this->humanAnnotationConfig;
  }
  /**
   * Specify this field if your model version performs image classification or
   * general classification. `annotationSpecSet` in this configuration must
   * match EvaluationJob.annotationSpecSet. `allowMultiLabel` in this
   * configuration must match `classificationMetadata.isMultiLabel` in
   * input_config.
   *
   * @param GoogleCloudDatalabelingV1beta1ImageClassificationConfig $imageClassificationConfig
   */
  public function setImageClassificationConfig(GoogleCloudDatalabelingV1beta1ImageClassificationConfig $imageClassificationConfig)
  {
    $this->imageClassificationConfig = $imageClassificationConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1ImageClassificationConfig
   */
  public function getImageClassificationConfig()
  {
    return $this->imageClassificationConfig;
  }
  /**
   * Rquired. Details for the sampled prediction input. Within this
   * configuration, there are requirements for several fields: * `dataType` must
   * be one of `IMAGE`, `TEXT`, or `GENERAL_DATA`. * `annotationType` must be
   * one of `IMAGE_CLASSIFICATION_ANNOTATION`, `TEXT_CLASSIFICATION_ANNOTATION`,
   * `GENERAL_CLASSIFICATION_ANNOTATION`, or `IMAGE_BOUNDING_BOX_ANNOTATION`
   * (image object detection). * If your machine learning model performs
   * classification, you must specify `classificationMetadata.isMultiLabel`. *
   * You must specify `bigquerySource` (not `gcsSource`).
   *
   * @param GoogleCloudDatalabelingV1beta1InputConfig $inputConfig
   */
  public function setInputConfig(GoogleCloudDatalabelingV1beta1InputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1InputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
  /**
   * Specify this field if your model version performs text classification.
   * `annotationSpecSet` in this configuration must match
   * EvaluationJob.annotationSpecSet. `allowMultiLabel` in this configuration
   * must match `classificationMetadata.isMultiLabel` in input_config.
   *
   * @param GoogleCloudDatalabelingV1beta1TextClassificationConfig $textClassificationConfig
   */
  public function setTextClassificationConfig(GoogleCloudDatalabelingV1beta1TextClassificationConfig $textClassificationConfig)
  {
    $this->textClassificationConfig = $textClassificationConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1TextClassificationConfig
   */
  public function getTextClassificationConfig()
  {
    return $this->textClassificationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1EvaluationJobConfig::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1EvaluationJobConfig');
