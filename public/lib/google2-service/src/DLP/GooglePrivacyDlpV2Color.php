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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Color extends \Google\Model
{
  /**
   * The amount of blue in the color as a value in the interval [0, 1].
   *
   * @var float
   */
  public $blue;
  /**
   * The amount of green in the color as a value in the interval [0, 1].
   *
   * @var float
   */
  public $green;
  /**
   * The amount of red in the color as a value in the interval [0, 1].
   *
   * @var float
   */
  public $red;

  /**
   * The amount of blue in the color as a value in the interval [0, 1].
   *
   * @param float $blue
   */
  public function setBlue($blue)
  {
    $this->blue = $blue;
  }
  /**
   * @return float
   */
  public function getBlue()
  {
    return $this->blue;
  }
  /**
   * The amount of green in the color as a value in the interval [0, 1].
   *
   * @param float $green
   */
  public function setGreen($green)
  {
    $this->green = $green;
  }
  /**
   * @return float
   */
  public function getGreen()
  {
    return $this->green;
  }
  /**
   * The amount of red in the color as a value in the interval [0, 1].
   *
   * @param float $red
   */
  public function setRed($red)
  {
    $this->red = $red;
  }
  /**
   * @return float
   */
  public function getRed()
  {
    return $this->red;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Color::class, 'Google_Service_DLP_GooglePrivacyDlpV2Color');
