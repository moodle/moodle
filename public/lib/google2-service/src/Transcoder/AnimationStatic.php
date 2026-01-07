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

namespace Google\Service\Transcoder;

class AnimationStatic extends \Google\Model
{
  /**
   * The time to start displaying the overlay object, in seconds. Default: 0
   *
   * @var string
   */
  public $startTimeOffset;
  protected $xyType = NormalizedCoordinate::class;
  protected $xyDataType = '';

  /**
   * The time to start displaying the overlay object, in seconds. Default: 0
   *
   * @param string $startTimeOffset
   */
  public function setStartTimeOffset($startTimeOffset)
  {
    $this->startTimeOffset = $startTimeOffset;
  }
  /**
   * @return string
   */
  public function getStartTimeOffset()
  {
    return $this->startTimeOffset;
  }
  /**
   * Normalized coordinates based on output video resolution. Valid values:
   * `0.0`–`1.0`. `xy` is the upper-left coordinate of the overlay object. For
   * example, use the x and y coordinates {0,0} to position the top-left corner
   * of the overlay animation in the top-left corner of the output video.
   *
   * @param NormalizedCoordinate $xy
   */
  public function setXy(NormalizedCoordinate $xy)
  {
    $this->xy = $xy;
  }
  /**
   * @return NormalizedCoordinate
   */
  public function getXy()
  {
    return $this->xy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnimationStatic::class, 'Google_Service_Transcoder_AnimationStatic');
