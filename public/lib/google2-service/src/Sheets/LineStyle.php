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

class LineStyle extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const TYPE_LINE_DASH_TYPE_UNSPECIFIED = 'LINE_DASH_TYPE_UNSPECIFIED';
  /**
   * No dash type, which is equivalent to a non-visible line.
   */
  public const TYPE_INVISIBLE = 'INVISIBLE';
  /**
   * A custom dash for a line. Modifying the exact custom dash style is
   * currently unsupported.
   */
  public const TYPE_CUSTOM = 'CUSTOM';
  /**
   * A solid line.
   */
  public const TYPE_SOLID = 'SOLID';
  /**
   * A dotted line.
   */
  public const TYPE_DOTTED = 'DOTTED';
  /**
   * A dashed line where the dashes have "medium" length.
   */
  public const TYPE_MEDIUM_DASHED = 'MEDIUM_DASHED';
  /**
   * A line that alternates between a "medium" dash and a dot.
   */
  public const TYPE_MEDIUM_DASHED_DOTTED = 'MEDIUM_DASHED_DOTTED';
  /**
   * A dashed line where the dashes have "long" length.
   */
  public const TYPE_LONG_DASHED = 'LONG_DASHED';
  /**
   * A line that alternates between a "long" dash and a dot.
   */
  public const TYPE_LONG_DASHED_DOTTED = 'LONG_DASHED_DOTTED';
  /**
   * The dash type of the line.
   *
   * @var string
   */
  public $type;
  /**
   * The thickness of the line, in px.
   *
   * @var int
   */
  public $width;

  /**
   * The dash type of the line.
   *
   * Accepted values: LINE_DASH_TYPE_UNSPECIFIED, INVISIBLE, CUSTOM, SOLID,
   * DOTTED, MEDIUM_DASHED, MEDIUM_DASHED_DOTTED, LONG_DASHED,
   * LONG_DASHED_DOTTED
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
  /**
   * The thickness of the line, in px.
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
class_alias(LineStyle::class, 'Google_Service_Sheets_LineStyle');
