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

class GoogleCloudDatalabelingV1beta1Evaluation extends \Google\Model
{
  public const ANNOTATION_TYPE_ANNOTATION_TYPE_UNSPECIFIED = 'ANNOTATION_TYPE_UNSPECIFIED';
  /**
   * Classification annotations in an image. Allowed for continuous evaluation.
   */
  public const ANNOTATION_TYPE_IMAGE_CLASSIFICATION_ANNOTATION = 'IMAGE_CLASSIFICATION_ANNOTATION';
  /**
   * Bounding box annotations in an image. A form of image object detection.
   * Allowed for continuous evaluation.
   */
  public const ANNOTATION_TYPE_IMAGE_BOUNDING_BOX_ANNOTATION = 'IMAGE_BOUNDING_BOX_ANNOTATION';
  /**
   * Oriented bounding box. The box does not have to be parallel to horizontal
   * line.
   */
  public const ANNOTATION_TYPE_IMAGE_ORIENTED_BOUNDING_BOX_ANNOTATION = 'IMAGE_ORIENTED_BOUNDING_BOX_ANNOTATION';
  /**
   * Bounding poly annotations in an image.
   */
  public const ANNOTATION_TYPE_IMAGE_BOUNDING_POLY_ANNOTATION = 'IMAGE_BOUNDING_POLY_ANNOTATION';
  /**
   * Polyline annotations in an image.
   */
  public const ANNOTATION_TYPE_IMAGE_POLYLINE_ANNOTATION = 'IMAGE_POLYLINE_ANNOTATION';
  /**
   * Segmentation annotations in an image.
   */
  public const ANNOTATION_TYPE_IMAGE_SEGMENTATION_ANNOTATION = 'IMAGE_SEGMENTATION_ANNOTATION';
  /**
   * Classification annotations in video shots.
   */
  public const ANNOTATION_TYPE_VIDEO_SHOTS_CLASSIFICATION_ANNOTATION = 'VIDEO_SHOTS_CLASSIFICATION_ANNOTATION';
  /**
   * Video object tracking annotation.
   */
  public const ANNOTATION_TYPE_VIDEO_OBJECT_TRACKING_ANNOTATION = 'VIDEO_OBJECT_TRACKING_ANNOTATION';
  /**
   * Video object detection annotation.
   */
  public const ANNOTATION_TYPE_VIDEO_OBJECT_DETECTION_ANNOTATION = 'VIDEO_OBJECT_DETECTION_ANNOTATION';
  /**
   * Video event annotation.
   */
  public const ANNOTATION_TYPE_VIDEO_EVENT_ANNOTATION = 'VIDEO_EVENT_ANNOTATION';
  /**
   * Classification for text. Allowed for continuous evaluation.
   */
  public const ANNOTATION_TYPE_TEXT_CLASSIFICATION_ANNOTATION = 'TEXT_CLASSIFICATION_ANNOTATION';
  /**
   * Entity extraction for text.
   */
  public const ANNOTATION_TYPE_TEXT_ENTITY_EXTRACTION_ANNOTATION = 'TEXT_ENTITY_EXTRACTION_ANNOTATION';
  /**
   * General classification. Allowed for continuous evaluation.
   */
  public const ANNOTATION_TYPE_GENERAL_CLASSIFICATION_ANNOTATION = 'GENERAL_CLASSIFICATION_ANNOTATION';
  /**
   * Output only. Type of task that the model version being evaluated performs,
   * as defined in the evaluationJobConfig.inputConfig.annotationType field of
   * the evaluation job that created this evaluation.
   *
   * @var string
   */
  public $annotationType;
  protected $configType = GoogleCloudDatalabelingV1beta1EvaluationConfig::class;
  protected $configDataType = '';
  /**
   * Output only. Timestamp for when this evaluation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The number of items in the ground truth dataset that were used
   * for this evaluation. Only populated when the evaulation is for certain
   * AnnotationTypes.
   *
   * @var string
   */
  public $evaluatedItemCount;
  /**
   * Output only. Timestamp for when the evaluation job that created this
   * evaluation ran.
   *
   * @var string
   */
  public $evaluationJobRunTime;
  protected $evaluationMetricsType = GoogleCloudDatalabelingV1beta1EvaluationMetrics::class;
  protected $evaluationMetricsDataType = '';
  /**
   * Output only. Resource name of an evaluation. The name has the following
   * format: "projects/{project_id}/datasets/{dataset_id}/evaluations/
   * {evaluation_id}'
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Type of task that the model version being evaluated performs,
   * as defined in the evaluationJobConfig.inputConfig.annotationType field of
   * the evaluation job that created this evaluation.
   *
   * Accepted values: ANNOTATION_TYPE_UNSPECIFIED,
   * IMAGE_CLASSIFICATION_ANNOTATION, IMAGE_BOUNDING_BOX_ANNOTATION,
   * IMAGE_ORIENTED_BOUNDING_BOX_ANNOTATION, IMAGE_BOUNDING_POLY_ANNOTATION,
   * IMAGE_POLYLINE_ANNOTATION, IMAGE_SEGMENTATION_ANNOTATION,
   * VIDEO_SHOTS_CLASSIFICATION_ANNOTATION, VIDEO_OBJECT_TRACKING_ANNOTATION,
   * VIDEO_OBJECT_DETECTION_ANNOTATION, VIDEO_EVENT_ANNOTATION,
   * TEXT_CLASSIFICATION_ANNOTATION, TEXT_ENTITY_EXTRACTION_ANNOTATION,
   * GENERAL_CLASSIFICATION_ANNOTATION
   *
   * @param self::ANNOTATION_TYPE_* $annotationType
   */
  public function setAnnotationType($annotationType)
  {
    $this->annotationType = $annotationType;
  }
  /**
   * @return self::ANNOTATION_TYPE_*
   */
  public function getAnnotationType()
  {
    return $this->annotationType;
  }
  /**
   * Output only. Options used in the evaluation job that created this
   * evaluation.
   *
   * @param GoogleCloudDatalabelingV1beta1EvaluationConfig $config
   */
  public function setConfig(GoogleCloudDatalabelingV1beta1EvaluationConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1EvaluationConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. Timestamp for when this evaluation was created.
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
   * Output only. The number of items in the ground truth dataset that were used
   * for this evaluation. Only populated when the evaulation is for certain
   * AnnotationTypes.
   *
   * @param string $evaluatedItemCount
   */
  public function setEvaluatedItemCount($evaluatedItemCount)
  {
    $this->evaluatedItemCount = $evaluatedItemCount;
  }
  /**
   * @return string
   */
  public function getEvaluatedItemCount()
  {
    return $this->evaluatedItemCount;
  }
  /**
   * Output only. Timestamp for when the evaluation job that created this
   * evaluation ran.
   *
   * @param string $evaluationJobRunTime
   */
  public function setEvaluationJobRunTime($evaluationJobRunTime)
  {
    $this->evaluationJobRunTime = $evaluationJobRunTime;
  }
  /**
   * @return string
   */
  public function getEvaluationJobRunTime()
  {
    return $this->evaluationJobRunTime;
  }
  /**
   * Output only. Metrics comparing predictions to ground truth labels.
   *
   * @param GoogleCloudDatalabelingV1beta1EvaluationMetrics $evaluationMetrics
   */
  public function setEvaluationMetrics(GoogleCloudDatalabelingV1beta1EvaluationMetrics $evaluationMetrics)
  {
    $this->evaluationMetrics = $evaluationMetrics;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1EvaluationMetrics
   */
  public function getEvaluationMetrics()
  {
    return $this->evaluationMetrics;
  }
  /**
   * Output only. Resource name of an evaluation. The name has the following
   * format: "projects/{project_id}/datasets/{dataset_id}/evaluations/
   * {evaluation_id}'
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1Evaluation::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1Evaluation');
