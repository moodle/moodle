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

class GoogleCloudAiplatformV1ErrorAnalysisAnnotationAttributedItem extends \Google\Model
{
  /**
   * The unique ID for each annotation. Used by FE to allocate the annotation in
   * DB.
   *
   * @var string
   */
  public $annotationResourceName;
  /**
   * The distance of this item to the annotation.
   *
   * @var 
   */
  public $distance;

  /**
   * The unique ID for each annotation. Used by FE to allocate the annotation in
   * DB.
   *
   * @param string $annotationResourceName
   */
  public function setAnnotationResourceName($annotationResourceName)
  {
    $this->annotationResourceName = $annotationResourceName;
  }
  /**
   * @return string
   */
  public function getAnnotationResourceName()
  {
    return $this->annotationResourceName;
  }
  public function setDistance($distance)
  {
    $this->distance = $distance;
  }
  public function getDistance()
  {
    return $this->distance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ErrorAnalysisAnnotationAttributedItem::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ErrorAnalysisAnnotationAttributedItem');
