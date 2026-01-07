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

class OnScreenPositionAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Ad type is not specified or is unknown in this version.
   */
  public const AD_TYPE_AD_TYPE_UNSPECIFIED = 'AD_TYPE_UNSPECIFIED';
  /**
   * Display creatives, e.g. image and HTML5.
   */
  public const AD_TYPE_AD_TYPE_DISPLAY = 'AD_TYPE_DISPLAY';
  /**
   * Video creatives, e.g. video ads that play during streaming content in video
   * players.
   */
  public const AD_TYPE_AD_TYPE_VIDEO = 'AD_TYPE_VIDEO';
  /**
   * Audio creatives, e.g. audio ads that play during audio content.
   */
  public const AD_TYPE_AD_TYPE_AUDIO = 'AD_TYPE_AUDIO';
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
   * Output only. The ad type to target. Only applicable to insertion order
   * targeting and new line items supporting the specified ad type will inherit
   * this targeting option by default. Possible values are: * `AD_TYPE_DISPLAY`,
   * the setting will be inherited by new line item when line_item_type is
   * `LINE_ITEM_TYPE_DISPLAY_DEFAULT`. * `AD_TYPE_VIDEO`, the setting will be
   * inherited by new line item when line_item_type is
   * `LINE_ITEM_TYPE_VIDEO_DEFAULT`.
   *
   * @var string
   */
  public $adType;
  /**
   * Output only. The on screen position.
   *
   * @var string
   */
  public $onScreenPosition;
  /**
   * Required. The targeting_option_id field when targeting_type is
   * `TARGETING_TYPE_ON_SCREEN_POSITION`.
   *
   * @var string
   */
  public $targetingOptionId;

  /**
   * Output only. The ad type to target. Only applicable to insertion order
   * targeting and new line items supporting the specified ad type will inherit
   * this targeting option by default. Possible values are: * `AD_TYPE_DISPLAY`,
   * the setting will be inherited by new line item when line_item_type is
   * `LINE_ITEM_TYPE_DISPLAY_DEFAULT`. * `AD_TYPE_VIDEO`, the setting will be
   * inherited by new line item when line_item_type is
   * `LINE_ITEM_TYPE_VIDEO_DEFAULT`.
   *
   * Accepted values: AD_TYPE_UNSPECIFIED, AD_TYPE_DISPLAY, AD_TYPE_VIDEO,
   * AD_TYPE_AUDIO
   *
   * @param self::AD_TYPE_* $adType
   */
  public function setAdType($adType)
  {
    $this->adType = $adType;
  }
  /**
   * @return self::AD_TYPE_*
   */
  public function getAdType()
  {
    return $this->adType;
  }
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
  /**
   * Required. The targeting_option_id field when targeting_type is
   * `TARGETING_TYPE_ON_SCREEN_POSITION`.
   *
   * @param string $targetingOptionId
   */
  public function setTargetingOptionId($targetingOptionId)
  {
    $this->targetingOptionId = $targetingOptionId;
  }
  /**
   * @return string
   */
  public function getTargetingOptionId()
  {
    return $this->targetingOptionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OnScreenPositionAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_OnScreenPositionAssignedTargetingOptionDetails');
