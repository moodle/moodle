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

namespace Google\Service\Forms;

class MediaProperties extends \Google\Model
{
  /**
   * Default value. Unused.
   */
  public const ALIGNMENT_ALIGNMENT_UNSPECIFIED = 'ALIGNMENT_UNSPECIFIED';
  /**
   * Left align.
   */
  public const ALIGNMENT_LEFT = 'LEFT';
  /**
   * Right align.
   */
  public const ALIGNMENT_RIGHT = 'RIGHT';
  /**
   * Center.
   */
  public const ALIGNMENT_CENTER = 'CENTER';
  /**
   * Position of the media.
   *
   * @var string
   */
  public $alignment;
  /**
   * The width of the media in pixels. When the media is displayed, it is scaled
   * to the smaller of this value or the width of the displayed form. The
   * original aspect ratio of the media is preserved. If a width is not
   * specified when the media is added to the form, it is set to the width of
   * the media source. Width must be between 0 and 740, inclusive. Setting width
   * to 0 or unspecified is only permitted when updating the media source.
   *
   * @var int
   */
  public $width;

  /**
   * Position of the media.
   *
   * Accepted values: ALIGNMENT_UNSPECIFIED, LEFT, RIGHT, CENTER
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
   * The width of the media in pixels. When the media is displayed, it is scaled
   * to the smaller of this value or the width of the displayed form. The
   * original aspect ratio of the media is preserved. If a width is not
   * specified when the media is added to the form, it is set to the width of
   * the media source. Width must be between 0 and 740, inclusive. Setting width
   * to 0 or unspecified is only permitted when updating the media source.
   *
   * @param int $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return int
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MediaProperties::class, 'Google_Service_Forms_MediaProperties');
