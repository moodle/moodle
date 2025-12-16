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

class LineProperties extends \Google\Model
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
   * An unspecified arrow style.
   */
  public const END_ARROW_ARROW_STYLE_UNSPECIFIED = 'ARROW_STYLE_UNSPECIFIED';
  /**
   * No arrow.
   */
  public const END_ARROW_NONE = 'NONE';
  /**
   * Arrow with notched back. Corresponds to ECMA-376 ST_LineEndType value
   * 'stealth'.
   */
  public const END_ARROW_STEALTH_ARROW = 'STEALTH_ARROW';
  /**
   * Filled arrow. Corresponds to ECMA-376 ST_LineEndType value 'triangle'.
   */
  public const END_ARROW_FILL_ARROW = 'FILL_ARROW';
  /**
   * Filled circle. Corresponds to ECMA-376 ST_LineEndType value 'oval'.
   */
  public const END_ARROW_FILL_CIRCLE = 'FILL_CIRCLE';
  /**
   * Filled square.
   */
  public const END_ARROW_FILL_SQUARE = 'FILL_SQUARE';
  /**
   * Filled diamond. Corresponds to ECMA-376 ST_LineEndType value 'diamond'.
   */
  public const END_ARROW_FILL_DIAMOND = 'FILL_DIAMOND';
  /**
   * Hollow arrow.
   */
  public const END_ARROW_OPEN_ARROW = 'OPEN_ARROW';
  /**
   * Hollow circle.
   */
  public const END_ARROW_OPEN_CIRCLE = 'OPEN_CIRCLE';
  /**
   * Hollow square.
   */
  public const END_ARROW_OPEN_SQUARE = 'OPEN_SQUARE';
  /**
   * Hollow diamond.
   */
  public const END_ARROW_OPEN_DIAMOND = 'OPEN_DIAMOND';
  /**
   * An unspecified arrow style.
   */
  public const START_ARROW_ARROW_STYLE_UNSPECIFIED = 'ARROW_STYLE_UNSPECIFIED';
  /**
   * No arrow.
   */
  public const START_ARROW_NONE = 'NONE';
  /**
   * Arrow with notched back. Corresponds to ECMA-376 ST_LineEndType value
   * 'stealth'.
   */
  public const START_ARROW_STEALTH_ARROW = 'STEALTH_ARROW';
  /**
   * Filled arrow. Corresponds to ECMA-376 ST_LineEndType value 'triangle'.
   */
  public const START_ARROW_FILL_ARROW = 'FILL_ARROW';
  /**
   * Filled circle. Corresponds to ECMA-376 ST_LineEndType value 'oval'.
   */
  public const START_ARROW_FILL_CIRCLE = 'FILL_CIRCLE';
  /**
   * Filled square.
   */
  public const START_ARROW_FILL_SQUARE = 'FILL_SQUARE';
  /**
   * Filled diamond. Corresponds to ECMA-376 ST_LineEndType value 'diamond'.
   */
  public const START_ARROW_FILL_DIAMOND = 'FILL_DIAMOND';
  /**
   * Hollow arrow.
   */
  public const START_ARROW_OPEN_ARROW = 'OPEN_ARROW';
  /**
   * Hollow circle.
   */
  public const START_ARROW_OPEN_CIRCLE = 'OPEN_CIRCLE';
  /**
   * Hollow square.
   */
  public const START_ARROW_OPEN_SQUARE = 'OPEN_SQUARE';
  /**
   * Hollow diamond.
   */
  public const START_ARROW_OPEN_DIAMOND = 'OPEN_DIAMOND';
  /**
   * The dash style of the line.
   *
   * @var string
   */
  public $dashStyle;
  /**
   * The style of the arrow at the end of the line.
   *
   * @var string
   */
  public $endArrow;
  protected $endConnectionType = LineConnection::class;
  protected $endConnectionDataType = '';
  protected $lineFillType = LineFill::class;
  protected $lineFillDataType = '';
  protected $linkType = Link::class;
  protected $linkDataType = '';
  /**
   * The style of the arrow at the beginning of the line.
   *
   * @var string
   */
  public $startArrow;
  protected $startConnectionType = LineConnection::class;
  protected $startConnectionDataType = '';
  protected $weightType = Dimension::class;
  protected $weightDataType = '';

  /**
   * The dash style of the line.
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
   * The style of the arrow at the end of the line.
   *
   * Accepted values: ARROW_STYLE_UNSPECIFIED, NONE, STEALTH_ARROW, FILL_ARROW,
   * FILL_CIRCLE, FILL_SQUARE, FILL_DIAMOND, OPEN_ARROW, OPEN_CIRCLE,
   * OPEN_SQUARE, OPEN_DIAMOND
   *
   * @param self::END_ARROW_* $endArrow
   */
  public function setEndArrow($endArrow)
  {
    $this->endArrow = $endArrow;
  }
  /**
   * @return self::END_ARROW_*
   */
  public function getEndArrow()
  {
    return $this->endArrow;
  }
  /**
   * The connection at the end of the line. If unset, there is no connection.
   * Only lines with a Type indicating it is a "connector" can have an
   * `end_connection`.
   *
   * @param LineConnection $endConnection
   */
  public function setEndConnection(LineConnection $endConnection)
  {
    $this->endConnection = $endConnection;
  }
  /**
   * @return LineConnection
   */
  public function getEndConnection()
  {
    return $this->endConnection;
  }
  /**
   * The fill of the line. The default line fill matches the defaults for new
   * lines created in the Slides editor.
   *
   * @param LineFill $lineFill
   */
  public function setLineFill(LineFill $lineFill)
  {
    $this->lineFill = $lineFill;
  }
  /**
   * @return LineFill
   */
  public function getLineFill()
  {
    return $this->lineFill;
  }
  /**
   * The hyperlink destination of the line. If unset, there is no link.
   *
   * @param Link $link
   */
  public function setLink(Link $link)
  {
    $this->link = $link;
  }
  /**
   * @return Link
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * The style of the arrow at the beginning of the line.
   *
   * Accepted values: ARROW_STYLE_UNSPECIFIED, NONE, STEALTH_ARROW, FILL_ARROW,
   * FILL_CIRCLE, FILL_SQUARE, FILL_DIAMOND, OPEN_ARROW, OPEN_CIRCLE,
   * OPEN_SQUARE, OPEN_DIAMOND
   *
   * @param self::START_ARROW_* $startArrow
   */
  public function setStartArrow($startArrow)
  {
    $this->startArrow = $startArrow;
  }
  /**
   * @return self::START_ARROW_*
   */
  public function getStartArrow()
  {
    return $this->startArrow;
  }
  /**
   * The connection at the beginning of the line. If unset, there is no
   * connection. Only lines with a Type indicating it is a "connector" can have
   * a `start_connection`.
   *
   * @param LineConnection $startConnection
   */
  public function setStartConnection(LineConnection $startConnection)
  {
    $this->startConnection = $startConnection;
  }
  /**
   * @return LineConnection
   */
  public function getStartConnection()
  {
    return $this->startConnection;
  }
  /**
   * The thickness of the line.
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
class_alias(LineProperties::class, 'Google_Service_Slides_LineProperties');
