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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2BadgeColors extends \Google\Model
{
  protected $backgroundColorType = GoogleTypeColor::class;
  protected $backgroundColorDataType = '';
  protected $foregroundColorType = GoogleTypeColor::class;
  protected $foregroundColorDataType = '';
  protected $soloColorType = GoogleTypeColor::class;
  protected $soloColorDataType = '';

  /**
   * Output only. Badge background that pairs with the foreground.
   *
   * @param GoogleTypeColor $backgroundColor
   */
  public function setBackgroundColor(GoogleTypeColor $backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return GoogleTypeColor
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * Output only. Badge foreground that pairs with the background.
   *
   * @param GoogleTypeColor $foregroundColor
   */
  public function setForegroundColor(GoogleTypeColor $foregroundColor)
  {
    $this->foregroundColor = $foregroundColor;
  }
  /**
   * @return GoogleTypeColor
   */
  public function getForegroundColor()
  {
    return $this->foregroundColor;
  }
  /**
   * Output only. Color that can be used for text without a background.
   *
   * @param GoogleTypeColor $soloColor
   */
  public function setSoloColor(GoogleTypeColor $soloColor)
  {
    $this->soloColor = $soloColor;
  }
  /**
   * @return GoogleTypeColor
   */
  public function getSoloColor()
  {
    return $this->soloColor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2BadgeColors::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2BadgeColors');
