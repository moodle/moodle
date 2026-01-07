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

class Shadow extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const ALIGNMENT_RECTANGLE_POSITION_UNSPECIFIED = 'RECTANGLE_POSITION_UNSPECIFIED';
  /**
   * Top left.
   */
  public const ALIGNMENT_TOP_LEFT = 'TOP_LEFT';
  /**
   * Top center.
   */
  public const ALIGNMENT_TOP_CENTER = 'TOP_CENTER';
  /**
   * Top right.
   */
  public const ALIGNMENT_TOP_RIGHT = 'TOP_RIGHT';
  /**
   * Left center.
   */
  public const ALIGNMENT_LEFT_CENTER = 'LEFT_CENTER';
  /**
   * Center.
   */
  public const ALIGNMENT_CENTER = 'CENTER';
  /**
   * Right center.
   */
  public const ALIGNMENT_RIGHT_CENTER = 'RIGHT_CENTER';
  /**
   * Bottom left.
   */
  public const ALIGNMENT_BOTTOM_LEFT = 'BOTTOM_LEFT';
  /**
   * Bottom center.
   */
  public const ALIGNMENT_BOTTOM_CENTER = 'BOTTOM_CENTER';
  /**
   * Bottom right.
   */
  public const ALIGNMENT_BOTTOM_RIGHT = 'BOTTOM_RIGHT';
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
   * Unspecified shadow type.
   */
  public const TYPE_SHADOW_TYPE_UNSPECIFIED = 'SHADOW_TYPE_UNSPECIFIED';
  /**
   * Outer shadow.
   */
  public const TYPE_OUTER = 'OUTER';
  /**
   * The alignment point of the shadow, that sets the origin for translate,
   * scale and skew of the shadow. This property is read-only.
   *
   * @var string
   */
  public $alignment;
  /**
   * The alpha of the shadow's color, from 0.0 to 1.0.
   *
   * @var float
   */
  public $alpha;
  protected $blurRadiusType = Dimension::class;
  protected $blurRadiusDataType = '';
  protected $colorType = OpaqueColor::class;
  protected $colorDataType = '';
  /**
   * The shadow property state. Updating the shadow on a page element will
   * implicitly update this field to `RENDERED`, unless another value is
   * specified in the same request. To have no shadow on a page element, set
   * this field to `NOT_RENDERED`. In this case, any other shadow fields set in
   * the same request will be ignored.
   *
   * @var string
   */
  public $propertyState;
  /**
   * Whether the shadow should rotate with the shape. This property is read-
   * only.
   *
   * @var bool
   */
  public $rotateWithShape;
  protected $transformType = AffineTransform::class;
  protected $transformDataType = '';
  /**
   * The type of the shadow. This property is read-only.
   *
   * @var string
   */
  public $type;

  /**
   * The alignment point of the shadow, that sets the origin for translate,
   * scale and skew of the shadow. This property is read-only.
   *
   * Accepted values: RECTANGLE_POSITION_UNSPECIFIED, TOP_LEFT, TOP_CENTER,
   * TOP_RIGHT, LEFT_CENTER, CENTER, RIGHT_CENTER, BOTTOM_LEFT, BOTTOM_CENTER,
   * BOTTOM_RIGHT
   *
   * @param self::ALIGNMENT_* $alignment
   */
  public function setAlignment($alignment)
  {
    $this->alignment = $alignment;
  }
  /**
   * @return self::ALIGNMENT_*
   */
  public function getAlignment()
  {
    return $this->alignment;
  }
  /**
   * The alpha of the shadow's color, from 0.0 to 1.0.
   *
   * @param float $alpha
   */
  public function setAlpha($alpha)
  {
    $this->alpha = $alpha;
  }
  /**
   * @return float
   */
  public function getAlpha()
  {
    return $this->alpha;
  }
  /**
   * The radius of the shadow blur. The larger the radius, the more diffuse the
   * shadow becomes.
   *
   * @param Dimension $blurRadius
   */
  public function setBlurRadius(Dimension $blurRadius)
  {
    $this->blurRadius = $blurRadius;
  }
  /**
   * @return Dimension
   */
  public function getBlurRadius()
  {
    return $this->blurRadius;
  }
  /**
   * The shadow color value.
   *
   * @param OpaqueColor $color
   */
  public function setColor(OpaqueColor $color)
  {
    $this->color = $color;
  }
  /**
   * @return OpaqueColor
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * The shadow property state. Updating the shadow on a page element will
   * implicitly update this field to `RENDERED`, unless another value is
   * specified in the same request. To have no shadow on a page element, set
   * this field to `NOT_RENDERED`. In this case, any other shadow fields set in
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
   * Whether the shadow should rotate with the shape. This property is read-
   * only.
   *
   * @param bool $rotateWithShape
   */
  public function setRotateWithShape($rotateWithShape)
  {
    $this->rotateWithShape = $rotateWithShape;
  }
  /**
   * @return bool
   */
  public function getRotateWithShape()
  {
    return $this->rotateWithShape;
  }
  /**
   * Transform that encodes the translate, scale, and skew of the shadow,
   * relative to the alignment position.
   *
   * @param AffineTransform $transform
   */
  public function setTransform(AffineTransform $transform)
  {
    $this->transform = $transform;
  }
  /**
   * @return AffineTransform
   */
  public function getTransform()
  {
    return $this->transform;
  }
  /**
   * The type of the shadow. This property is read-only.
   *
   * Accepted values: SHADOW_TYPE_UNSPECIFIED, OUTER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Shadow::class, 'Google_Service_Slides_Shadow');
