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

class ShapeProperties extends \Google\Model
{
  /**
   * An unspecified content alignment. The content alignment is inherited from
   * the parent if it exists.
   */
  public const CONTENT_ALIGNMENT_CONTENT_ALIGNMENT_UNSPECIFIED = 'CONTENT_ALIGNMENT_UNSPECIFIED';
  /**
   * An unsupported content alignment.
   */
  public const CONTENT_ALIGNMENT_CONTENT_ALIGNMENT_UNSUPPORTED = 'CONTENT_ALIGNMENT_UNSUPPORTED';
  /**
   * An alignment that aligns the content to the top of the content holder.
   * Corresponds to ECMA-376 ST_TextAnchoringType 't'.
   */
  public const CONTENT_ALIGNMENT_TOP = 'TOP';
  /**
   * An alignment that aligns the content to the middle of the content holder.
   * Corresponds to ECMA-376 ST_TextAnchoringType 'ctr'.
   */
  public const CONTENT_ALIGNMENT_MIDDLE = 'MIDDLE';
  /**
   * An alignment that aligns the content to the bottom of the content holder.
   * Corresponds to ECMA-376 ST_TextAnchoringType 'b'.
   */
  public const CONTENT_ALIGNMENT_BOTTOM = 'BOTTOM';
  protected $autofitType = Autofit::class;
  protected $autofitDataType = '';
  /**
   * The alignment of the content in the shape. If unspecified, the alignment is
   * inherited from a parent placeholder if it exists. If the shape has no
   * parent, the default alignment matches the alignment for new shapes created
   * in the Slides editor.
   *
   * @var string
   */
  public $contentAlignment;
  protected $linkType = Link::class;
  protected $linkDataType = '';
  protected $outlineType = Outline::class;
  protected $outlineDataType = '';
  protected $shadowType = Shadow::class;
  protected $shadowDataType = '';
  protected $shapeBackgroundFillType = ShapeBackgroundFill::class;
  protected $shapeBackgroundFillDataType = '';

  /**
   * The autofit properties of the shape. This property is only set for shapes
   * that allow text.
   *
   * @param Autofit $autofit
   */
  public function setAutofit(Autofit $autofit)
  {
    $this->autofit = $autofit;
  }
  /**
   * @return Autofit
   */
  public function getAutofit()
  {
    return $this->autofit;
  }
  /**
   * The alignment of the content in the shape. If unspecified, the alignment is
   * inherited from a parent placeholder if it exists. If the shape has no
   * parent, the default alignment matches the alignment for new shapes created
   * in the Slides editor.
   *
   * Accepted values: CONTENT_ALIGNMENT_UNSPECIFIED,
   * CONTENT_ALIGNMENT_UNSUPPORTED, TOP, MIDDLE, BOTTOM
   *
   * @param self::CONTENT_ALIGNMENT_* $contentAlignment
   */
  public function setContentAlignment($contentAlignment)
  {
    $this->contentAlignment = $contentAlignment;
  }
  /**
   * @return self::CONTENT_ALIGNMENT_*
   */
  public function getContentAlignment()
  {
    return $this->contentAlignment;
  }
  /**
   * The hyperlink destination of the shape. If unset, there is no link. Links
   * are not inherited from parent placeholders.
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
   * The outline of the shape. If unset, the outline is inherited from a parent
   * placeholder if it exists. If the shape has no parent, then the default
   * outline depends on the shape type, matching the defaults for new shapes
   * created in the Slides editor.
   *
   * @param Outline $outline
   */
  public function setOutline(Outline $outline)
  {
    $this->outline = $outline;
  }
  /**
   * @return Outline
   */
  public function getOutline()
  {
    return $this->outline;
  }
  /**
   * The shadow properties of the shape. If unset, the shadow is inherited from
   * a parent placeholder if it exists. If the shape has no parent, then the
   * default shadow matches the defaults for new shapes created in the Slides
   * editor. This property is read-only.
   *
   * @param Shadow $shadow
   */
  public function setShadow(Shadow $shadow)
  {
    $this->shadow = $shadow;
  }
  /**
   * @return Shadow
   */
  public function getShadow()
  {
    return $this->shadow;
  }
  /**
   * The background fill of the shape. If unset, the background fill is
   * inherited from a parent placeholder if it exists. If the shape has no
   * parent, then the default background fill depends on the shape type,
   * matching the defaults for new shapes created in the Slides editor.
   *
   * @param ShapeBackgroundFill $shapeBackgroundFill
   */
  public function setShapeBackgroundFill(ShapeBackgroundFill $shapeBackgroundFill)
  {
    $this->shapeBackgroundFill = $shapeBackgroundFill;
  }
  /**
   * @return ShapeBackgroundFill
   */
  public function getShapeBackgroundFill()
  {
    return $this->shapeBackgroundFill;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShapeProperties::class, 'Google_Service_Slides_ShapeProperties');
