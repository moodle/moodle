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

namespace Google\Service\Dfareporting;

class Language extends \Google\Model
{
  /**
   * Language ID of this language. This is the ID used for targeting and
   * generating reports.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#language".
   *
   * @var string
   */
  public $kind;
  /**
   * Format of language code is an ISO 639 two-letter language code optionally
   * followed by an underscore followed by an ISO 3166 code. Examples are "en"
   * for English or "zh_CN" for Simplified Chinese.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Name of this language.
   *
   * @var string
   */
  public $name;

  /**
   * Language ID of this language. This is the ID used for targeting and
   * generating reports.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#language".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Format of language code is an ISO 639 two-letter language code optionally
   * followed by an underscore followed by an ISO 3166 code. Examples are "en"
   * for English or "zh_CN" for Simplified Chinese.
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
   * Name of this language.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Language::class, 'Google_Service_Dfareporting_Language');
