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

class GoogleCloudDatalabelingV1beta1EventConfig extends \Google\Collection
{
  protected $collection_key = 'annotationSpecSets';
  /**
   * Required. The list of annotation spec set resource name. Similar to video
   * classification, we support selecting event from multiple AnnotationSpecSet
   * at the same time.
   *
   * @var string[]
   */
  public $annotationSpecSets;
  /**
   * Videos will be cut to smaller clips to make it easier for labelers to work
   * on. Users can configure is field in seconds, if not set, default value is
   * 60s.
   *
   * @var int
   */
  public $clipLength;
  /**
   * The overlap length between different video clips. Users can configure is
   * field in seconds, if not set, default value is 1s.
   *
   * @var int
   */
  public $overlapLength;

  /**
   * Required. The list of annotation spec set resource name. Similar to video
   * classification, we support selecting event from multiple AnnotationSpecSet
   * at the same time.
   *
   * @param string[] $annotationSpecSets
   */
  public function setAnnotationSpecSets($annotationSpecSets)
  {
    $this->annotationSpecSets = $annotationSpecSets;
  }
  /**
   * @return string[]
   */
  public function getAnnotationSpecSets()
  {
    return $this->annotationSpecSets;
  }
  /**
   * Videos will be cut to smaller clips to make it easier for labelers to work
   * on. Users can configure is field in seconds, if not set, default value is
   * 60s.
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
   * field in seconds, if not set, default value is 1s.
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
class_alias(GoogleCloudDatalabelingV1beta1EventConfig::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1EventConfig');
