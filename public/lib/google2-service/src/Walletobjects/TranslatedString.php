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

namespace Google\Service\Walletobjects;

class TranslatedString extends \Google\Model
{
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#translatedString"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  /**
   * Represents the BCP 47 language tag. Example values are "en-US", "en-GB",
   * "de", or "de-AT".
   *
   * @var string
   */
  public $language;
  /**
   * The UTF-8 encoded translated string.
   *
   * @var string
   */
  public $value;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#translatedString"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Represents the BCP 47 language tag. Example values are "en-US", "en-GB",
   * "de", or "de-AT".
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The UTF-8 encoded translated string.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TranslatedString::class, 'Google_Service_Walletobjects_TranslatedString');
