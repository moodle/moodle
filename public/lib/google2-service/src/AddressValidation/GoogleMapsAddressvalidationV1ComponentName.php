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

namespace Google\Service\AddressValidation;

class GoogleMapsAddressvalidationV1ComponentName extends \Google\Model
{
  /**
   * The BCP-47 language code. This will not be present if the component name is
   * not associated with a language, such as a street number.
   *
   * @var string
   */
  public $languageCode;
  /**
   * The name text. For example, "5th Avenue" for a street name or "1253" for a
   * street number.
   *
   * @var string
   */
  public $text;

  /**
   * The BCP-47 language code. This will not be present if the component name is
   * not associated with a language, such as a street number.
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
  /**
   * The name text. For example, "5th Avenue" for a street name or "1253" for a
   * street number.
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
class_alias(GoogleMapsAddressvalidationV1ComponentName::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1ComponentName');
