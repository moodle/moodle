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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaLanguageInfo extends \Google\Model
{
  /**
   * Output only. Language part of normalized_language_code. E.g.: `en-US` ->
   * `en`, `zh-Hans-HK` -> `zh`, `en` -> `en`.
   *
   * @var string
   */
  public $language;
  /**
   * The language code for the DataStore.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. This is the normalized form of language_code. E.g.:
   * language_code of `en-GB`, `en_GB`, `en-UK` or `en-gb` will have
   * normalized_language_code of `en-GB`.
   *
   * @var string
   */
  public $normalizedLanguageCode;
  /**
   * Output only. Region part of normalized_language_code, if present. E.g.:
   * `en-US` -> `US`, `zh-Hans-HK` -> `HK`, `en` -> ``.
   *
   * @var string
   */
  public $region;

  /**
   * Output only. Language part of normalized_language_code. E.g.: `en-US` ->
   * `en`, `zh-Hans-HK` -> `zh`, `en` -> `en`.
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
   * The language code for the DataStore.
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
   * Output only. This is the normalized form of language_code. E.g.:
   * language_code of `en-GB`, `en_GB`, `en-UK` or `en-gb` will have
   * normalized_language_code of `en-GB`.
   *
   * @param string $normalizedLanguageCode
   */
  public function setNormalizedLanguageCode($normalizedLanguageCode)
  {
    $this->normalizedLanguageCode = $normalizedLanguageCode;
  }
  /**
   * @return string
   */
  public function getNormalizedLanguageCode()
  {
    return $this->normalizedLanguageCode;
  }
  /**
   * Output only. Region part of normalized_language_code, if present. E.g.:
   * `en-US` -> `US`, `zh-Hans-HK` -> `HK`, `en` -> ``.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaLanguageInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaLanguageInfo');
