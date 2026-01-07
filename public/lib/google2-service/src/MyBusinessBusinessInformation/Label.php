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

namespace Google\Service\MyBusinessBusinessInformation;

class Label extends \Google\Model
{
  /**
   * Optional. Description of the price list, section, or item.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Display name for the price list, section, or item.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. The BCP-47 language code that these strings apply for. Only one
   * set of labels may be set per language.
   *
   * @var string
   */
  public $languageCode;

  /**
   * Optional. Description of the price list, section, or item.
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
  /**
   * Required. Display name for the price list, section, or item.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. The BCP-47 language code that these strings apply for. Only one
   * set of labels may be set per language.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Label::class, 'Google_Service_MyBusinessBusinessInformation_Label');
