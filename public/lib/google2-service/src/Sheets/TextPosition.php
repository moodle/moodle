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

class TextPosition extends \Google\Model
{
  /**
   * The horizontal alignment is not specified. Do not use this.
   */
  public const HORIZONTAL_ALIGNMENT_HORIZONTAL_ALIGN_UNSPECIFIED = 'HORIZONTAL_ALIGN_UNSPECIFIED';
  /**
   * The text is explicitly aligned to the left of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_LEFT = 'LEFT';
  /**
   * The text is explicitly aligned to the center of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_CENTER = 'CENTER';
  /**
   * The text is explicitly aligned to the right of the cell.
   */
  public const HORIZONTAL_ALIGNMENT_RIGHT = 'RIGHT';
  /**
   * Horizontal alignment setting for the piece of text.
   *
   * @var string
   */
  public $horizontalAlignment;

  /**
   * Horizontal alignment setting for the piece of text.
   *
   * Accepted values: HORIZONTAL_ALIGN_UNSPECIFIED, LEFT, CENTER, RIGHT
   *
   * @param self::HORIZONTAL_ALIGNMENT_* $horizontalAlignment
   */
  public function setHorizontalAlignment($horizontalAlignment)
  {
    $this->horizontalAlignment = $horizontalAlignment;
  }
  /**
   * @return self::HORIZONTAL_ALIGNMENT_*
   */
  public function getHorizontalAlignment()
  {
    return $this->horizontalAlignment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextPosition::class, 'Google_Service_Sheets_TextPosition');
