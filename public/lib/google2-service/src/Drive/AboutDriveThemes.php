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

namespace Google\Service\Drive;

class AboutDriveThemes extends \Google\Model
{
  /**
   * A link to this theme's background image.
   *
   * @var string
   */
  public $backgroundImageLink;
  /**
   * The color of this theme as an RGB hex string.
   *
   * @var string
   */
  public $colorRgb;
  /**
   * The ID of the theme.
   *
   * @var string
   */
  public $id;

  /**
   * A link to this theme's background image.
   *
   * @param string $backgroundImageLink
   */
  public function setBackgroundImageLink($backgroundImageLink)
  {
    $this->backgroundImageLink = $backgroundImageLink;
  }
  /**
   * @return string
   */
  public function getBackgroundImageLink()
  {
    return $this->backgroundImageLink;
  }
  /**
   * The color of this theme as an RGB hex string.
   *
   * @param string $colorRgb
   */
  public function setColorRgb($colorRgb)
  {
    $this->colorRgb = $colorRgb;
  }
  /**
   * @return string
   */
  public function getColorRgb()
  {
    return $this->colorRgb;
  }
  /**
   * The ID of the theme.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AboutDriveThemes::class, 'Google_Service_Drive_AboutDriveThemes');
