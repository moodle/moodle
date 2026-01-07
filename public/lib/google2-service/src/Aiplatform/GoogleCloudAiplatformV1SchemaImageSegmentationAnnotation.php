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

class GoogleCloudAiplatformV1SchemaImageSegmentationAnnotation extends \Google\Model
{
  protected $maskAnnotationType = GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationMaskAnnotation::class;
  protected $maskAnnotationDataType = '';
  protected $polygonAnnotationType = GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationPolygonAnnotation::class;
  protected $polygonAnnotationDataType = '';
  protected $polylineAnnotationType = GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationPolylineAnnotation::class;
  protected $polylineAnnotationDataType = '';

  /**
   * Mask based segmentation annotation. Only one mask annotation can exist for
   * one image.
   *
   * @param GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationMaskAnnotation $maskAnnotation
   */
  public function setMaskAnnotation(GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationMaskAnnotation $maskAnnotation)
  {
    $this->maskAnnotation = $maskAnnotation;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationMaskAnnotation
   */
  public function getMaskAnnotation()
  {
    return $this->maskAnnotation;
  }
  /**
   * Polygon annotation.
   *
   * @param GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationPolygonAnnotation $polygonAnnotation
   */
  public function setPolygonAnnotation(GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationPolygonAnnotation $polygonAnnotation)
  {
    $this->polygonAnnotation = $polygonAnnotation;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationPolygonAnnotation
   */
  public function getPolygonAnnotation()
  {
    return $this->polygonAnnotation;
  }
  /**
   * Polyline annotation.
   *
   * @param GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationPolylineAnnotation $polylineAnnotation
   */
  public function setPolylineAnnotation(GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationPolylineAnnotation $polylineAnnotation)
  {
    $this->polylineAnnotation = $polylineAnnotation;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationPolylineAnnotation
   */
  public function getPolylineAnnotation()
  {
    return $this->polylineAnnotation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaImageSegmentationAnnotation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaImageSegmentationAnnotation');
