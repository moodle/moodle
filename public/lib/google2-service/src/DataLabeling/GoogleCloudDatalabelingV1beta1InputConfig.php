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

class GoogleCloudDatalabelingV1beta1InputConfig extends \Google\Model
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
   * Data type is unspecified.
   */
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Allowed for continuous evaluation.
   */
  public const DATA_TYPE_IMAGE = 'IMAGE';
  /**
   * Video data type.
   */
  public const DATA_TYPE_VIDEO = 'VIDEO';
  /**
   * Allowed for continuous evaluation.
   */
  public const DATA_TYPE_TEXT = 'TEXT';
  /**
   * Allowed for continuous evaluation.
   */
  public const DATA_TYPE_GENERAL_DATA = 'GENERAL_DATA';
  /**
   * Optional. The type of annotation to be performed on this data. You must
   * specify this field if you are using this InputConfig in an EvaluationJob.
   *
   * @var string
   */
  public $annotationType;
  protected $bigquerySourceType = GoogleCloudDatalabelingV1beta1BigQuerySource::class;
  protected $bigquerySourceDataType = '';
  protected $classificationMetadataType = GoogleCloudDatalabelingV1beta1ClassificationMetadata::class;
  protected $classificationMetadataDataType = '';
  /**
   * Required. Data type must be specifed when user tries to import data.
   *
   * @var string
   */
  public $dataType;
  protected $gcsSourceType = GoogleCloudDatalabelingV1beta1GcsSource::class;
  protected $gcsSourceDataType = '';
  protected $textMetadataType = GoogleCloudDatalabelingV1beta1TextMetadata::class;
  protected $textMetadataDataType = '';

  /**
   * Optional. The type of annotation to be performed on this data. You must
   * specify this field if you are using this InputConfig in an EvaluationJob.
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
   * Source located in BigQuery. You must specify this field if you are using
   * this InputConfig in an EvaluationJob.
   *
   * @param GoogleCloudDatalabelingV1beta1BigQuerySource $bigquerySource
   */
  public function setBigquerySource(GoogleCloudDatalabelingV1beta1BigQuerySource $bigquerySource)
  {
    $this->bigquerySource = $bigquerySource;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1BigQuerySource
   */
  public function getBigquerySource()
  {
    return $this->bigquerySource;
  }
  /**
   * Optional. Metadata about annotations for the input. You must specify this
   * field if you are using this InputConfig in an EvaluationJob for a model
   * version that performs classification.
   *
   * @param GoogleCloudDatalabelingV1beta1ClassificationMetadata $classificationMetadata
   */
  public function setClassificationMetadata(GoogleCloudDatalabelingV1beta1ClassificationMetadata $classificationMetadata)
  {
    $this->classificationMetadata = $classificationMetadata;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1ClassificationMetadata
   */
  public function getClassificationMetadata()
  {
    return $this->classificationMetadata;
  }
  /**
   * Required. Data type must be specifed when user tries to import data.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, IMAGE, VIDEO, TEXT, GENERAL_DATA
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Source located in Cloud Storage.
   *
   * @param GoogleCloudDatalabelingV1beta1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudDatalabelingV1beta1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Required for text import, as language code must be specified.
   *
   * @param GoogleCloudDatalabelingV1beta1TextMetadata $textMetadata
   */
  public function setTextMetadata(GoogleCloudDatalabelingV1beta1TextMetadata $textMetadata)
  {
    $this->textMetadata = $textMetadata;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1TextMetadata
   */
  public function getTextMetadata()
  {
    return $this->textMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1InputConfig::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1InputConfig');
