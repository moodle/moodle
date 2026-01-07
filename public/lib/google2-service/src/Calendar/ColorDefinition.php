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

namespace Google\Service\Calendar;

class ColorDefinition extends \Google\Model
{
  /**
   * The background color associated with this color definition.
   *
   * @var string
   */
  public $background;
  /**
   * The foreground color that can be used to write on top of a background with
   * 'background' color.
   *
   * @var string
   */
  public $foreground;

  /**
   * The background color associated with this color definition.
   *
   * @param string $background
   */
  public function setBackground($background)
  {
    $this->background = $background;
  }
  /**
   * @return string
   */
  public function getBackground()
  {
    return $this->background;
  }
  /**
   * The foreground color that can be used to write on top of a background with
   * 'background' color.
   *
   * @param string $foreground
   */
  public function setForeground($foreground)
  {
    $this->foreground = $foreground;
  }
  /**
   * @return string
   */
  public function getForeground()
  {
    return $this->foreground;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ColorDefinition::class, 'Google_Service_Calendar_ColorDefinition');
