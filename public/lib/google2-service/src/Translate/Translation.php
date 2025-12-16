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

class Translation extends \Google\Model
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
  protected $glossaryConfigType = TranslateTextGlossaryConfig::class;
  protected $glossaryConfigDataType = '';
  /**
   * Only present when `model` is present in the request. `model` here is
   * normalized to have project number. For example: If the `model` requested in
   * TranslationTextRequest is `projects/{project-id}/locations/{location-
   * id}/models/general/nmt` then `model` here would be normalized to
   * `projects/{project-number}/locations/{location-id}/models/general/nmt`.
   *
   * @var string
   */
  public $model;
  /**
   * Text translated into the target language. If an error occurs during
   * translation, this field might be excluded from the response.
   *
   * @var string
   */
  public $translatedText;

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
   * The `glossary_config` used for this translation.
   *
   * @param TranslateTextGlossaryConfig $glossaryConfig
   */
  public function setGlossaryConfig(TranslateTextGlossaryConfig $glossaryConfig)
  {
    $this->glossaryConfig = $glossaryConfig;
  }
  /**
   * @return TranslateTextGlossaryConfig
   */
  public function getGlossaryConfig()
  {
    return $this->glossaryConfig;
  }
  /**
   * Only present when `model` is present in the request. `model` here is
   * normalized to have project number. For example: If the `model` requested in
   * TranslationTextRequest is `projects/{project-id}/locations/{location-
   * id}/models/general/nmt` then `model` here would be normalized to
   * `projects/{project-number}/locations/{location-id}/models/general/nmt`.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Text translated into the target language. If an error occurs during
   * translation, this field might be excluded from the response.
   *
   * @param string $translatedText
   */
  public function setTranslatedText($translatedText)
  {
    $this->translatedText = $translatedText;
  }
  /**
   * @return string
   */
  public function getTranslatedText()
  {
    return $this->translatedText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Translation::class, 'Google_Service_Translate_Translation');
