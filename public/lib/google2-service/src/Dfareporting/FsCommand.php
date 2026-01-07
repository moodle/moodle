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

class FsCommand extends \Google\Model
{
  /**
   * Center of the window Corresponds to "center" in UI
   */
  public const POSITION_OPTION_CENTERED = 'CENTERED';
  /**
   * user-defined distance from top left-hand corner of the window Corresponds
   * to "top-left" in UI
   */
  public const POSITION_OPTION_DISTANCE_FROM_TOP_LEFT_CORNER = 'DISTANCE_FROM_TOP_LEFT_CORNER';
  /**
   * Distance from the left of the browser.Applicable when positionOption is
   * DISTANCE_FROM_TOP_LEFT_CORNER.
   *
   * @var int
   */
  public $left;
  /**
   * Position in the browser where the window will open.
   *
   * @var string
   */
  public $positionOption;
  /**
   * Distance from the top of the browser. Applicable when positionOption is
   * DISTANCE_FROM_TOP_LEFT_CORNER.
   *
   * @var int
   */
  public $top;
  /**
   * Height of the window.
   *
   * @var int
   */
  public $windowHeight;
  /**
   * Width of the window.
   *
   * @var int
   */
  public $windowWidth;

  /**
   * Distance from the left of the browser.Applicable when positionOption is
   * DISTANCE_FROM_TOP_LEFT_CORNER.
   *
   * @param int $left
   */
  public function setLeft($left)
  {
    $this->left = $left;
  }
  /**
   * @return int
   */
  public function getLeft()
  {
    return $this->left;
  }
  /**
   * Position in the browser where the window will open.
   *
   * Accepted values: CENTERED, DISTANCE_FROM_TOP_LEFT_CORNER
   *
   * @param self::POSITION_OPTION_* $positionOption
   */
  public function setPositionOption($positionOption)
  {
    $this->positionOption = $positionOption;
  }
  /**
   * @return self::POSITION_OPTION_*
   */
  public function getPositionOption()
  {
    return $this->positionOption;
  }
  /**
   * Distance from the top of the browser. Applicable when positionOption is
   * DISTANCE_FROM_TOP_LEFT_CORNER.
   *
   * @param int $top
   */
  public function setTop($top)
  {
    $this->top = $top;
  }
  /**
   * @return int
   */
  public function getTop()
  {
    return $this->top;
  }
  /**
   * Height of the window.
   *
   * @param int $windowHeight
   */
  public function setWindowHeight($windowHeight)
  {
    $this->windowHeight = $windowHeight;
  }
  /**
   * @return int
   */
  public function getWindowHeight()
  {
    return $this->windowHeight;
  }
  /**
   * Width of the window.
   *
   * @param int $windowWidth
   */
  public function setWindowWidth($windowWidth)
  {
    $this->windowWidth = $windowWidth;
  }
  /**
   * @return int
   */
  public function getWindowWidth()
  {
    return $this->windowWidth;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FsCommand::class, 'Google_Service_Dfareporting_FsCommand');
