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

namespace Google\Service\Dfareporting;

class PopupWindowProperties extends \Google\Model
{
  /**
   * window positioning at center.
   */
  public const POSITION_TYPE_CENTER = 'CENTER';
  /**
   * window positioning by upper left corner coordinates.
   */
  public const POSITION_TYPE_COORDINATES = 'COORDINATES';
  protected $dimensionType = Size::class;
  protected $dimensionDataType = '';
  protected $offsetType = OffsetPosition::class;
  protected $offsetDataType = '';
  /**
   * Popup window position either centered or at specific coordinate.
   *
   * @var string
   */
  public $positionType;
  /**
   * Whether to display the browser address bar.
   *
   * @var bool
   */
  public $showAddressBar;
  /**
   * Whether to display the browser menu bar.
   *
   * @var bool
   */
  public $showMenuBar;
  /**
   * Whether to display the browser scroll bar.
   *
   * @var bool
   */
  public $showScrollBar;
  /**
   * Whether to display the browser status bar.
   *
   * @var bool
   */
  public $showStatusBar;
  /**
   * Whether to display the browser tool bar.
   *
   * @var bool
   */
  public $showToolBar;
  /**
   * Title of popup window.
   *
   * @var string
   */
  public $title;

  /**
   * Popup dimension for a creative. This is a read-only field. Applicable to
   * the following creative types: all RICH_MEDIA and all VPAID
   *
   * @param Size $dimension
   */
  public function setDimension(Size $dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return Size
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * Upper-left corner coordinates of the popup window. Applicable if
   * positionType is COORDINATES.
   *
   * @param OffsetPosition $offset
   */
  public function setOffset(OffsetPosition $offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return OffsetPosition
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * Popup window position either centered or at specific coordinate.
   *
   * Accepted values: CENTER, COORDINATES
   *
   * @param self::POSITION_TYPE_* $positionType
   */
  public function setPositionType($positionType)
  {
    $this->positionType = $positionType;
  }
  /**
   * @return self::POSITION_TYPE_*
   */
  public function getPositionType()
  {
    return $this->positionType;
  }
  /**
   * Whether to display the browser address bar.
   *
   * @param bool $showAddressBar
   */
  public function setShowAddressBar($showAddressBar)
  {
    $this->showAddressBar = $showAddressBar;
  }
  /**
   * @return bool
   */
  public function getShowAddressBar()
  {
    return $this->showAddressBar;
  }
  /**
   * Whether to display the browser menu bar.
   *
   * @param bool $showMenuBar
   */
  public function setShowMenuBar($showMenuBar)
  {
    $this->showMenuBar = $showMenuBar;
  }
  /**
   * @return bool
   */
  public function getShowMenuBar()
  {
    return $this->showMenuBar;
  }
  /**
   * Whether to display the browser scroll bar.
   *
   * @param bool $showScrollBar
   */
  public function setShowScrollBar($showScrollBar)
  {
    $this->showScrollBar = $showScrollBar;
  }
  /**
   * @return bool
   */
  public function getShowScrollBar()
  {
    return $this->showScrollBar;
  }
  /**
   * Whether to display the browser status bar.
   *
   * @param bool $showStatusBar
   */
  public function setShowStatusBar($showStatusBar)
  {
    $this->showStatusBar = $showStatusBar;
  }
  /**
   * @return bool
   */
  public function getShowStatusBar()
  {
    return $this->showStatusBar;
  }
  /**
   * Whether to display the browser tool bar.
   *
   * @param bool $showToolBar
   */
  public function setShowToolBar($showToolBar)
  {
    $this->showToolBar = $showToolBar;
  }
  /**
   * @return bool
   */
  public function getShowToolBar()
  {
    return $this->showToolBar;
  }
  /**
   * Title of popup window.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PopupWindowProperties::class, 'Google_Service_Dfareporting_PopupWindowProperties');
