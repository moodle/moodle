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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1FaceDetectionConfig extends \Google\Model
{
  /**
   * Whether to enable face attributes detection, such as glasses, dark_glasses,
   * mouth_open etc. Ignored if 'include_bounding_boxes' is set to false.
   *
   * @var bool
   */
  public $includeAttributes;
  /**
   * Whether bounding boxes are included in the face annotation output.
   *
   * @var bool
   */
  public $includeBoundingBoxes;
  /**
   * Model to use for face detection. Supported values: "builtin/stable" (the
   * default if unset) and "builtin/latest".
   *
   * @var string
   */
  public $model;

  /**
   * Whether to enable face attributes detection, such as glasses, dark_glasses,
   * mouth_open etc. Ignored if 'include_bounding_boxes' is set to false.
   *
   * @param bool $includeAttributes
   */
  public function setIncludeAttributes($includeAttributes)
  {
    $this->includeAttributes = $includeAttributes;
  }
  /**
   * @return bool
   */
  public function getIncludeAttributes()
  {
    return $this->includeAttributes;
  }
  /**
   * Whether bounding boxes are included in the face annotation output.
   *
   * @param bool $includeBoundingBoxes
   */
  public function setIncludeBoundingBoxes($includeBoundingBoxes)
  {
    $this->includeBoundingBoxes = $includeBoundingBoxes;
  }
  /**
   * @return bool
   */
  public function getIncludeBoundingBoxes()
  {
    return $this->includeBoundingBoxes;
  }
  /**
   * Model to use for face detection. Supported values: "builtin/stable" (the
   * default if unset) and "builtin/latest".
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1FaceDetectionConfig::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1FaceDetectionConfig');
