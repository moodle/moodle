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

namespace Google\Service\Directory;

class BacklightInfo extends \Google\Model
{
  /**
   * Output only. Current brightness of the backlight, between 0 and
   * max_brightness.
   *
   * @var int
   */
  public $brightness;
  /**
   * Output only. Maximum brightness for the backlight.
   *
   * @var int
   */
  public $maxBrightness;
  /**
   * Output only. Path to this backlight on the system. Useful if the caller
   * needs to correlate with other information.
   *
   * @var string
   */
  public $path;

  /**
   * Output only. Current brightness of the backlight, between 0 and
   * max_brightness.
   *
   * @param int $brightness
   */
  public function setBrightness($brightness)
  {
    $this->brightness = $brightness;
  }
  /**
   * @return int
   */
  public function getBrightness()
  {
    return $this->brightness;
  }
  /**
   * Output only. Maximum brightness for the backlight.
   *
   * @param int $maxBrightness
   */
  public function setMaxBrightness($maxBrightness)
  {
    $this->maxBrightness = $maxBrightness;
  }
  /**
   * @return int
   */
  public function getMaxBrightness()
  {
    return $this->maxBrightness;
  }
  /**
   * Output only. Path to this backlight on the system. Useful if the caller
   * needs to correlate with other information.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BacklightInfo::class, 'Google_Service_Directory_BacklightInfo');
