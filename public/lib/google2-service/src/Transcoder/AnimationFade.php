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

class AnimationFade extends \Google\Model
{
  /**
   * The fade type is not specified.
   */
  public const FADE_TYPE_FADE_TYPE_UNSPECIFIED = 'FADE_TYPE_UNSPECIFIED';
  /**
   * Fade the overlay object into view.
   */
  public const FADE_TYPE_FADE_IN = 'FADE_IN';
  /**
   * Fade the overlay object out of view.
   */
  public const FADE_TYPE_FADE_OUT = 'FADE_OUT';
  /**
   * The time to end the fade animation, in seconds. Default:
   * `start_time_offset` + 1s
   *
   * @var string
   */
  public $endTimeOffset;
  /**
   * Required. Type of fade animation: `FADE_IN` or `FADE_OUT`.
   *
   * @var string
   */
  public $fadeType;
  /**
   * The time to start the fade animation, in seconds. Default: 0
   *
   * @var string
   */
  public $startTimeOffset;
  protected $xyType = NormalizedCoordinate::class;
  protected $xyDataType = '';

  /**
   * The time to end the fade animation, in seconds. Default:
   * `start_time_offset` + 1s
   *
   * @param string $endTimeOffset
   */
  public function setEndTimeOffset($endTimeOffset)
  {
    $this->endTimeOffset = $endTimeOffset;
  }
  /**
   * @return string
   */
  public function getEndTimeOffset()
  {
    return $this->endTimeOffset;
  }
  /**
   * Required. Type of fade animation: `FADE_IN` or `FADE_OUT`.
   *
   * Accepted values: FADE_TYPE_UNSPECIFIED, FADE_IN, FADE_OUT
   *
   * @param self::FADE_TYPE_* $fadeType
   */
  public function setFadeType($fadeType)
  {
    $this->fadeType = $fadeType;
  }
  /**
   * @return self::FADE_TYPE_*
   */
  public function getFadeType()
  {
    return $this->fadeType;
  }
  /**
   * The time to start the fade animation, in seconds. Default: 0
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
class_alias(AnimationFade::class, 'Google_Service_Transcoder_AnimationFade');
