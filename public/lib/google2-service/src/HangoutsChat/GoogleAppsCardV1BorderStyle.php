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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1BorderStyle extends \Google\Model
{
  /**
   * Don't use. Unspecified.
   */
  public const TYPE_BORDER_TYPE_UNSPECIFIED = 'BORDER_TYPE_UNSPECIFIED';
  /**
   * No border.
   */
  public const TYPE_NO_BORDER = 'NO_BORDER';
  /**
   * Default value. Outline.
   */
  public const TYPE_STROKE = 'STROKE';
  /**
   * The corner radius for the border.
   *
   * @var int
   */
  public $cornerRadius;
  protected $strokeColorType = Color::class;
  protected $strokeColorDataType = '';
  /**
   * The border type.
   *
   * @var string
   */
  public $type;

  /**
   * The corner radius for the border.
   *
   * @param int $cornerRadius
   */
  public function setCornerRadius($cornerRadius)
  {
    $this->cornerRadius = $cornerRadius;
  }
  /**
   * @return int
   */
  public function getCornerRadius()
  {
    return $this->cornerRadius;
  }
  /**
   * The colors to use when the type is `BORDER_TYPE_STROKE`. To set the stroke
   * color, specify a value for the `red`, `green`, and `blue` fields. The value
   * must be a float number between 0 and 1 based on the RGB color value, where
   * `0` (0/255) represents the absence of color and `1` (255/255) represents
   * the maximum intensity of the color. For example, the following sets the
   * color to red at its maximum intensity: ``` "color": { "red": 1, "green": 0,
   * "blue": 0, } ``` The `alpha` field is unavailable for stroke color. If
   * specified, this field is ignored.
   *
   * @param Color $strokeColor
   */
  public function setStrokeColor(Color $strokeColor)
  {
    $this->strokeColor = $strokeColor;
  }
  /**
   * @return Color
   */
  public function getStrokeColor()
  {
    return $this->strokeColor;
  }
  /**
   * The border type.
   *
   * Accepted values: BORDER_TYPE_UNSPECIFIED, NO_BORDER, STROKE
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
class_alias(GoogleAppsCardV1BorderStyle::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1BorderStyle');
