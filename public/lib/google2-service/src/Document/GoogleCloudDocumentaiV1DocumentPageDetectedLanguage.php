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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentPageDetectedLanguage extends \Google\Model
{
  /**
   * Confidence of detected language. Range `[0, 1]`.
   *
   * @var float
   */
  public $confidence;
  /**
   * The [BCP-47 language
   * code](https://www.unicode.org/reports/tr35/#Unicode_locale_identifier),
   * such as `en-US` or `sr-Latn`.
   *
   * @var string
   */
  public $languageCode;

  /**
   * Confidence of detected language. Range `[0, 1]`.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * The [BCP-47 language
   * code](https://www.unicode.org/reports/tr35/#Unicode_locale_identifier),
   * such as `en-US` or `sr-Latn`.
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
class_alias(GoogleCloudDocumentaiV1DocumentPageDetectedLanguage::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentPageDetectedLanguage');
