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

class GoogleAppsCardV1CollapseControl extends \Google\Model
{
  /**
   * Don't use. Unspecified.
   */
  public const HORIZONTAL_ALIGNMENT_HORIZONTAL_ALIGNMENT_UNSPECIFIED = 'HORIZONTAL_ALIGNMENT_UNSPECIFIED';
  /**
   * Default value. Aligns widgets to the start position of the column. For
   * left-to-right layouts, aligns to the left. For right-to-left layouts,
   * aligns to the right.
   */
  public const HORIZONTAL_ALIGNMENT_START = 'START';
  /**
   * Aligns widgets to the center of the column.
   */
  public const HORIZONTAL_ALIGNMENT_CENTER = 'CENTER';
  /**
   * Aligns widgets to the end position of the column. For left-to-right
   * layouts, aligns widgets to the right. For right-to-left layouts, aligns
   * widgets to the left.
   */
  public const HORIZONTAL_ALIGNMENT_END = 'END';
  protected $collapseButtonType = GoogleAppsCardV1Button::class;
  protected $collapseButtonDataType = '';
  protected $expandButtonType = GoogleAppsCardV1Button::class;
  protected $expandButtonDataType = '';
  /**
   * The horizontal alignment of the expand and collapse button.
   *
   * @var string
   */
  public $horizontalAlignment;

  /**
   * Optional. Define a customizable button to collapse the section. Both
   * expand_button and collapse_button field must be set. Only one field set
   * will not take into effect. If this field isn't set, the default button is
   * used.
   *
   * @param GoogleAppsCardV1Button $collapseButton
   */
  public function setCollapseButton(GoogleAppsCardV1Button $collapseButton)
  {
    $this->collapseButton = $collapseButton;
  }
  /**
   * @return GoogleAppsCardV1Button
   */
  public function getCollapseButton()
  {
    return $this->collapseButton;
  }
  /**
   * Optional. Define a customizable button to expand the section. Both
   * expand_button and collapse_button field must be set. Only one field set
   * will not take into effect. If this field isn't set, the default button is
   * used.
   *
   * @param GoogleAppsCardV1Button $expandButton
   */
  public function setExpandButton(GoogleAppsCardV1Button $expandButton)
  {
    $this->expandButton = $expandButton;
  }
  /**
   * @return GoogleAppsCardV1Button
   */
  public function getExpandButton()
  {
    return $this->expandButton;
  }
  /**
   * The horizontal alignment of the expand and collapse button.
   *
   * Accepted values: HORIZONTAL_ALIGNMENT_UNSPECIFIED, START, CENTER, END
   *
   * @param self::HORIZONTAL_ALIGNMENT_* $horizontalAlignment
   */
  public function setHorizontalAlignment($horizontalAlignment)
  {
    $this->horizontalAlignment = $horizontalAlignment;
  }
  /**
   * @return self::HORIZONTAL_ALIGNMENT_*
   */
  public function getHorizontalAlignment()
  {
    return $this->horizontalAlignment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1CollapseControl::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1CollapseControl');
