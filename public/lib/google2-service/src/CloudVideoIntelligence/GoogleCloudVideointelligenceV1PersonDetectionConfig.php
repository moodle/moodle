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

class GoogleCloudVideointelligenceV1PersonDetectionConfig extends \Google\Model
{
  /**
   * Whether to enable person attributes detection, such as cloth color (black,
   * blue, etc), type (coat, dress, etc), pattern (plain, floral, etc), hair,
   * etc. Ignored if 'include_bounding_boxes' is set to false.
   *
   * @var bool
   */
  public $includeAttributes;
  /**
   * Whether bounding boxes are included in the person detection annotation
   * output.
   *
   * @var bool
   */
  public $includeBoundingBoxes;
  /**
   * Whether to enable pose landmarks detection. Ignored if
   * 'include_bounding_boxes' is set to false.
   *
   * @var bool
   */
  public $includePoseLandmarks;

  /**
   * Whether to enable person attributes detection, such as cloth color (black,
   * blue, etc), type (coat, dress, etc), pattern (plain, floral, etc), hair,
   * etc. Ignored if 'include_bounding_boxes' is set to false.
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
   * Whether bounding boxes are included in the person detection annotation
   * output.
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
   * Whether to enable pose landmarks detection. Ignored if
   * 'include_bounding_boxes' is set to false.
   *
   * @param bool $includePoseLandmarks
   */
  public function setIncludePoseLandmarks($includePoseLandmarks)
  {
    $this->includePoseLandmarks = $includePoseLandmarks;
  }
  /**
   * @return bool
   */
  public function getIncludePoseLandmarks()
  {
    return $this->includePoseLandmarks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1PersonDetectionConfig::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1PersonDetectionConfig');
