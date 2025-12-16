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

class Color extends \Google\Model
{
  /**
   * Control brightness of the video. Enter a value between -1 and 1, where -1
   * is minimum brightness and 1 is maximum brightness. 0 is no change. The
   * default is 0.
   *
   * @var 
   */
  public $brightness;
  /**
   * Control black and white contrast of the video. Enter a value between -1 and
   * 1, where -1 is minimum contrast and 1 is maximum contrast. 0 is no change.
   * The default is 0.
   *
   * @var 
   */
  public $contrast;
  /**
   * Control color saturation of the video. Enter a value between -1 and 1,
   * where -1 is fully desaturated and 1 is maximum saturation. 0 is no change.
   * The default is 0.
   *
   * @var 
   */
  public $saturation;

  public function setBrightness($brightness)
  {
    $this->brightness = $brightness;
  }
  public function getBrightness()
  {
    return $this->brightness;
  }
  public function setContrast($contrast)
  {
    $this->contrast = $contrast;
  }
  public function getContrast()
  {
    return $this->contrast;
  }
  public function setSaturation($saturation)
  {
    $this->saturation = $saturation;
  }
  public function getSaturation()
  {
    return $this->saturation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Color::class, 'Google_Service_Transcoder_Color');
