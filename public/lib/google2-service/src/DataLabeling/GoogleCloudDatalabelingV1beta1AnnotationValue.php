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

class GoogleCloudDatalabelingV1beta1AnnotationValue extends \Google\Model
{
  protected $imageBoundingPolyAnnotationType = GoogleCloudDatalabelingV1beta1ImageBoundingPolyAnnotation::class;
  protected $imageBoundingPolyAnnotationDataType = '';
  protected $imageClassificationAnnotationType = GoogleCloudDatalabelingV1beta1ImageClassificationAnnotation::class;
  protected $imageClassificationAnnotationDataType = '';
  protected $imagePolylineAnnotationType = GoogleCloudDatalabelingV1beta1ImagePolylineAnnotation::class;
  protected $imagePolylineAnnotationDataType = '';
  protected $imageSegmentationAnnotationType = GoogleCloudDatalabelingV1beta1ImageSegmentationAnnotation::class;
  protected $imageSegmentationAnnotationDataType = '';
  protected $textClassificationAnnotationType = GoogleCloudDatalabelingV1beta1TextClassificationAnnotation::class;
  protected $textClassificationAnnotationDataType = '';
  protected $textEntityExtractionAnnotationType = GoogleCloudDatalabelingV1beta1TextEntityExtractionAnnotation::class;
  protected $textEntityExtractionAnnotationDataType = '';
  protected $videoClassificationAnnotationType = GoogleCloudDatalabelingV1beta1VideoClassificationAnnotation::class;
  protected $videoClassificationAnnotationDataType = '';
  protected $videoEventAnnotationType = GoogleCloudDatalabelingV1beta1VideoEventAnnotation::class;
  protected $videoEventAnnotationDataType = '';
  protected $videoObjectTrackingAnnotationType = GoogleCloudDatalabelingV1beta1VideoObjectTrackingAnnotation::class;
  protected $videoObjectTrackingAnnotationDataType = '';

  /**
   * Annotation value for image bounding box, oriented bounding box and polygon
   * cases.
   *
   * @param GoogleCloudDatalabelingV1beta1ImageBoundingPolyAnnotation $imageBoundingPolyAnnotation
   */
  public function setImageBoundingPolyAnnotation(GoogleCloudDatalabelingV1beta1ImageBoundingPolyAnnotation $imageBoundingPolyAnnotation)
  {
    $this->imageBoundingPolyAnnotation = $imageBoundingPolyAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1ImageBoundingPolyAnnotation
   */
  public function getImageBoundingPolyAnnotation()
  {
    return $this->imageBoundingPolyAnnotation;
  }
  /**
   * Annotation value for image classification case.
   *
   * @param GoogleCloudDatalabelingV1beta1ImageClassificationAnnotation $imageClassificationAnnotation
   */
  public function setImageClassificationAnnotation(GoogleCloudDatalabelingV1beta1ImageClassificationAnnotation $imageClassificationAnnotation)
  {
    $this->imageClassificationAnnotation = $imageClassificationAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1ImageClassificationAnnotation
   */
  public function getImageClassificationAnnotation()
  {
    return $this->imageClassificationAnnotation;
  }
  /**
   * Annotation value for image polyline cases. Polyline here is different from
   * BoundingPoly. It is formed by line segments connected to each other but not
   * closed form(Bounding Poly). The line segments can cross each other.
   *
   * @param GoogleCloudDatalabelingV1beta1ImagePolylineAnnotation $imagePolylineAnnotation
   */
  public function setImagePolylineAnnotation(GoogleCloudDatalabelingV1beta1ImagePolylineAnnotation $imagePolylineAnnotation)
  {
    $this->imagePolylineAnnotation = $imagePolylineAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1ImagePolylineAnnotation
   */
  public function getImagePolylineAnnotation()
  {
    return $this->imagePolylineAnnotation;
  }
  /**
   * Annotation value for image segmentation.
   *
   * @param GoogleCloudDatalabelingV1beta1ImageSegmentationAnnotation $imageSegmentationAnnotation
   */
  public function setImageSegmentationAnnotation(GoogleCloudDatalabelingV1beta1ImageSegmentationAnnotation $imageSegmentationAnnotation)
  {
    $this->imageSegmentationAnnotation = $imageSegmentationAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1ImageSegmentationAnnotation
   */
  public function getImageSegmentationAnnotation()
  {
    return $this->imageSegmentationAnnotation;
  }
  /**
   * Annotation value for text classification case.
   *
   * @param GoogleCloudDatalabelingV1beta1TextClassificationAnnotation $textClassificationAnnotation
   */
  public function setTextClassificationAnnotation(GoogleCloudDatalabelingV1beta1TextClassificationAnnotation $textClassificationAnnotation)
  {
    $this->textClassificationAnnotation = $textClassificationAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1TextClassificationAnnotation
   */
  public function getTextClassificationAnnotation()
  {
    return $this->textClassificationAnnotation;
  }
  /**
   * Annotation value for text entity extraction case.
   *
   * @param GoogleCloudDatalabelingV1beta1TextEntityExtractionAnnotation $textEntityExtractionAnnotation
   */
  public function setTextEntityExtractionAnnotation(GoogleCloudDatalabelingV1beta1TextEntityExtractionAnnotation $textEntityExtractionAnnotation)
  {
    $this->textEntityExtractionAnnotation = $textEntityExtractionAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1TextEntityExtractionAnnotation
   */
  public function getTextEntityExtractionAnnotation()
  {
    return $this->textEntityExtractionAnnotation;
  }
  /**
   * Annotation value for video classification case.
   *
   * @param GoogleCloudDatalabelingV1beta1VideoClassificationAnnotation $videoClassificationAnnotation
   */
  public function setVideoClassificationAnnotation(GoogleCloudDatalabelingV1beta1VideoClassificationAnnotation $videoClassificationAnnotation)
  {
    $this->videoClassificationAnnotation = $videoClassificationAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1VideoClassificationAnnotation
   */
  public function getVideoClassificationAnnotation()
  {
    return $this->videoClassificationAnnotation;
  }
  /**
   * Annotation value for video event case.
   *
   * @param GoogleCloudDatalabelingV1beta1VideoEventAnnotation $videoEventAnnotation
   */
  public function setVideoEventAnnotation(GoogleCloudDatalabelingV1beta1VideoEventAnnotation $videoEventAnnotation)
  {
    $this->videoEventAnnotation = $videoEventAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1VideoEventAnnotation
   */
  public function getVideoEventAnnotation()
  {
    return $this->videoEventAnnotation;
  }
  /**
   * Annotation value for video object detection and tracking case.
   *
   * @param GoogleCloudDatalabelingV1beta1VideoObjectTrackingAnnotation $videoObjectTrackingAnnotation
   */
  public function setVideoObjectTrackingAnnotation(GoogleCloudDatalabelingV1beta1VideoObjectTrackingAnnotation $videoObjectTrackingAnnotation)
  {
    $this->videoObjectTrackingAnnotation = $videoObjectTrackingAnnotation;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1VideoObjectTrackingAnnotation
   */
  public function getVideoObjectTrackingAnnotation()
  {
    return $this->videoObjectTrackingAnnotation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1AnnotationValue::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1AnnotationValue');
