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

class GoogleCloudAiplatformV1SchemaImageBoundingBoxAnnotation extends \Google\Model
{
  /**
   * The resource Id of the AnnotationSpec that this Annotation pertains to.
   *
   * @var string
   */
  public $annotationSpecId;
  /**
   * The display name of the AnnotationSpec that this Annotation pertains to.
   *
   * @var string
   */
  public $displayName;
  /**
   * The rightmost coordinate of the bounding box.
   *
   * @var 
   */
  public $xMax;
  /**
   * The leftmost coordinate of the bounding box.
   *
   * @var 
   */
  public $xMin;
  /**
   * The bottommost coordinate of the bounding box.
   *
   * @var 
   */
  public $yMax;
  /**
   * The topmost coordinate of the bounding box.
   *
   * @var 
   */
  public $yMin;

  /**
   * The resource Id of the AnnotationSpec that this Annotation pertains to.
   *
   * @param string $annotationSpecId
   */
  public function setAnnotationSpecId($annotationSpecId)
  {
    $this->annotationSpecId = $annotationSpecId;
  }
  /**
   * @return string
   */
  public function getAnnotationSpecId()
  {
    return $this->annotationSpecId;
  }
  /**
   * The display name of the AnnotationSpec that this Annotation pertains to.
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
  public function setXMax($xMax)
  {
    $this->xMax = $xMax;
  }
  public function getXMax()
  {
    return $this->xMax;
  }
  public function setXMin($xMin)
  {
    $this->xMin = $xMin;
  }
  public function getXMin()
  {
    return $this->xMin;
  }
  public function setYMax($yMax)
  {
    $this->yMax = $yMax;
  }
  public function getYMax()
  {
    return $this->yMax;
  }
  public function setYMin($yMin)
  {
    $this->yMin = $yMin;
  }
  public function getYMin()
  {
    return $this->yMin;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaImageBoundingBoxAnnotation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaImageBoundingBoxAnnotation');
