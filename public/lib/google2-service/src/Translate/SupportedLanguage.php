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

namespace Google\Service\Translate;

class SupportedLanguage extends \Google\Model
{
  /**
   * Human-readable name of the language localized in the display language
   * specified in the request.
   *
   * @var string
   */
  public $displayName;
  /**
   * Supported language code, generally consisting of its ISO 639-1 identifier,
   * for example, 'en', 'ja'. In certain cases, ISO-639 codes including language
   * and region identifiers are returned (for example, 'zh-TW' and 'zh-CN').
   *
   * @var string
   */
  public $languageCode;
  /**
   * Can be used as a source language.
   *
   * @var bool
   */
  public $supportSource;
  /**
   * Can be used as a target language.
   *
   * @var bool
   */
  public $supportTarget;

  /**
   * Human-readable name of the language localized in the display language
   * specified in the request.
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
   * Supported language code, generally consisting of its ISO 639-1 identifier,
   * for example, 'en', 'ja'. In certain cases, ISO-639 codes including language
   * and region identifiers are returned (for example, 'zh-TW' and 'zh-CN').
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
   * Can be used as a source language.
   *
   * @param bool $supportSource
   */
  public function setSupportSource($supportSource)
  {
    $this->supportSource = $supportSource;
  }
  /**
   * @return bool
   */
  public function getSupportSource()
  {
    return $this->supportSource;
  }
  /**
   * Can be used as a target language.
   *
   * @param bool $supportTarget
   */
  public function setSupportTarget($supportTarget)
  {
    $this->supportTarget = $supportTarget;
  }
  /**
   * @return bool
   */
  public function getSupportTarget()
  {
    return $this->supportTarget;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SupportedLanguage::class, 'Google_Service_Translate_SupportedLanguage');
