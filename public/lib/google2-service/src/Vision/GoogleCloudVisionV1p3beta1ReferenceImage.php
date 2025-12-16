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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p3beta1ReferenceImage extends \Google\Collection
{
  protected $collection_key = 'boundingPolys';
  protected $boundingPolysType = GoogleCloudVisionV1p3beta1BoundingPoly::class;
  protected $boundingPolysDataType = 'array';
  /**
   * The resource name of the reference image. Format is: `projects/PROJECT_ID/l
   * ocations/LOC_ID/products/PRODUCT_ID/referenceImages/IMAGE_ID`. This field
   * is ignored when creating a reference image.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The Google Cloud Storage URI of the reference image. The URI must
   * start with `gs://`.
   *
   * @var string
   */
  public $uri;

  /**
   * Optional. Bounding polygons around the areas of interest in the reference
   * image. If this field is empty, the system will try to detect regions of
   * interest. At most 10 bounding polygons will be used. The provided shape is
   * converted into a non-rotated rectangle. Once converted, the small edge of
   * the rectangle must be greater than or equal to 300 pixels. The aspect ratio
   * must be 1:4 or less (i.e. 1:3 is ok; 1:5 is not).
   *
   * @param GoogleCloudVisionV1p3beta1BoundingPoly[] $boundingPolys
   */
  public function setBoundingPolys($boundingPolys)
  {
    $this->boundingPolys = $boundingPolys;
  }
  /**
   * @return GoogleCloudVisionV1p3beta1BoundingPoly[]
   */
  public function getBoundingPolys()
  {
    return $this->boundingPolys;
  }
  /**
   * The resource name of the reference image. Format is: `projects/PROJECT_ID/l
   * ocations/LOC_ID/products/PRODUCT_ID/referenceImages/IMAGE_ID`. This field
   * is ignored when creating a reference image.
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
  /**
   * Required. The Google Cloud Storage URI of the reference image. The URI must
   * start with `gs://`.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p3beta1ReferenceImage::class, 'Google_Service_Vision_GoogleCloudVisionV1p3beta1ReferenceImage');
