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

class Autofit extends \Google\Model
{
  /**
   * The autofit type is unspecified.
   */
  public const AUTOFIT_TYPE_AUTOFIT_TYPE_UNSPECIFIED = 'AUTOFIT_TYPE_UNSPECIFIED';
  /**
   * Do not autofit.
   */
  public const AUTOFIT_TYPE_NONE = 'NONE';
  /**
   * Shrink text on overflow to fit the shape.
   */
  public const AUTOFIT_TYPE_TEXT_AUTOFIT = 'TEXT_AUTOFIT';
  /**
   * Resize the shape to fit the text.
   */
  public const AUTOFIT_TYPE_SHAPE_AUTOFIT = 'SHAPE_AUTOFIT';
  /**
   * The autofit type of the shape. If the autofit type is
   * AUTOFIT_TYPE_UNSPECIFIED, the autofit type is inherited from a parent
   * placeholder if it exists. The field is automatically set to NONE if a
   * request is made that might affect text fitting within its bounding text
   * box. In this case, the font_scale is applied to the font_size and the
   * line_spacing_reduction is applied to the line_spacing. Both properties are
   * also reset to default values.
   *
   * @var string
   */
  public $autofitType;
  /**
   * The font scale applied to the shape. For shapes with autofit_type NONE or
   * SHAPE_AUTOFIT, this value is the default value of 1. For TEXT_AUTOFIT, this
   * value multiplied by the font_size gives the font size that's rendered in
   * the editor. This property is read-only.
   *
   * @var float
   */
  public $fontScale;
  /**
   * The line spacing reduction applied to the shape. For shapes with
   * autofit_type NONE or SHAPE_AUTOFIT, this value is the default value of 0.
   * For TEXT_AUTOFIT, this value subtracted from the line_spacing gives the
   * line spacing that's rendered in the editor. This property is read-only.
   *
   * @var float
   */
  public $lineSpacingReduction;

  /**
   * The autofit type of the shape. If the autofit type is
   * AUTOFIT_TYPE_UNSPECIFIED, the autofit type is inherited from a parent
   * placeholder if it exists. The field is automatically set to NONE if a
   * request is made that might affect text fitting within its bounding text
   * box. In this case, the font_scale is applied to the font_size and the
   * line_spacing_reduction is applied to the line_spacing. Both properties are
   * also reset to default values.
   *
   * Accepted values: AUTOFIT_TYPE_UNSPECIFIED, NONE, TEXT_AUTOFIT,
   * SHAPE_AUTOFIT
   *
   * @param self::AUTOFIT_TYPE_* $autofitType
   */
  public function setAutofitType($autofitType)
  {
    $this->autofitType = $autofitType;
  }
  /**
   * @return self::AUTOFIT_TYPE_*
   */
  public function getAutofitType()
  {
    return $this->autofitType;
  }
  /**
   * The font scale applied to the shape. For shapes with autofit_type NONE or
   * SHAPE_AUTOFIT, this value is the default value of 1. For TEXT_AUTOFIT, this
   * value multiplied by the font_size gives the font size that's rendered in
   * the editor. This property is read-only.
   *
   * @param float $fontScale
   */
  public function setFontScale($fontScale)
  {
    $this->fontScale = $fontScale;
  }
  /**
   * @return float
   */
  public function getFontScale()
  {
    return $this->fontScale;
  }
  /**
   * The line spacing reduction applied to the shape. For shapes with
   * autofit_type NONE or SHAPE_AUTOFIT, this value is the default value of 0.
   * For TEXT_AUTOFIT, this value subtracted from the line_spacing gives the
   * line spacing that's rendered in the editor. This property is read-only.
   *
   * @param float $lineSpacingReduction
   */
  public function setLineSpacingReduction($lineSpacingReduction)
  {
    $this->lineSpacingReduction = $lineSpacingReduction;
  }
  /**
   * @return float
   */
  public function getLineSpacingReduction()
  {
    return $this->lineSpacingReduction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Autofit::class, 'Google_Service_Slides_Autofit');
