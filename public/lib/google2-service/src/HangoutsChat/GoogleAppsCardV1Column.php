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

class GoogleAppsCardV1Column extends \Google\Collection
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
  /**
   * Don't use. Unspecified.
   */
  public const HORIZONTAL_SIZE_STYLE_HORIZONTAL_SIZE_STYLE_UNSPECIFIED = 'HORIZONTAL_SIZE_STYLE_UNSPECIFIED';
  /**
   * Default value. Column fills the available space, up to 70% of the card's
   * width. If both columns are set to `FILL_AVAILABLE_SPACE`, each column fills
   * 50% of the space.
   */
  public const HORIZONTAL_SIZE_STYLE_FILL_AVAILABLE_SPACE = 'FILL_AVAILABLE_SPACE';
  /**
   * Column fills the least amount of space possible and no more than 30% of the
   * card's width.
   */
  public const HORIZONTAL_SIZE_STYLE_FILL_MINIMUM_SPACE = 'FILL_MINIMUM_SPACE';
  /**
   * Don't use. Unspecified.
   */
  public const VERTICAL_ALIGNMENT_VERTICAL_ALIGNMENT_UNSPECIFIED = 'VERTICAL_ALIGNMENT_UNSPECIFIED';
  /**
   * Default value. Aligns widgets to the center of a column.
   */
  public const VERTICAL_ALIGNMENT_CENTER = 'CENTER';
  /**
   * Aligns widgets to the top of a column.
   */
  public const VERTICAL_ALIGNMENT_TOP = 'TOP';
  /**
   * Aligns widgets to the bottom of a column.
   */
  public const VERTICAL_ALIGNMENT_BOTTOM = 'BOTTOM';
  protected $collection_key = 'widgets';
  /**
   * Specifies whether widgets align to the left, right, or center of a column.
   *
   * @var string
   */
  public $horizontalAlignment;
  /**
   * Specifies how a column fills the width of the card.
   *
   * @var string
   */
  public $horizontalSizeStyle;
  /**
   * Specifies whether widgets align to the top, bottom, or center of a column.
   *
   * @var string
   */
  public $verticalAlignment;
  protected $widgetsType = GoogleAppsCardV1Widgets::class;
  protected $widgetsDataType = 'array';

  /**
   * Specifies whether widgets align to the left, right, or center of a column.
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
  /**
   * Specifies how a column fills the width of the card.
   *
   * Accepted values: HORIZONTAL_SIZE_STYLE_UNSPECIFIED, FILL_AVAILABLE_SPACE,
   * FILL_MINIMUM_SPACE
   *
   * @param self::HORIZONTAL_SIZE_STYLE_* $horizontalSizeStyle
   */
  public function setHorizontalSizeStyle($horizontalSizeStyle)
  {
    $this->horizontalSizeStyle = $horizontalSizeStyle;
  }
  /**
   * @return self::HORIZONTAL_SIZE_STYLE_*
   */
  public function getHorizontalSizeStyle()
  {
    return $this->horizontalSizeStyle;
  }
  /**
   * Specifies whether widgets align to the top, bottom, or center of a column.
   *
   * Accepted values: VERTICAL_ALIGNMENT_UNSPECIFIED, CENTER, TOP, BOTTOM
   *
   * @param self::VERTICAL_ALIGNMENT_* $verticalAlignment
   */
  public function setVerticalAlignment($verticalAlignment)
  {
    $this->verticalAlignment = $verticalAlignment;
  }
  /**
   * @return self::VERTICAL_ALIGNMENT_*
   */
  public function getVerticalAlignment()
  {
    return $this->verticalAlignment;
  }
  /**
   * An array of widgets included in a column. Widgets appear in the order that
   * they are specified.
   *
   * @param GoogleAppsCardV1Widgets[] $widgets
   */
  public function setWidgets($widgets)
  {
    $this->widgets = $widgets;
  }
  /**
   * @return GoogleAppsCardV1Widgets[]
   */
  public function getWidgets()
  {
    return $this->widgets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Column::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Column');
