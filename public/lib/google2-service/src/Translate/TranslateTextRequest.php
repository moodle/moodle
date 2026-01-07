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

class TranslateTextRequest extends \Google\Collection
{
  protected $collection_key = 'contents';
  /**
   * Required. The content of the input in string format. We recommend the total
   * content be less than 30,000 codepoints. The max length of this field is
   * 1024. Use BatchTranslateText for larger text.
   *
   * @var string[]
   */
  public $contents;
  protected $glossaryConfigType = TranslateTextGlossaryConfig::class;
  protected $glossaryConfigDataType = '';
  /**
   * Optional. The labels with user-defined metadata for the request. Label keys
   * and values can be no longer than 63 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. Label values are optional. Label keys
   * must start with a letter. See
   * https://cloud.google.com/translate/docs/advanced/labels for more
   * information.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The format of the source text, for example, "text/html",
   * "text/plain". If left blank, the MIME type defaults to "text/html".
   *
   * @var string
   */
  public $mimeType;
  /**
   * Optional. The `model` type requested for this translation. The format
   * depends on model type: - AutoML Translation models: `projects/{project-
   * number-or-id}/locations/{location-id}/models/{model-id}` - General (built-
   * in) models: `projects/{project-number-or-id}/locations/{location-
   * id}/models/general/nmt`, - Translation LLM models: `projects/{project-
   * number-or-id}/locations/{location-id}/models/general/translation-llm`, For
   * global (non-regionalized) requests, use `location-id` `global`. For
   * example, `projects/{project-number-or-
   * id}/locations/global/models/general/nmt`. If not provided, the default
   * Google model (NMT) will be used
   *
   * @var string
   */
  public $model;
  /**
   * Optional. The ISO-639 language code of the input text if known, for
   * example, "en-US" or "sr-Latn". Supported language codes are listed in
   * Language Support. If the source language isn't specified, the API attempts
   * to identify the source language automatically and returns the source
   * language within the response.
   *
   * @var string
   */
  public $sourceLanguageCode;
  /**
   * Required. The ISO-639 language code to use for translation of the input
   * text, set to one of the language codes listed in Language Support.
   *
   * @var string
   */
  public $targetLanguageCode;
  protected $transliterationConfigType = TransliterationConfig::class;
  protected $transliterationConfigDataType = '';

  /**
   * Required. The content of the input in string format. We recommend the total
   * content be less than 30,000 codepoints. The max length of this field is
   * 1024. Use BatchTranslateText for larger text.
   *
   * @param string[] $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return string[]
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Optional. Glossary to be applied. The glossary must be within the same
   * region (have the same location-id) as the model, otherwise an
   * INVALID_ARGUMENT (400) error is returned.
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
   * Optional. The labels with user-defined metadata for the request. Label keys
   * and values can be no longer than 63 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. Label values are optional. Label keys
   * must start with a letter. See
   * https://cloud.google.com/translate/docs/advanced/labels for more
   * information.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. The format of the source text, for example, "text/html",
   * "text/plain". If left blank, the MIME type defaults to "text/html".
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Optional. The `model` type requested for this translation. The format
   * depends on model type: - AutoML Translation models: `projects/{project-
   * number-or-id}/locations/{location-id}/models/{model-id}` - General (built-
   * in) models: `projects/{project-number-or-id}/locations/{location-
   * id}/models/general/nmt`, - Translation LLM models: `projects/{project-
   * number-or-id}/locations/{location-id}/models/general/translation-llm`, For
   * global (non-regionalized) requests, use `location-id` `global`. For
   * example, `projects/{project-number-or-
   * id}/locations/global/models/general/nmt`. If not provided, the default
   * Google model (NMT) will be used
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
   * Optional. The ISO-639 language code of the input text if known, for
   * example, "en-US" or "sr-Latn". Supported language codes are listed in
   * Language Support. If the source language isn't specified, the API attempts
   * to identify the source language automatically and returns the source
   * language within the response.
   *
   * @param string $sourceLanguageCode
   */
  public function setSourceLanguageCode($sourceLanguageCode)
  {
    $this->sourceLanguageCode = $sourceLanguageCode;
  }
  /**
   * @return string
   */
  public function getSourceLanguageCode()
  {
    return $this->sourceLanguageCode;
  }
  /**
   * Required. The ISO-639 language code to use for translation of the input
   * text, set to one of the language codes listed in Language Support.
   *
   * @param string $targetLanguageCode
   */
  public function setTargetLanguageCode($targetLanguageCode)
  {
    $this->targetLanguageCode = $targetLanguageCode;
  }
  /**
   * @return string
   */
  public function getTargetLanguageCode()
  {
    return $this->targetLanguageCode;
  }
  /**
   * Optional. Transliteration to be applied.
   *
   * @param TransliterationConfig $transliterationConfig
   */
  public function setTransliterationConfig(TransliterationConfig $transliterationConfig)
  {
    $this->transliterationConfig = $transliterationConfig;
  }
  /**
   * @return TransliterationConfig
   */
  public function getTransliterationConfig()
  {
    return $this->transliterationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TranslateTextRequest::class, 'Google_Service_Translate_TranslateTextRequest');
