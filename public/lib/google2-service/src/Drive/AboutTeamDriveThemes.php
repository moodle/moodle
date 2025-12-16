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

class AboutTeamDriveThemes extends \Google\Model
{
  /**
   * Deprecated: Use `driveThemes/backgroundImageLink` instead.
   *
   * @deprecated
   * @var string
   */
  public $backgroundImageLink;
  /**
   * Deprecated: Use `driveThemes/colorRgb` instead.
   *
   * @deprecated
   * @var string
   */
  public $colorRgb;
  /**
   * Deprecated: Use `driveThemes/id` instead.
   *
   * @deprecated
   * @var string
   */
  public $id;

  /**
   * Deprecated: Use `driveThemes/backgroundImageLink` instead.
   *
   * @deprecated
   * @param string $backgroundImageLink
   */
  public function setBackgroundImageLink($backgroundImageLink)
  {
    $this->backgroundImageLink = $backgroundImageLink;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBackgroundImageLink()
  {
    return $this->backgroundImageLink;
  }
  /**
   * Deprecated: Use `driveThemes/colorRgb` instead.
   *
   * @deprecated
   * @param string $colorRgb
   */
  public function setColorRgb($colorRgb)
  {
    $this->colorRgb = $colorRgb;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getColorRgb()
  {
    return $this->colorRgb;
  }
  /**
   * Deprecated: Use `driveThemes/id` instead.
   *
   * @deprecated
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AboutTeamDriveThemes::class, 'Google_Service_Drive_AboutTeamDriveThemes');
