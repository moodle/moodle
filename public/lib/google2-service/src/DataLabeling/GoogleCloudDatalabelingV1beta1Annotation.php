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

class GoogleCloudDatalabelingV1beta1Annotation extends \Google\Model
{
  public const ANNOTATION_SENTIMENT_ANNOTATION_SENTIMENT_UNSPECIFIED = 'ANNOTATION_SENTIMENT_UNSPECIFIED';
  /**
   * This annotation describes negatively about the data.
   */
  public const ANNOTATION_SENTIMENT_NEGATIVE = 'NEGATIVE';
  /**
   * This label describes positively about the data.
   */
  public const ANNOTATION_SENTIMENT_POSITIVE = 'POSITIVE';
  public const ANNOTATION_SOURCE_ANNOTATION_SOURCE_UNSPECIFIED = 'ANNOTATION_SOURCE_UNSPECIFIED';
  /**
   * Answer is provided by a human contributor.
   */
  public const ANNOTATION_SOURCE_OPERATOR = 'OPERATOR';
  protected $annotationMetadataType = GoogleCloudDatalabelingV1beta1AnnotationMetadata::class;
  protected $annotationMetadataDataType = '';
  /**
   * Output only. Sentiment for this annotation.
   *
   * @var string
   */
  public $annotationSentiment;
  /**
   * Output only. The source of the annotation.
   *
   * @var string
   */
  public $annotationSource;
  protected $annotationValueType = GoogleCloudDatalabelingV1beta1AnnotationValue::class;
  protected $annotationValueDataType = '';
  /**
   * Output only. Unique name of this annotation, format is: projects/{project_i
   * d}/datasets/{dataset_id}/annotatedDatasets/{annotated_dataset}/examples/{ex
   * ample_id}/annotations/{annotation_id}
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Annotation metadata, including information like votes for
   * labels.
   *
   * @param GoogleCloudDatalabelingV1beta1AnnotationMetadata $annotationMetadata
   */
  public function setAnnotationMetadata(GoogleCloudDatalabelingV1beta1AnnotationMetadata $annotationMetadata)
  {
    $this->annotationMetadata = $annotationMetadata;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1AnnotationMetadata
   */
  public function getAnnotationMetadata()
  {
    return $this->annotationMetadata;
  }
  /**
   * Output only. Sentiment for this annotation.
   *
   * Accepted values: ANNOTATION_SENTIMENT_UNSPECIFIED, NEGATIVE, POSITIVE
   *
   * @param self::ANNOTATION_SENTIMENT_* $annotationSentiment
   */
  public function setAnnotationSentiment($annotationSentiment)
  {
    $this->annotationSentiment = $annotationSentiment;
  }
  /**
   * @return self::ANNOTATION_SENTIMENT_*
   */
  public function getAnnotationSentiment()
  {
    return $this->annotationSentiment;
  }
  /**
   * Output only. The source of the annotation.
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
   * Output only. This is the actual annotation value, e.g classification,
   * bounding box values are stored here.
   *
   * @param GoogleCloudDatalabelingV1beta1AnnotationValue $annotationValue
   */
  public function setAnnotationValue(GoogleCloudDatalabelingV1beta1AnnotationValue $annotationValue)
  {
    $this->annotationValue = $annotationValue;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1AnnotationValue
   */
  public function getAnnotationValue()
  {
    return $this->annotationValue;
  }
  /**
   * Output only. Unique name of this annotation, format is: projects/{project_i
   * d}/datasets/{dataset_id}/annotatedDatasets/{annotated_dataset}/examples/{ex
   * ample_id}/annotations/{annotation_id}
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
class_alias(GoogleCloudDatalabelingV1beta1Annotation::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1Annotation');
