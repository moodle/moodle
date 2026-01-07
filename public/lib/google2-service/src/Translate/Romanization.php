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

class Romanization extends \Google\Model
{
  /**
   * The ISO-639 language code of source text in the initial request, detected
   * automatically, if no source language was passed within the initial request.
   * If the source language was passed, auto-detection of the language does not
   * occur and this field is empty.
   *
   * @var string
   */
  public $detectedLanguageCode;
  /**
   * Romanized text. If an error occurs during romanization, this field might be
   * excluded from the response.
   *
   * @var string
   */
  public $romanizedText;

  /**
   * The ISO-639 language code of source text in the initial request, detected
   * automatically, if no source language was passed within the initial request.
   * If the source language was passed, auto-detection of the language does not
   * occur and this field is empty.
   *
   * @param string $detectedLanguageCode
   */
  public function setDetectedLanguageCode($detectedLanguageCode)
  {
    $this->detectedLanguageCode = $detectedLanguageCode;
  }
  /**
   * @return string
   */
  public function getDetectedLanguageCode()
  {
    return $this->detectedLanguageCode;
  }
  /**
   * Romanized text. If an error occurs during romanization, this field might be
   * excluded from the response.
   *
   * @param string $romanizedText
   */
  public function setRomanizedText($romanizedText)
  {
    $this->romanizedText = $romanizedText;
  }
  /**
   * @return string
   */
  public function getRomanizedText()
  {
    return $this->romanizedText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Romanization::class, 'Google_Service_Translate_Romanization');
