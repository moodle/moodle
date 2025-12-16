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

class Outline extends \Google\Model
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
   * If a property's state is RENDERED, then the element has the corresponding
   * property when rendered on a page. If the element is a placeholder shape as
   * determined by the placeholder field, and it inherits from a placeholder
   * shape, the corresponding field may be unset, meaning that the property
   * value is inherited from a parent placeholder. If the element does not
   * inherit, then the field will contain the rendered value. This is the
   * default value.
   */
  public const PROPERTY_STATE_RENDERED = 'RENDERED';
  /**
   * If a property's state is NOT_RENDERED, then the element does not have the
   * corresponding property when rendered on a page. However, the field may
   * still be set so it can be inherited by child shapes. To remove a property
   * from a rendered element, set its property_state to NOT_RENDERED.
   */
  public const PROPERTY_STATE_NOT_RENDERED = 'NOT_RENDERED';
  /**
   * If a property's state is INHERIT, then the property state uses the value of
   * corresponding `property_state` field on the parent shape. Elements that do
   * not inherit will never have an INHERIT property state.
   */
  public const PROPERTY_STATE_INHERIT = 'INHERIT';
  /**
   * The dash style of the outline.
   *
   * @var string
   */
  public $dashStyle;
  protected $outlineFillType = OutlineFill::class;
  protected $outlineFillDataType = '';
  /**
   * The outline property state. Updating the outline on a page element will
   * implicitly update this field to `RENDERED`, unless another value is
   * specified in the same request. To have no outline on a page element, set
   * this field to `NOT_RENDERED`. In this case, any other outline fields set in
   * the same request will be ignored.
   *
   * @var string
   */
  public $propertyState;
  protected $weightType = Dimension::class;
  protected $weightDataType = '';

  /**
   * The dash style of the outline.
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
   * The fill of the outline.
   *
   * @param OutlineFill $outlineFill
   */
  public function setOutlineFill(OutlineFill $outlineFill)
  {
    $this->outlineFill = $outlineFill;
  }
  /**
   * @return OutlineFill
   */
  public function getOutlineFill()
  {
    return $this->outlineFill;
  }
  /**
   * The outline property state. Updating the outline on a page element will
   * implicitly update this field to `RENDERED`, unless another value is
   * specified in the same request. To have no outline on a page element, set
   * this field to `NOT_RENDERED`. In this case, any other outline fields set in
   * the same request will be ignored.
   *
   * Accepted values: RENDERED, NOT_RENDERED, INHERIT
   *
   * @param self::PROPERTY_STATE_* $propertyState
   */
  public function setPropertyState($propertyState)
  {
    $this->propertyState = $propertyState;
  }
  /**
   * @return self::PROPERTY_STATE_*
   */
  public function getPropertyState()
  {
    return $this->propertyState;
  }
  /**
   * The thickness of the outline.
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
class_alias(Outline::class, 'Google_Service_Slides_Outline');
