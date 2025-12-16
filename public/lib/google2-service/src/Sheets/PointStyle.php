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

namespace Google\Service\Sheets;

class PointStyle extends \Google\Model
{
  /**
   * Default value.
   */
  public const SHAPE_POINT_SHAPE_UNSPECIFIED = 'POINT_SHAPE_UNSPECIFIED';
  /**
   * A circle shape.
   */
  public const SHAPE_CIRCLE = 'CIRCLE';
  /**
   * A diamond shape.
   */
  public const SHAPE_DIAMOND = 'DIAMOND';
  /**
   * A hexagon shape.
   */
  public const SHAPE_HEXAGON = 'HEXAGON';
  /**
   * A pentagon shape.
   */
  public const SHAPE_PENTAGON = 'PENTAGON';
  /**
   * A square shape.
   */
  public const SHAPE_SQUARE = 'SQUARE';
  /**
   * A star shape.
   */
  public const SHAPE_STAR = 'STAR';
  /**
   * A triangle shape.
   */
  public const SHAPE_TRIANGLE = 'TRIANGLE';
  /**
   * An x-mark shape.
   */
  public const SHAPE_X_MARK = 'X_MARK';
  /**
   * The point shape. If empty or unspecified, a default shape is used.
   *
   * @var string
   */
  public $shape;
  /**
   * The point size. If empty, a default size is used.
   *
   * @var 
   */
  public $size;

  /**
   * The point shape. If empty or unspecified, a default shape is used.
   *
   * Accepted values: POINT_SHAPE_UNSPECIFIED, CIRCLE, DIAMOND, HEXAGON,
   * PENTAGON, SQUARE, STAR, TRIANGLE, X_MARK
   *
   * @param self::SHAPE_* $shape
   */
  public function setShape($shape)
  {
    $this->shape = $shape;
  }
  /**
   * @return self::SHAPE_*
   */
  public function getShape()
  {
    return $this->shape;
  }
  public function setSize($size)
  {
    $this->size = $size;
  }
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PointStyle::class, 'Google_Service_Sheets_PointStyle');
