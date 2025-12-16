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

class PageBackgroundFill extends \Google\Model
{
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
   * The background fill property state. Updating the fill on a page will
   * implicitly update this field to `RENDERED`, unless another value is
   * specified in the same request. To have no fill on a page, set this field to
   * `NOT_RENDERED`. In this case, any other fill fields set in the same request
   * will be ignored.
   *
   * @var string
   */
  public $propertyState;
  protected $solidFillType = SolidFill::class;
  protected $solidFillDataType = '';
  protected $stretchedPictureFillType = StretchedPictureFill::class;
  protected $stretchedPictureFillDataType = '';

  /**
   * The background fill property state. Updating the fill on a page will
   * implicitly update this field to `RENDERED`, unless another value is
   * specified in the same request. To have no fill on a page, set this field to
   * `NOT_RENDERED`. In this case, any other fill fields set in the same request
   * will be ignored.
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
   * Solid color fill.
   *
   * @param SolidFill $solidFill
   */
  public function setSolidFill(SolidFill $solidFill)
  {
    $this->solidFill = $solidFill;
  }
  /**
   * @return SolidFill
   */
  public function getSolidFill()
  {
    return $this->solidFill;
  }
  /**
   * Stretched picture fill.
   *
   * @param StretchedPictureFill $stretchedPictureFill
   */
  public function setStretchedPictureFill(StretchedPictureFill $stretchedPictureFill)
  {
    $this->stretchedPictureFill = $stretchedPictureFill;
  }
  /**
   * @return StretchedPictureFill
   */
  public function getStretchedPictureFill()
  {
    return $this->stretchedPictureFill;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PageBackgroundFill::class, 'Google_Service_Slides_PageBackgroundFill');
