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

namespace Google\Service\YouTube;

class InvideoTiming extends \Google\Model
{
  public const TYPE_offsetFromStart = 'offsetFromStart';
  public const TYPE_offsetFromEnd = 'offsetFromEnd';
  /**
   * Defines the duration in milliseconds for which the promotion should be
   * displayed. If missing, the client should use the default.
   *
   * @var string
   */
  public $durationMs;
  /**
   * Defines the time at which the promotion will appear. Depending on the value
   * of type the value of the offsetMs field will represent a time offset from
   * the start or from the end of the video, expressed in milliseconds.
   *
   * @var string
   */
  public $offsetMs;
  /**
   * Describes a timing type. If the value is offsetFromStart, then the offsetMs
   * field represents an offset from the start of the video. If the value is
   * offsetFromEnd, then the offsetMs field represents an offset from the end of
   * the video.
   *
   * @var string
   */
  public $type;

  /**
   * Defines the duration in milliseconds for which the promotion should be
   * displayed. If missing, the client should use the default.
   *
   * @param string $durationMs
   */
  public function setDurationMs($durationMs)
  {
    $this->durationMs = $durationMs;
  }
  /**
   * @return string
   */
  public function getDurationMs()
  {
    return $this->durationMs;
  }
  /**
   * Defines the time at which the promotion will appear. Depending on the value
   * of type the value of the offsetMs field will represent a time offset from
   * the start or from the end of the video, expressed in milliseconds.
   *
   * @param string $offsetMs
   */
  public function setOffsetMs($offsetMs)
  {
    $this->offsetMs = $offsetMs;
  }
  /**
   * @return string
   */
  public function getOffsetMs()
  {
    return $this->offsetMs;
  }
  /**
   * Describes a timing type. If the value is offsetFromStart, then the offsetMs
   * field represents an offset from the start of the video. If the value is
   * offsetFromEnd, then the offsetMs field represents an offset from the end of
   * the video.
   *
   * Accepted values: offsetFromStart, offsetFromEnd
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InvideoTiming::class, 'Google_Service_YouTube_InvideoTiming');
