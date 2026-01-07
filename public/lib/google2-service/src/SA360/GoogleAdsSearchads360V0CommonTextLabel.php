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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonTextLabel extends \Google\Model
{
  /**
   * Background color of the label in HEX format. This string must match the
   * regular expression '^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$'. Note: The
   * background color may not be visible for manager accounts.
   *
   * @var string
   */
  public $backgroundColor;
  /**
   * A short description of the label. The length must be no more than 200
   * characters.
   *
   * @var string
   */
  public $description;

  /**
   * Background color of the label in HEX format. This string must match the
   * regular expression '^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$'. Note: The
   * background color may not be visible for manager accounts.
   *
   * @param string $backgroundColor
   */
  public function setBackgroundColor($backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return string
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * A short description of the label. The length must be no more than 200
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonTextLabel::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonTextLabel');
