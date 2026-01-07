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

namespace Google\Service\Slides;

class CropProperties extends \Google\Model
{
  /**
   * The rotation angle of the crop window around its center, in radians.
   * Rotation angle is applied after the offset.
   *
   * @var float
   */
  public $angle;
  /**
   * The offset specifies the bottom edge of the crop rectangle that is located
   * above the original bounding rectangle bottom edge, relative to the object's
   * original height.
   *
   * @var float
   */
  public $bottomOffset;
  /**
   * The offset specifies the left edge of the crop rectangle that is located to
   * the right of the original bounding rectangle left edge, relative to the
   * object's original width.
   *
   * @var float
   */
  public $leftOffset;
  /**
   * The offset specifies the right edge of the crop rectangle that is located
   * to the left of the original bounding rectangle right edge, relative to the
   * object's original width.
   *
   * @var float
   */
  public $rightOffset;
  /**
   * The offset specifies the top edge of the crop rectangle that is located
   * below the original bounding rectangle top edge, relative to the object's
   * original height.
   *
   * @var float
   */
  public $topOffset;

  /**
   * The rotation angle of the crop window around its center, in radians.
   * Rotation angle is applied after the offset.
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
   * The offset specifies the bottom edge of the crop rectangle that is located
   * above the original bounding rectangle bottom edge, relative to the object's
   * original height.
   *
   * @param float $bottomOffset
   */
  public function setBottomOffset($bottomOffset)
  {
    $this->bottomOffset = $bottomOffset;
  }
  /**
   * @return float
   */
  public function getBottomOffset()
  {
    return $this->bottomOffset;
  }
  /**
   * The offset specifies the left edge of the crop rectangle that is located to
   * the right of the original bounding rectangle left edge, relative to the
   * object's original width.
   *
   * @param float $leftOffset
   */
  public function setLeftOffset($leftOffset)
  {
    $this->leftOffset = $leftOffset;
  }
  /**
   * @return float
   */
  public function getLeftOffset()
  {
    return $this->leftOffset;
  }
  /**
   * The offset specifies the right edge of the crop rectangle that is located
   * to the left of the original bounding rectangle right edge, relative to the
   * object's original width.
   *
   * @param float $rightOffset
   */
  public function setRightOffset($rightOffset)
  {
    $this->rightOffset = $rightOffset;
  }
  /**
   * @return float
   */
  public function getRightOffset()
  {
    return $this->rightOffset;
  }
  /**
   * The offset specifies the top edge of the crop rectangle that is located
   * below the original bounding rectangle top edge, relative to the object's
   * original height.
   *
   * @param float $topOffset
   */
  public function setTopOffset($topOffset)
  {
    $this->topOffset = $topOffset;
  }
  /**
   * @return float
   */
  public function getTopOffset()
  {
    return $this->topOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CropProperties::class, 'Google_Service_Slides_CropProperties');
