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

class TabStop extends \Google\Model
{
  /**
   * The tab stop alignment is unspecified.
   */
  public const ALIGNMENT_TAB_STOP_ALIGNMENT_UNSPECIFIED = 'TAB_STOP_ALIGNMENT_UNSPECIFIED';
  /**
   * The tab stop is aligned to the start of the line. This is the default.
   */
  public const ALIGNMENT_START = 'START';
  /**
   * The tab stop is aligned to the center of the line.
   */
  public const ALIGNMENT_CENTER = 'CENTER';
  /**
   * The tab stop is aligned to the end of the line.
   */
  public const ALIGNMENT_END = 'END';
  /**
   * The alignment of this tab stop. If unset, the value defaults to START.
   *
   * @var string
   */
  public $alignment;
  protected $offsetType = Dimension::class;
  protected $offsetDataType = '';

  /**
   * The alignment of this tab stop. If unset, the value defaults to START.
   *
   * Accepted values: TAB_STOP_ALIGNMENT_UNSPECIFIED, START, CENTER, END
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
   * The offset between this tab stop and the start margin.
   *
   * @param Dimension $offset
   */
  public function setOffset(Dimension $offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return Dimension
   */
  public function getOffset()
  {
    return $this->offset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TabStop::class, 'Google_Service_Docs_TabStop');
