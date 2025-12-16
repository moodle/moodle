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

namespace Google\Service\CloudSearch;

class BackgroundColoredText extends \Google\Model
{
  public const BACKGROUND_COLOR_UNKNOWN_COLOR = 'UNKNOWN_COLOR';
  public const BACKGROUND_COLOR_WHITE = 'WHITE';
  public const BACKGROUND_COLOR_YELLOW = 'YELLOW';
  public const BACKGROUND_COLOR_ORANGE = 'ORANGE';
  public const BACKGROUND_COLOR_GREEN = 'GREEN';
  public const BACKGROUND_COLOR_BLUE = 'BLUE';
  public const BACKGROUND_COLOR_GREY = 'GREY';
  /**
   * [Optional] Color of the background. The text color can change depending on
   * the selected background color, and the client does not have control over
   * this. If missing, the background will be WHITE.
   *
   * @var string
   */
  public $backgroundColor;
  /**
   * [Required] The text to display.
   *
   * @var string
   */
  public $text;

  /**
   * [Optional] Color of the background. The text color can change depending on
   * the selected background color, and the client does not have control over
   * this. If missing, the background will be WHITE.
   *
   * Accepted values: UNKNOWN_COLOR, WHITE, YELLOW, ORANGE, GREEN, BLUE, GREY
   *
   * @param self::BACKGROUND_COLOR_* $backgroundColor
   */
  public function setBackgroundColor($backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return self::BACKGROUND_COLOR_*
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * [Required] The text to display.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackgroundColoredText::class, 'Google_Service_CloudSearch_BackgroundColoredText');
