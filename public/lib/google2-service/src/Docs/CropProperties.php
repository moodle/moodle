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

namespace Google\Service\Docs;

class CropProperties extends \Google\Model
{
  /**
   * The clockwise rotation angle of the crop rectangle around its center, in
   * radians. Rotation is applied after the offsets.
   *
   * @var float
   */
  public $angle;
  /**
   * The offset specifies how far inwards the bottom edge of the crop rectangle
   * is from the bottom edge of the original content as a fraction of the
   * original content's height.
   *
   * @var float
   */
  public $offsetBottom;
  /**
   * The offset specifies how far inwards the left edge of the crop rectangle is
   * from the left edge of the original content as a fraction of the original
   * content's width.
   *
   * @var float
   */
  public $offsetLeft;
  /**
   * The offset specifies how far inwards the right edge of the crop rectangle
   * is from the right edge of the original content as a fraction of the
   * original content's width.
   *
   * @var float
   */
  public $offsetRight;
  /**
   * The offset specifies how far inwards the top edge of the crop rectangle is
   * from the top edge of the original content as a fraction of the original
   * content's height.
   *
   * @var float
   */
  public $offsetTop;

  /**
   * The clockwise rotation angle of the crop rectangle around its center, in
   * radians. Rotation is applied after the offsets.
   *
   * @param float $angle
   */
  public function setAngle($angle)
  {
    $this->angle = $angle;
  }
  /**
   * @return float
   */
  public function getAngle()
  {
    return $this->angle;
  }
  /**
   * The offset specifies how far inwards the bottom edge of the crop rectangle
   * is from the bottom edge of the original content as a fraction of the
   * original content's height.
   *
   * @param float $offsetBottom
   */
  public function setOffsetBottom($offsetBottom)
  {
    $this->offsetBottom = $offsetBottom;
  }
  /**
   * @return float
   */
  public function getOffsetBottom()
  {
    return $this->offsetBottom;
  }
  /**
   * The offset specifies how far inwards the left edge of the crop rectangle is
   * from the left edge of the original content as a fraction of the original
   * content's width.
   *
   * @param float $offsetLeft
   */
  public function setOffsetLeft($offsetLeft)
  {
    $this->offsetLeft = $offsetLeft;
  }
  /**
   * @return float
   */
  public function getOffsetLeft()
  {
    return $this->offsetLeft;
  }
  /**
   * The offset specifies how far inwards the right edge of the crop rectangle
   * is from the right edge of the original content as a fraction of the
   * original content's width.
   *
   * @param float $offsetRight
   */
  public function setOffsetRight($offsetRight)
  {
    $this->offsetRight = $offsetRight;
  }
  /**
   * @return float
   */
  public function getOffsetRight()
  {
    return $this->offsetRight;
  }
  /**
   * The offset specifies how far inwards the top edge of the crop rectangle is
   * from the top edge of the original content as a fraction of the original
   * content's height.
   *
   * @param float $offsetTop
   */
  public function setOffsetTop($offsetTop)
  {
    $this->offsetTop = $offsetTop;
  }
  /**
   * @return float
   */
  public function getOffsetTop()
  {
    return $this->offsetTop;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CropProperties::class, 'Google_Service_Docs_CropProperties');
