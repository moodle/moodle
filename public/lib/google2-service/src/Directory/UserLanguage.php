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

namespace Google\Service\Directory;

class UserLanguage extends \Google\Model
{
  /**
   * Other language. User can provide their own language name if there is no
   * corresponding ISO 639 language code. If this is set, `languageCode` can't
   * be set.
   *
   * @var string
   */
  public $customLanguage;
  /**
   * ISO 639 string representation of a language. See [Language Codes](/admin-
   * sdk/directory/v1/languages) for the list of supported codes. Valid language
   * codes outside the supported set will be accepted by the API but may lead to
   * unexpected behavior. Illegal values cause `SchemaException`. If this is
   * set, `customLanguage` can't be set.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. If present, controls whether the specified `languageCode` is the
   * user's preferred language. If `customLanguage` is set, this can't be set.
   * Allowed values are `preferred` and `not_preferred`.
   *
   * @var string
   */
  public $preference;

  /**
   * Other language. User can provide their own language name if there is no
   * corresponding ISO 639 language code. If this is set, `languageCode` can't
   * be set.
   *
   * @param string $customLanguage
   */
  public function setCustomLanguage($customLanguage)
  {
    $this->customLanguage = $customLanguage;
  }
  /**
   * @return string
   */
  public function getCustomLanguage()
  {
    return $this->customLanguage;
  }
  /**
   * ISO 639 string representation of a language. See [Language Codes](/admin-
   * sdk/directory/v1/languages) for the list of supported codes. Valid language
   * codes outside the supported set will be accepted by the API but may lead to
   * unexpected behavior. Illegal values cause `SchemaException`. If this is
   * set, `customLanguage` can't be set.
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
   * Optional. If present, controls whether the specified `languageCode` is the
   * user's preferred language. If `customLanguage` is set, this can't be set.
   * Allowed values are `preferred` and `not_preferred`.
   *
   * @param string $preference
   */
  public function setPreference($preference)
  {
    $this->preference = $preference;
  }
  /**
   * @return string
   */
  public function getPreference()
  {
    return $this->preference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserLanguage::class, 'Google_Service_Directory_UserLanguage');
