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

class GoogleCloudDatalabelingV1beta1LabelImageRequest extends \Google\Model
{
  public const FEATURE_FEATURE_UNSPECIFIED = 'FEATURE_UNSPECIFIED';
  /**
   * Label whole image with one or more of labels.
   */
  public const FEATURE_CLASSIFICATION = 'CLASSIFICATION';
  /**
   * Label image with bounding boxes for labels.
   */
  public const FEATURE_BOUNDING_BOX = 'BOUNDING_BOX';
  /**
   * Label oriented bounding box. The box does not have to be parallel to
   * horizontal line.
   */
  public const FEATURE_ORIENTED_BOUNDING_BOX = 'ORIENTED_BOUNDING_BOX';
  /**
   * Label images with bounding poly. A bounding poly is a plane figure that is
   * bounded by a finite chain of straight line segments closing in a loop.
   */
  public const FEATURE_BOUNDING_POLY = 'BOUNDING_POLY';
  /**
   * Label images with polyline. Polyline is formed by connected line segments
   * which are not in closed form.
   */
  public const FEATURE_POLYLINE = 'POLYLINE';
  /**
   * Label images with segmentation. Segmentation is different from bounding
   * poly since it is more fine-grained, pixel level annotation.
   */
  public const FEATURE_SEGMENTATION = 'SEGMENTATION';
  protected $basicConfigType = GoogleCloudDatalabelingV1beta1HumanAnnotationConfig::class;
  protected $basicConfigDataType = '';
  protected $boundingPolyConfigType = GoogleCloudDatalabelingV1beta1BoundingPolyConfig::class;
  protected $boundingPolyConfigDataType = '';
  /**
   * Required. The type of image labeling task.
   *
   * @var string
   */
  public $feature;
  protected $imageClassificationConfigType = GoogleCloudDatalabelingV1beta1ImageClassificationConfig::class;
  protected $imageClassificationConfigDataType = '';
  protected $polylineConfigType = GoogleCloudDatalabelingV1beta1PolylineConfig::class;
  protected $polylineConfigDataType = '';
  protected $segmentationConfigType = GoogleCloudDatalabelingV1beta1SegmentationConfig::class;
  protected $segmentationConfigDataType = '';

  /**
   * Required. Basic human annotation config.
   *
   * @param GoogleCloudDatalabelingV1beta1HumanAnnotationConfig $basicConfig
   */
  public function setBasicConfig(GoogleCloudDatalabelingV1beta1HumanAnnotationConfig $basicConfig)
  {
    $this->basicConfig = $basicConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1HumanAnnotationConfig
   */
  public function getBasicConfig()
  {
    return $this->basicConfig;
  }
  /**
   * Configuration for bounding box and bounding poly task. One of
   * image_classification_config, bounding_poly_config, polyline_config and
   * segmentation_config are required.
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
   * Required. The type of image labeling task.
   *
   * Accepted values: FEATURE_UNSPECIFIED, CLASSIFICATION, BOUNDING_BOX,
   * ORIENTED_BOUNDING_BOX, BOUNDING_POLY, POLYLINE, SEGMENTATION
   *
   * @param self::FEATURE_* $feature
   */
  public function setFeature($feature)
  {
    $this->feature = $feature;
  }
  /**
   * @return self::FEATURE_*
   */
  public function getFeature()
  {
    return $this->feature;
  }
  /**
   * Configuration for image classification task. One of
   * image_classification_config, bounding_poly_config, polyline_config and
   * segmentation_config are required.
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
   * Configuration for polyline task. One of image_classification_config,
   * bounding_poly_config, polyline_config and segmentation_config are required.
   *
   * @param GoogleCloudDatalabelingV1beta1PolylineConfig $polylineConfig
   */
  public function setPolylineConfig(GoogleCloudDatalabelingV1beta1PolylineConfig $polylineConfig)
  {
    $this->polylineConfig = $polylineConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1PolylineConfig
   */
  public function getPolylineConfig()
  {
    return $this->polylineConfig;
  }
  /**
   * Configuration for segmentation task. One of image_classification_config,
   * bounding_poly_config, polyline_config and segmentation_config are required.
   *
   * @param GoogleCloudDatalabelingV1beta1SegmentationConfig $segmentationConfig
   */
  public function setSegmentationConfig(GoogleCloudDatalabelingV1beta1SegmentationConfig $segmentationConfig)
  {
    $this->segmentationConfig = $segmentationConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1SegmentationConfig
   */
  public function getSegmentationConfig()
  {
    return $this->segmentationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1LabelImageRequest::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1LabelImageRequest');
