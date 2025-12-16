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

namespace Google\Service\ToolResults;

class RegionProto extends \Google\Model
{
  /**
   * The height, in pixels. Always set.
   *
   * @var int
   */
  public $heightPx;
  /**
   * The left side of the rectangle, in pixels. Always set.
   *
   * @var int
   */
  public $leftPx;
  /**
   * The top of the rectangle, in pixels. Always set.
   *
   * @var int
   */
  public $topPx;
  /**
   * The width, in pixels. Always set.
   *
   * @var int
   */
  public $widthPx;

  /**
   * The height, in pixels. Always set.
   *
   * @param int $heightPx
   */
  public function setHeightPx($heightPx)
  {
    $this->heightPx = $heightPx;
  }
  /**
   * @return int
   */
  public function getHeightPx()
  {
    return $this->heightPx;
  }
  /**
   * The left side of the rectangle, in pixels. Always set.
   *
   * @param int $leftPx
   */
  public function setLeftPx($leftPx)
  {
    $this->leftPx = $leftPx;
  }
  /**
   * @return int
   */
  public function getLeftPx()
  {
    return $this->leftPx;
  }
  /**
   * The top of the rectangle, in pixels. Always set.
   *
   * @param int $topPx
   */
  public function setTopPx($topPx)
  {
    $this->topPx = $topPx;
  }
  /**
   * @return int
   */
  public function getTopPx()
  {
    return $this->topPx;
  }
  /**
   * The width, in pixels. Always set.
   *
   * @param int $widthPx
   */
  public function setWidthPx($widthPx)
  {
    $this->widthPx = $widthPx;
  }
  /**
   * @return int
   */
  public function getWidthPx()
  {
    return $this->widthPx;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionProto::class, 'Google_Service_ToolResults_RegionProto');
