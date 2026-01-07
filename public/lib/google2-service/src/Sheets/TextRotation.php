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

namespace Google\Service\Sheets;

class TextRotation extends \Google\Model
{
  /**
   * The angle between the standard orientation and the desired orientation.
   * Measured in degrees. Valid values are between -90 and 90. Positive angles
   * are angled upwards, negative are angled downwards. Note: For LTR text
   * direction positive angles are in the counterclockwise direction, whereas
   * for RTL they are in the clockwise direction
   *
   * @var int
   */
  public $angle;
  /**
   * If true, text reads top to bottom, but the orientation of individual
   * characters is unchanged. For example: | V | | e | | r | | t | | i | | c | |
   * a | | l |
   *
   * @var bool
   */
  public $vertical;

  /**
   * The angle between the standard orientation and the desired orientation.
   * Measured in degrees. Valid values are between -90 and 90. Positive angles
   * are angled upwards, negative are angled downwards. Note: For LTR text
   * direction positive angles are in the counterclockwise direction, whereas
   * for RTL they are in the clockwise direction
   *
   * @param int $angle
   */
  public function setAngle($angle)
  {
    $this->angle = $angle;
  }
  /**
   * @return int
   */
  public function getAngle()
  {
    return $this->angle;
  }
  /**
   * If true, text reads top to bottom, but the orientation of individual
   * characters is unchanged. For example: | V | | e | | r | | t | | i | | c | |
   * a | | l |
   *
   * @param bool $vertical
   */
  public function setVertical($vertical)
  {
    $this->vertical = $vertical;
  }
  /**
   * @return bool
   */
  public function getVertical()
  {
    return $this->vertical;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextRotation::class, 'Google_Service_Sheets_TextRotation');
