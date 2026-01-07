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

class GoogleCloudDatalabelingV1beta1ObjectTrackingConfig extends \Google\Model
{
  /**
   * Required. Annotation spec set resource name.
   *
   * @var string
   */
  public $annotationSpecSet;
  /**
   * Videos will be cut to smaller clips to make it easier for labelers to work
   * on. Users can configure is field in seconds, if not set, default value is
   * 20s.
   *
   * @var int
   */
  public $clipLength;
  /**
   * The overlap length between different video clips. Users can configure is
   * field in seconds, if not set, default value is 0.3s.
   *
   * @var int
   */
  public $overlapLength;

  /**
   * Required. Annotation spec set resource name.
   *
   * @param string $annotationSpecSet
   */
  public function setAnnotationSpecSet($annotationSpecSet)
  {
    $this->annotationSpecSet = $annotationSpecSet;
  }
  /**
   * @return string
   */
  public function getAnnotationSpecSet()
  {
    return $this->annotationSpecSet;
  }
  /**
   * Videos will be cut to smaller clips to make it easier for labelers to work
   * on. Users can configure is field in seconds, if not set, default value is
   * 20s.
   *
   * @param int $clipLength
   */
  public function setClipLength($clipLength)
  {
    $this->clipLength = $clipLength;
  }
  /**
   * @return int
   */
  public function getClipLength()
  {
    return $this->clipLength;
  }
  /**
   * The overlap length between different video clips. Users can configure is
   * field in seconds, if not set, default value is 0.3s.
   *
   * @param int $overlapLength
   */
  public function setOverlapLength($overlapLength)
  {
    $this->overlapLength = $overlapLength;
  }
  /**
   * @return int
   */
  public function getOverlapLength()
  {
    return $this->overlapLength;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1ObjectTrackingConfig::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1ObjectTrackingConfig');
