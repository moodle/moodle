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

class PositionedObjectPositioning extends \Google\Model
{
  /**
   * The layout is unspecified.
   */
  public const LAYOUT_POSITIONED_OBJECT_LAYOUT_UNSPECIFIED = 'POSITIONED_OBJECT_LAYOUT_UNSPECIFIED';
  /**
   * The text wraps around the positioned object.
   */
  public const LAYOUT_WRAP_TEXT = 'WRAP_TEXT';
  /**
   * Breaks text such that the positioned object is on the left and text is on
   * the right.
   */
  public const LAYOUT_BREAK_LEFT = 'BREAK_LEFT';
  /**
   * Breaks text such that the positioned object is on the right and text is on
   * the left.
   */
  public const LAYOUT_BREAK_RIGHT = 'BREAK_RIGHT';
  /**
   * Breaks text such that there's no text on the left or right of the
   * positioned object.
   */
  public const LAYOUT_BREAK_LEFT_RIGHT = 'BREAK_LEFT_RIGHT';
  /**
   * The positioned object is in front of the text.
   */
  public const LAYOUT_IN_FRONT_OF_TEXT = 'IN_FRONT_OF_TEXT';
  /**
   * The positioned object is behind the text.
   */
  public const LAYOUT_BEHIND_TEXT = 'BEHIND_TEXT';
  /**
   * The layout of this positioned object.
   *
   * @var string
   */
  public $layout;
  protected $leftOffsetType = Dimension::class;
  protected $leftOffsetDataType = '';
  protected $topOffsetType = Dimension::class;
  protected $topOffsetDataType = '';

  /**
   * The layout of this positioned object.
   *
   * Accepted values: POSITIONED_OBJECT_LAYOUT_UNSPECIFIED, WRAP_TEXT,
   * BREAK_LEFT, BREAK_RIGHT, BREAK_LEFT_RIGHT, IN_FRONT_OF_TEXT, BEHIND_TEXT
   *
   * @param self::LAYOUT_* $layout
   */
  public function setLayout($layout)
  {
    $this->layout = $layout;
  }
  /**
   * @return self::LAYOUT_*
   */
  public function getLayout()
  {
    return $this->layout;
  }
  /**
   * The offset of the left edge of the positioned object relative to the
   * beginning of the Paragraph it's tethered to. The exact positioning of the
   * object can depend on other content in the document and the document's
   * styling.
   *
   * @param Dimension $leftOffset
   */
  public function setLeftOffset(Dimension $leftOffset)
  {
    $this->leftOffset = $leftOffset;
  }
  /**
   * @return Dimension
   */
  public function getLeftOffset()
  {
    return $this->leftOffset;
  }
  /**
   * The offset of the top edge of the positioned object relative to the
   * beginning of the Paragraph it's tethered to. The exact positioning of the
   * object can depend on other content in the document and the document's
   * styling.
   *
   * @param Dimension $topOffset
   */
  public function setTopOffset(Dimension $topOffset)
  {
    $this->topOffset = $topOffset;
  }
  /**
   * @return Dimension
   */
  public function getTopOffset()
  {
    return $this->topOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PositionedObjectPositioning::class, 'Google_Service_Docs_PositionedObjectPositioning');
