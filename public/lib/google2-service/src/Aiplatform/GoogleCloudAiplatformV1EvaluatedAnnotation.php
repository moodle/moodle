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

class GoogleCloudAiplatformV1EvaluatedAnnotation extends \Google\Collection
{
  /**
   * Invalid value.
   */
  public const TYPE_EVALUATED_ANNOTATION_TYPE_UNSPECIFIED = 'EVALUATED_ANNOTATION_TYPE_UNSPECIFIED';
  /**
   * The EvaluatedAnnotation is a true positive. It has a prediction created by
   * the Model and a ground truth Annotation which the prediction matches.
   */
  public const TYPE_TRUE_POSITIVE = 'TRUE_POSITIVE';
  /**
   * The EvaluatedAnnotation is false positive. It has a prediction created by
   * the Model which does not match any ground truth annotation.
   */
  public const TYPE_FALSE_POSITIVE = 'FALSE_POSITIVE';
  /**
   * The EvaluatedAnnotation is false negative. It has a ground truth annotation
   * which is not matched by any of the model created predictions.
   */
  public const TYPE_FALSE_NEGATIVE = 'FALSE_NEGATIVE';
  protected $collection_key = 'predictions';
  /**
   * Output only. The data item payload that the Model predicted this
   * EvaluatedAnnotation on.
   *
   * @var array
   */
  public $dataItemPayload;
  protected $errorAnalysisAnnotationsType = GoogleCloudAiplatformV1ErrorAnalysisAnnotation::class;
  protected $errorAnalysisAnnotationsDataType = 'array';
  /**
   * Output only. ID of the EvaluatedDataItemView under the same ancestor
   * ModelEvaluation. The EvaluatedDataItemView consists of all ground truths
   * and predictions on data_item_payload.
   *
   * @var string
   */
  public $evaluatedDataItemViewId;
  protected $explanationsType = GoogleCloudAiplatformV1EvaluatedAnnotationExplanation::class;
  protected $explanationsDataType = 'array';
  /**
   * Output only. The ground truth Annotations, i.e. the Annotations that exist
   * in the test data the Model is evaluated on. For true positive, there is one
   * and only one ground truth annotation, which matches the only prediction in
   * predictions. For false positive, there are zero or more ground truth
   * annotations that are similar to the only prediction in predictions, but not
   * enough for a match. For false negative, there is one and only one ground
   * truth annotation, which doesn't match any predictions created by the model.
   * The schema of the ground truth is stored in
   * ModelEvaluation.annotation_schema_uri
   *
   * @var array[]
   */
  public $groundTruths;
  /**
   * Output only. The model predicted annotations. For true positive, there is
   * one and only one prediction, which matches the only one ground truth
   * annotation in ground_truths. For false positive, there is one and only one
   * prediction, which doesn't match any ground truth annotation of the
   * corresponding data_item_view_id. For false negative, there are zero or more
   * predictions which are similar to the only ground truth annotation in
   * ground_truths but not enough for a match. The schema of the prediction is
   * stored in ModelEvaluation.annotation_schema_uri
   *
   * @var array[]
   */
  public $predictions;
  /**
   * Output only. Type of the EvaluatedAnnotation.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The data item payload that the Model predicted this
   * EvaluatedAnnotation on.
   *
   * @param array $dataItemPayload
   */
  public function setDataItemPayload($dataItemPayload)
  {
    $this->dataItemPayload = $dataItemPayload;
  }
  /**
   * @return array
   */
  public function getDataItemPayload()
  {
    return $this->dataItemPayload;
  }
  /**
   * Annotations of model error analysis results.
   *
   * @param GoogleCloudAiplatformV1ErrorAnalysisAnnotation[] $errorAnalysisAnnotations
   */
  public function setErrorAnalysisAnnotations($errorAnalysisAnnotations)
  {
    $this->errorAnalysisAnnotations = $errorAnalysisAnnotations;
  }
  /**
   * @return GoogleCloudAiplatformV1ErrorAnalysisAnnotation[]
   */
  public function getErrorAnalysisAnnotations()
  {
    return $this->errorAnalysisAnnotations;
  }
  /**
   * Output only. ID of the EvaluatedDataItemView under the same ancestor
   * ModelEvaluation. The EvaluatedDataItemView consists of all ground truths
   * and predictions on data_item_payload.
   *
   * @param string $evaluatedDataItemViewId
   */
  public function setEvaluatedDataItemViewId($evaluatedDataItemViewId)
  {
    $this->evaluatedDataItemViewId = $evaluatedDataItemViewId;
  }
  /**
   * @return string
   */
  public function getEvaluatedDataItemViewId()
  {
    return $this->evaluatedDataItemViewId;
  }
  /**
   * Explanations of predictions. Each element of the explanations indicates the
   * explanation for one explanation Method. The attributions list in the
   * EvaluatedAnnotationExplanation.explanation object corresponds to the
   * predictions list. For example, the second element in the attributions list
   * explains the second element in the predictions list.
   *
   * @param GoogleCloudAiplatformV1EvaluatedAnnotationExplanation[] $explanations
   */
  public function setExplanations($explanations)
  {
    $this->explanations = $explanations;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluatedAnnotationExplanation[]
   */
  public function getExplanations()
  {
    return $this->explanations;
  }
  /**
   * Output only. The ground truth Annotations, i.e. the Annotations that exist
   * in the test data the Model is evaluated on. For true positive, there is one
   * and only one ground truth annotation, which matches the only prediction in
   * predictions. For false positive, there are zero or more ground truth
   * annotations that are similar to the only prediction in predictions, but not
   * enough for a match. For false negative, there is one and only one ground
   * truth annotation, which doesn't match any predictions created by the model.
   * The schema of the ground truth is stored in
   * ModelEvaluation.annotation_schema_uri
   *
   * @param array[] $groundTruths
   */
  public function setGroundTruths($groundTruths)
  {
    $this->groundTruths = $groundTruths;
  }
  /**
   * @return array[]
   */
  public function getGroundTruths()
  {
    return $this->groundTruths;
  }
  /**
   * Output only. The model predicted annotations. For true positive, there is
   * one and only one prediction, which matches the only one ground truth
   * annotation in ground_truths. For false positive, there is one and only one
   * prediction, which doesn't match any ground truth annotation of the
   * corresponding data_item_view_id. For false negative, there are zero or more
   * predictions which are similar to the only ground truth annotation in
   * ground_truths but not enough for a match. The schema of the prediction is
   * stored in ModelEvaluation.annotation_schema_uri
   *
   * @param array[] $predictions
   */
  public function setPredictions($predictions)
  {
    $this->predictions = $predictions;
  }
  /**
   * @return array[]
   */
  public function getPredictions()
  {
    return $this->predictions;
  }
  /**
   * Output only. Type of the EvaluatedAnnotation.
   *
   * Accepted values: EVALUATED_ANNOTATION_TYPE_UNSPECIFIED, TRUE_POSITIVE,
   * FALSE_POSITIVE, FALSE_NEGATIVE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluatedAnnotation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluatedAnnotation');
