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

class TableBorderProperties extends \Google\Model
{
  /**
   * Unspecified dash style.
   */
  public const DASH_STYLE_DASH_STYLE_UNSPECIFIED = 'DASH_STYLE_UNSPECIFIED';
  /**
   * Solid line. Corresponds to ECMA-376 ST_PresetLineDashVal value 'solid'.
   * This is the default dash style.
   */
  public const DASH_STYLE_SOLID = 'SOLID';
  /**
   * Dotted line. Corresponds to ECMA-376 ST_PresetLineDashVal value 'dot'.
   */
  public const DASH_STYLE_DOT = 'DOT';
  /**
   * Dashed line. Corresponds to ECMA-376 ST_PresetLineDashVal value 'dash'.
   */
  public const DASH_STYLE_DASH = 'DASH';
  /**
   * Alternating dashes and dots. Corresponds to ECMA-376 ST_PresetLineDashVal
   * value 'dashDot'.
   */
  public const DASH_STYLE_DASH_DOT = 'DASH_DOT';
  /**
   * Line with large dashes. Corresponds to ECMA-376 ST_PresetLineDashVal value
   * 'lgDash'.
   */
  public const DASH_STYLE_LONG_DASH = 'LONG_DASH';
  /**
   * Alternating large dashes and dots. Corresponds to ECMA-376
   * ST_PresetLineDashVal value 'lgDashDot'.
   */
  public const DASH_STYLE_LONG_DASH_DOT = 'LONG_DASH_DOT';
  /**
   * The dash style of the border.
   *
   * @var string
   */
  public $dashStyle;
  protected $tableBorderFillType = TableBorderFill::class;
  protected $tableBorderFillDataType = '';
  protected $weightType = Dimension::class;
  protected $weightDataType = '';

  /**
   * The dash style of the border.
   *
   * Accepted values: DASH_STYLE_UNSPECIFIED, SOLID, DOT, DASH, DASH_DOT,
   * LONG_DASH, LONG_DASH_DOT
   *
   * @param self::DASH_STYLE_* $dashStyle
   */
  public function setDashStyle($dashStyle)
  {
    $this->dashStyle = $dashStyle;
  }
  /**
   * @return self::DASH_STYLE_*
   */
  public function getDashStyle()
  {
    return $this->dashStyle;
  }
  /**
   * The fill of the table border.
   *
   * @param TableBorderFill $tableBorderFill
   */
  public function setTableBorderFill(TableBorderFill $tableBorderFill)
  {
    $this->tableBorderFill = $tableBorderFill;
  }
  /**
   * @return TableBorderFill
   */
  public function getTableBorderFill()
  {
    return $this->tableBorderFill;
  }
  /**
   * The thickness of the border.
   *
   * @param Dimension $weight
   */
  public function setWeight(Dimension $weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return Dimension
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableBorderProperties::class, 'Google_Service_Slides_TableBorderProperties');
