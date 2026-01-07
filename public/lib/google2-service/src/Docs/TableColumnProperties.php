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

class TableColumnProperties extends \Google\Model
{
  /**
   * The column width type is unspecified.
   */
  public const WIDTH_TYPE_WIDTH_TYPE_UNSPECIFIED = 'WIDTH_TYPE_UNSPECIFIED';
  /**
   * The column width is evenly distributed among the other evenly distributed
   * columns. The width of the column is automatically determined and will have
   * an equal portion of the width remaining for the table after accounting for
   * all columns with specified widths.
   */
  public const WIDTH_TYPE_EVENLY_DISTRIBUTED = 'EVENLY_DISTRIBUTED';
  /**
   * A fixed column width. The width property contains the column's width.
   */
  public const WIDTH_TYPE_FIXED_WIDTH = 'FIXED_WIDTH';
  protected $widthDataType = '';
  /**
   * The width type of the column.
   *
   * @var string
   */
  public $widthType;

  /**
   * The width of the column. Set when the column's `width_type` is FIXED_WIDTH.
   *
   * @param Dimension $width
   */
  public function setWidth(Dimension $width)
  {
    $this->width = $width;
  }
  /**
   * @return Dimension
   */
  public function getWidth()
  {
    return $this->width;
  }
  /**
   * The width type of the column.
   *
   * Accepted values: WIDTH_TYPE_UNSPECIFIED, EVENLY_DISTRIBUTED, FIXED_WIDTH
   *
   * @param self::WIDTH_TYPE_* $widthType
   */
  public function setWidthType($widthType)
  {
    $this->widthType = $widthType;
  }
  /**
   * @return self::WIDTH_TYPE_*
   */
  public function getWidthType()
  {
    return $this->widthType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableColumnProperties::class, 'Google_Service_Docs_TableColumnProperties');
