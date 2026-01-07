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

namespace Google\Service\CloudNaturalLanguage;

class XPSConfusionMatrixRow extends \Google\Collection
{
  protected $collection_key = 'exampleCount';
  /**
   * Same as above except intended to represent other counts (for e.g. for
   * segmentation this is pixel count). NOTE(params): Only example_count or
   * count is set (oneoff does not support repeated fields unless they are
   * embedded inside another message).
   *
   * @var string[]
   */
  public $count;
  /**
   * Value of the specific cell in the confusion matrix. The number of values
   * each row has (i.e. the length of the row) is equal to the length of the
   * annotation_spec_id_token field.
   *
   * @var int[]
   */
  public $exampleCount;

  /**
   * Same as above except intended to represent other counts (for e.g. for
   * segmentation this is pixel count). NOTE(params): Only example_count or
   * count is set (oneoff does not support repeated fields unless they are
   * embedded inside another message).
   *
   * @param string[] $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string[]
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Value of the specific cell in the confusion matrix. The number of values
   * each row has (i.e. the length of the row) is equal to the length of the
   * annotation_spec_id_token field.
   *
   * @param int[] $exampleCount
   */
  public function setExampleCount($exampleCount)
  {
    $this->exampleCount = $exampleCount;
  }
  /**
   * @return int[]
   */
  public function getExampleCount()
  {
    return $this->exampleCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSConfusionMatrixRow::class, 'Google_Service_CloudNaturalLanguage_XPSConfusionMatrixRow');
