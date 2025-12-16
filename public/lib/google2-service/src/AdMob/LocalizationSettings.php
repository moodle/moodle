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

namespace Google\Service\AdMob;

class LocalizationSettings extends \Google\Model
{
  /**
   * Currency code of the earning related metrics, which is the 3-letter code
   * defined in ISO 4217. The daily average rate is used for the currency
   * conversion. Defaults to the account currency code if unspecified.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Language used for any localized text, such as some dimension value display
   * labels. The language tag defined in the IETF BCP47. Defaults to 'en-US' if
   * unspecified.
   *
   * @var string
   */
  public $languageCode;

  /**
   * Currency code of the earning related metrics, which is the 3-letter code
   * defined in ISO 4217. The daily average rate is used for the currency
   * conversion. Defaults to the account currency code if unspecified.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Language used for any localized text, such as some dimension value display
   * labels. The language tag defined in the IETF BCP47. Defaults to 'en-US' if
   * unspecified.
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
class_alias(LocalizationSettings::class, 'Google_Service_AdMob_LocalizationSettings');
