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

namespace Google\Service\Vision;

class CropHintsParams extends \Google\Collection
{
  protected $collection_key = 'aspectRatios';
  /**
   * Aspect ratios in floats, representing the ratio of the width to the height
   * of the image. For example, if the desired aspect ratio is 4/3, the
   * corresponding float value should be 1.33333. If not specified, the best
   * possible crop is returned. The number of provided aspect ratios is limited
   * to a maximum of 16; any aspect ratios provided after the 16th are ignored.
   *
   * @var float[]
   */
  public $aspectRatios;

  /**
   * Aspect ratios in floats, representing the ratio of the width to the height
   * of the image. For example, if the desired aspect ratio is 4/3, the
   * corresponding float value should be 1.33333. If not specified, the best
   * possible crop is returned. The number of provided aspect ratios is limited
   * to a maximum of 16; any aspect ratios provided after the 16th are ignored.
   *
   * @param float[] $aspectRatios
   */
  public function setAspectRatios($aspectRatios)
  {
    $this->aspectRatios = $aspectRatios;
  }
  /**
   * @return float[]
   */
  public function getAspectRatios()
  {
    return $this->aspectRatios;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CropHintsParams::class, 'Google_Service_Vision_CropHintsParams');
