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

class GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceDisplayHints extends \Google\Model
{
  protected $badgeColorsType = GoogleAppsDriveLabelsV2BadgeColors::class;
  protected $badgeColorsDataType = '';
  /**
   * The priority of this badge. Used to compare and sort between multiple
   * badges. A lower number means the badge should be shown first. When a
   * badging configuration is not present, this will be 0. Otherwise, this will
   * be set to `BadgeConfig.priority_override` or the default heuristic which
   * prefers creation date of the label, and field and option priority.
   *
   * @var string
   */
  public $badgePriority;
  protected $darkBadgeColorsType = GoogleAppsDriveLabelsV2BadgeColors::class;
  protected $darkBadgeColorsDataType = '';
  /**
   * Whether the option should be shown in the UI as disabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * This option should be hidden in the search menu when searching for Drive
   * items.
   *
   * @var bool
   */
  public $hiddenInSearch;
  /**
   * This option should be shown in the apply menu when applying values to a
   * Drive item.
   *
   * @var bool
   */
  public $shownInApply;

  /**
   * The colors to use for the badge. Changed to Google Material colors based on
   * the chosen `properties.badge_config.color`.
   *
   * @param GoogleAppsDriveLabelsV2BadgeColors $badgeColors
   */
  public function setBadgeColors(GoogleAppsDriveLabelsV2BadgeColors $badgeColors)
  {
    $this->badgeColors = $badgeColors;
  }
  /**
   * @return GoogleAppsDriveLabelsV2BadgeColors
   */
  public function getBadgeColors()
  {
    return $this->badgeColors;
  }
  /**
   * The priority of this badge. Used to compare and sort between multiple
   * badges. A lower number means the badge should be shown first. When a
   * badging configuration is not present, this will be 0. Otherwise, this will
   * be set to `BadgeConfig.priority_override` or the default heuristic which
   * prefers creation date of the label, and field and option priority.
   *
   * @param string $badgePriority
   */
  public function setBadgePriority($badgePriority)
  {
    $this->badgePriority = $badgePriority;
  }
  /**
   * @return string
   */
  public function getBadgePriority()
  {
    return $this->badgePriority;
  }
  /**
   * The dark-mode color to use for the badge. Changed to Google Material colors
   * based on the chosen `properties.badge_config.color`.
   *
   * @param GoogleAppsDriveLabelsV2BadgeColors $darkBadgeColors
   */
  public function setDarkBadgeColors(GoogleAppsDriveLabelsV2BadgeColors $darkBadgeColors)
  {
    $this->darkBadgeColors = $darkBadgeColors;
  }
  /**
   * @return GoogleAppsDriveLabelsV2BadgeColors
   */
  public function getDarkBadgeColors()
  {
    return $this->darkBadgeColors;
  }
  /**
   * Whether the option should be shown in the UI as disabled.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * This option should be hidden in the search menu when searching for Drive
   * items.
   *
   * @param bool $hiddenInSearch
   */
  public function setHiddenInSearch($hiddenInSearch)
  {
    $this->hiddenInSearch = $hiddenInSearch;
  }
  /**
   * @return bool
   */
  public function getHiddenInSearch()
  {
    return $this->hiddenInSearch;
  }
  /**
   * This option should be shown in the apply menu when applying values to a
   * Drive item.
   *
   * @param bool $shownInApply
   */
  public function setShownInApply($shownInApply)
  {
    $this->shownInApply = $shownInApply;
  }
  /**
   * @return bool
   */
  public function getShownInApply()
  {
    return $this->shownInApply;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceDisplayHints::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceDisplayHints');
