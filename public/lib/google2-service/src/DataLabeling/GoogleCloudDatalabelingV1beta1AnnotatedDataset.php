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

class GoogleCloudDatalabelingV1beta1AnnotatedDataset extends \Google\Collection
{
  public const ANNOTATION_SOURCE_ANNOTATION_SOURCE_UNSPECIFIED = 'ANNOTATION_SOURCE_UNSPECIFIED';
  /**
   * Answer is provided by a human contributor.
   */
  public const ANNOTATION_SOURCE_OPERATOR = 'OPERATOR';
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
  protected $collection_key = 'blockingResources';
  /**
   * Output only. Source of the annotation.
   *
   * @var string
   */
  public $annotationSource;
  /**
   * Output only. Type of the annotation. It is specified when starting labeling
   * task.
   *
   * @var string
   */
  public $annotationType;
  /**
   * Output only. The names of any related resources that are blocking changes
   * to the annotated dataset.
   *
   * @var string[]
   */
  public $blockingResources;
  /**
   * Output only. Number of examples that have annotation in the annotated
   * dataset.
   *
   * @var string
   */
  public $completedExampleCount;
  /**
   * Output only. Time the AnnotatedDataset was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The description of the AnnotatedDataset. It is specified in
   * HumanAnnotationConfig when user starts a labeling task. Maximum of 10000
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The display name of the AnnotatedDataset. It is specified in
   * HumanAnnotationConfig when user starts a labeling task. Maximum of 64
   * characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Number of examples in the annotated dataset.
   *
   * @var string
   */
  public $exampleCount;
  protected $labelStatsType = GoogleCloudDatalabelingV1beta1LabelStats::class;
  protected $labelStatsDataType = '';
  protected $metadataType = GoogleCloudDatalabelingV1beta1AnnotatedDatasetMetadata::class;
  protected $metadataDataType = '';
  /**
   * Output only. AnnotatedDataset resource name in format of:
   * projects/{project_id}/datasets/{dataset_id}/annotatedDatasets/
   * {annotated_dataset_id}
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Source of the annotation.
   *
   * Accepted values: ANNOTATION_SOURCE_UNSPECIFIED, OPERATOR
   *
   * @param self::ANNOTATION_SOURCE_* $annotationSource
   */
  public function setAnnotationSource($annotationSource)
  {
    $this->annotationSource = $annotationSource;
  }
  /**
   * @return self::ANNOTATION_SOURCE_*
   */
  public function getAnnotationSource()
  {
    return $this->annotationSource;
  }
  /**
   * Output only. Type of the annotation. It is specified when starting labeling
   * task.
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
   * Output only. The names of any related resources that are blocking changes
   * to the annotated dataset.
   *
   * @param string[] $blockingResources
   */
  public function setBlockingResources($blockingResources)
  {
    $this->blockingResources = $blockingResources;
  }
  /**
   * @return string[]
   */
  public function getBlockingResources()
  {
    return $this->blockingResources;
  }
  /**
   * Output only. Number of examples that have annotation in the annotated
   * dataset.
   *
   * @param string $completedExampleCount
   */
  public function setCompletedExampleCount($completedExampleCount)
  {
    $this->completedExampleCount = $completedExampleCount;
  }
  /**
   * @return string
   */
  public function getCompletedExampleCount()
  {
    return $this->completedExampleCount;
  }
  /**
   * Output only. Time the AnnotatedDataset was created.
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
   * Output only. The description of the AnnotatedDataset. It is specified in
   * HumanAnnotationConfig when user starts a labeling task. Maximum of 10000
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The display name of the AnnotatedDataset. It is specified in
   * HumanAnnotationConfig when user starts a labeling task. Maximum of 64
   * characters.
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
   * Output only. Number of examples in the annotated dataset.
   *
   * @param string $exampleCount
   */
  public function setExampleCount($exampleCount)
  {
    $this->exampleCount = $exampleCount;
  }
  /**
   * @return string
   */
  public function getExampleCount()
  {
    return $this->exampleCount;
  }
  /**
   * Output only. Per label statistics.
   *
   * @param GoogleCloudDatalabelingV1beta1LabelStats $labelStats
   */
  public function setLabelStats(GoogleCloudDatalabelingV1beta1LabelStats $labelStats)
  {
    $this->labelStats = $labelStats;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1LabelStats
   */
  public function getLabelStats()
  {
    return $this->labelStats;
  }
  /**
   * Output only. Additional information about AnnotatedDataset.
   *
   * @param GoogleCloudDatalabelingV1beta1AnnotatedDatasetMetadata $metadata
   */
  public function setMetadata(GoogleCloudDatalabelingV1beta1AnnotatedDatasetMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1AnnotatedDatasetMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. AnnotatedDataset resource name in format of:
   * projects/{project_id}/datasets/{dataset_id}/annotatedDatasets/
   * {annotated_dataset_id}
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
class_alias(GoogleCloudDatalabelingV1beta1AnnotatedDataset::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1AnnotatedDataset');
