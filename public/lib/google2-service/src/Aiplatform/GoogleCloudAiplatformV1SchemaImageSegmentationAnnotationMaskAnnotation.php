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

class GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationMaskAnnotation extends \Google\Collection
{
  protected $collection_key = 'annotationSpecColors';
  protected $annotationSpecColorsType = GoogleCloudAiplatformV1SchemaAnnotationSpecColor::class;
  protected $annotationSpecColorsDataType = 'array';
  /**
   * Google Cloud Storage URI that points to the mask image. The image must be
   * in PNG format. It must have the same size as the DataItem's image. Each
   * pixel in the image mask represents the AnnotationSpec which the pixel in
   * the image DataItem belong to. Each color is mapped to one AnnotationSpec
   * based on annotation_spec_colors.
   *
   * @var string
   */
  public $maskGcsUri;

  /**
   * The mapping between color and AnnotationSpec for this Annotation.
   *
   * @param GoogleCloudAiplatformV1SchemaAnnotationSpecColor[] $annotationSpecColors
   */
  public function setAnnotationSpecColors($annotationSpecColors)
  {
    $this->annotationSpecColors = $annotationSpecColors;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaAnnotationSpecColor[]
   */
  public function getAnnotationSpecColors()
  {
    return $this->annotationSpecColors;
  }
  /**
   * Google Cloud Storage URI that points to the mask image. The image must be
   * in PNG format. It must have the same size as the DataItem's image. Each
   * pixel in the image mask represents the AnnotationSpec which the pixel in
   * the image DataItem belong to. Each color is mapped to one AnnotationSpec
   * based on annotation_spec_colors.
   *
   * @param string $maskGcsUri
   */
  public function setMaskGcsUri($maskGcsUri)
  {
    $this->maskGcsUri = $maskGcsUri;
  }
  /**
   * @return string
   */
  public function getMaskGcsUri()
  {
    return $this->maskGcsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationMaskAnnotation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaImageSegmentationAnnotationMaskAnnotation');
