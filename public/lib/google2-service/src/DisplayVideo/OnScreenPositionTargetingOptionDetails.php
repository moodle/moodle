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

namespace Google\Service\DisplayVideo;

class OnScreenPositionTargetingOptionDetails extends \Google\Model
{
  /**
   * On screen position is not specified in this version. This enum is a place
   * holder for a default value and does not represent a real on screen
   * position.
   */
  public const ON_SCREEN_POSITION_ON_SCREEN_POSITION_UNSPECIFIED = 'ON_SCREEN_POSITION_UNSPECIFIED';
  /**
   * The ad position is unknown on the screen.
   */
  public const ON_SCREEN_POSITION_ON_SCREEN_POSITION_UNKNOWN = 'ON_SCREEN_POSITION_UNKNOWN';
  /**
   * The ad is located above the fold.
   */
  public const ON_SCREEN_POSITION_ON_SCREEN_POSITION_ABOVE_THE_FOLD = 'ON_SCREEN_POSITION_ABOVE_THE_FOLD';
  /**
   * The ad is located below the fold.
   */
  public const ON_SCREEN_POSITION_ON_SCREEN_POSITION_BELOW_THE_FOLD = 'ON_SCREEN_POSITION_BELOW_THE_FOLD';
  /**
   * Output only. The on screen position.
   *
   * @var string
   */
  public $onScreenPosition;

  /**
   * Output only. The on screen position.
   *
   * Accepted values: ON_SCREEN_POSITION_UNSPECIFIED,
   * ON_SCREEN_POSITION_UNKNOWN, ON_SCREEN_POSITION_ABOVE_THE_FOLD,
   * ON_SCREEN_POSITION_BELOW_THE_FOLD
   *
   * @param self::ON_SCREEN_POSITION_* $onScreenPosition
   */
  public function setOnScreenPosition($onScreenPosition)
  {
    $this->onScreenPosition = $onScreenPosition;
  }
  /**
   * @return self::ON_SCREEN_POSITION_*
   */
  public function getOnScreenPosition()
  {
    return $this->onScreenPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OnScreenPositionTargetingOptionDetails::class, 'Google_Service_DisplayVideo_OnScreenPositionTargetingOptionDetails');
