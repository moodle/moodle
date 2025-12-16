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

namespace Google\Service\Dataflow;

class StreamingOperationalLimits extends \Google\Model
{
  /**
   * The maximum size for an element in bag state.
   *
   * @var string
   */
  public $maxBagElementBytes;
  /**
   * The maximum size for an element in global data.
   *
   * @var string
   */
  public $maxGlobalDataBytes;
  /**
   * The maximum size allowed for a key.
   *
   * @var string
   */
  public $maxKeyBytes;
  /**
   * The maximum size for a single output element.
   *
   * @var string
   */
  public $maxProductionOutputBytes;
  /**
   * The maximum size for an element in sorted list state.
   *
   * @var string
   */
  public $maxSortedListElementBytes;
  /**
   * The maximum size for a source state update.
   *
   * @var string
   */
  public $maxSourceStateBytes;
  /**
   * The maximum size for a state tag.
   *
   * @var string
   */
  public $maxTagBytes;
  /**
   * The maximum size for a value state field.
   *
   * @var string
   */
  public $maxValueBytes;

  /**
   * The maximum size for an element in bag state.
   *
   * @param string $maxBagElementBytes
   */
  public function setMaxBagElementBytes($maxBagElementBytes)
  {
    $this->maxBagElementBytes = $maxBagElementBytes;
  }
  /**
   * @return string
   */
  public function getMaxBagElementBytes()
  {
    return $this->maxBagElementBytes;
  }
  /**
   * The maximum size for an element in global data.
   *
   * @param string $maxGlobalDataBytes
   */
  public function setMaxGlobalDataBytes($maxGlobalDataBytes)
  {
    $this->maxGlobalDataBytes = $maxGlobalDataBytes;
  }
  /**
   * @return string
   */
  public function getMaxGlobalDataBytes()
  {
    return $this->maxGlobalDataBytes;
  }
  /**
   * The maximum size allowed for a key.
   *
   * @param string $maxKeyBytes
   */
  public function setMaxKeyBytes($maxKeyBytes)
  {
    $this->maxKeyBytes = $maxKeyBytes;
  }
  /**
   * @return string
   */
  public function getMaxKeyBytes()
  {
    return $this->maxKeyBytes;
  }
  /**
   * The maximum size for a single output element.
   *
   * @param string $maxProductionOutputBytes
   */
  public function setMaxProductionOutputBytes($maxProductionOutputBytes)
  {
    $this->maxProductionOutputBytes = $maxProductionOutputBytes;
  }
  /**
   * @return string
   */
  public function getMaxProductionOutputBytes()
  {
    return $this->maxProductionOutputBytes;
  }
  /**
   * The maximum size for an element in sorted list state.
   *
   * @param string $maxSortedListElementBytes
   */
  public function setMaxSortedListElementBytes($maxSortedListElementBytes)
  {
    $this->maxSortedListElementBytes = $maxSortedListElementBytes;
  }
  /**
   * @return string
   */
  public function getMaxSortedListElementBytes()
  {
    return $this->maxSortedListElementBytes;
  }
  /**
   * The maximum size for a source state update.
   *
   * @param string $maxSourceStateBytes
   */
  public function setMaxSourceStateBytes($maxSourceStateBytes)
  {
    $this->maxSourceStateBytes = $maxSourceStateBytes;
  }
  /**
   * @return string
   */
  public function getMaxSourceStateBytes()
  {
    return $this->maxSourceStateBytes;
  }
  /**
   * The maximum size for a state tag.
   *
   * @param string $maxTagBytes
   */
  public function setMaxTagBytes($maxTagBytes)
  {
    $this->maxTagBytes = $maxTagBytes;
  }
  /**
   * @return string
   */
  public function getMaxTagBytes()
  {
    return $this->maxTagBytes;
  }
  /**
   * The maximum size for a value state field.
   *
   * @param string $maxValueBytes
   */
  public function setMaxValueBytes($maxValueBytes)
  {
    $this->maxValueBytes = $maxValueBytes;
  }
  /**
   * @return string
   */
  public function getMaxValueBytes()
  {
    return $this->maxValueBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamingOperationalLimits::class, 'Google_Service_Dataflow_StreamingOperationalLimits');
