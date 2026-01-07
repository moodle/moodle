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

class BatchTranslateDocumentRequest extends \Google\Collection
{
  protected $collection_key = 'targetLanguageCodes';
  /**
   * Optional. This flag is to support user customized attribution. If not
   * provided, the default is `Machine Translated by Google`. Customized
   * attribution should follow rules in
   * https://cloud.google.com/translate/attribution#attribution_and_logos
   *
   * @var string
   */
  public $customizedAttribution;
  /**
   * Optional. If true, enable auto rotation correction in DVS.
   *
   * @var bool
   */
  public $enableRotationCorrection;
  /**
   * Optional. If true, use the text removal server to remove the shadow text on
   * background image for native pdf translation. Shadow removal feature can
   * only be enabled when is_translate_native_pdf_only: false &&
   * pdf_native_only: false
   *
   * @var bool
   */
  public $enableShadowRemovalNativePdf;
  /**
   * Optional. The file format conversion map that is applied to all input
   * files. The map key is the original mime_type. The map value is the target
   * mime_type of translated documents. Supported file format conversion
   * includes: - `application/pdf` to `application/vnd.openxmlformats-
   * officedocument.wordprocessingml.document` If nothing specified, output
   * files will be in the same format as the original file.
   *
   * @var string[]
   */
  public $formatConversions;
  protected $glossariesType = TranslateTextGlossaryConfig::class;
  protected $glossariesDataType = 'map';
  protected $inputConfigsType = BatchDocumentInputConfig::class;
  protected $inputConfigsDataType = 'array';
  /**
   * Optional. The models to use for translation. Map's key is target language
   * code. Map's value is the model name. Value can be a built-in general model,
   * or an AutoML Translation model. The value format depends on model type: -
   * AutoML Translation models: `projects/{project-number-or-
   * id}/locations/{location-id}/models/{model-id}` - General (built-in) models:
   * `projects/{project-number-or-id}/locations/{location-
   * id}/models/general/nmt`, If the map is empty or a specific model is not
   * requested for a language pair, then default google model (nmt) is used.
   *
   * @var string[]
   */
  public $models;
  protected $outputConfigType = BatchDocumentOutputConfig::class;
  protected $outputConfigDataType = '';
  /**
   * Required. The ISO-639 language code of the input document if known, for
   * example, "en-US" or "sr-Latn". Supported language codes are listed in
   * [Language Support](https://cloud.google.com/translate/docs/languages).
   *
   * @var string
   */
  public $sourceLanguageCode;
  /**
   * Required. The ISO-639 language code to use for translation of the input
   * document. Specify up to 10 language codes here.
   *
   * @var string[]
   */
  public $targetLanguageCodes;

  /**
   * Optional. This flag is to support user customized attribution. If not
   * provided, the default is `Machine Translated by Google`. Customized
   * attribution should follow rules in
   * https://cloud.google.com/translate/attribution#attribution_and_logos
   *
   * @param string $customizedAttribution
   */
  public function setCustomizedAttribution($customizedAttribution)
  {
    $this->customizedAttribution = $customizedAttribution;
  }
  /**
   * @return string
   */
  public function getCustomizedAttribution()
  {
    return $this->customizedAttribution;
  }
  /**
   * Optional. If true, enable auto rotation correction in DVS.
   *
   * @param bool $enableRotationCorrection
   */
  public function setEnableRotationCorrection($enableRotationCorrection)
  {
    $this->enableRotationCorrection = $enableRotationCorrection;
  }
  /**
   * @return bool
   */
  public function getEnableRotationCorrection()
  {
    return $this->enableRotationCorrection;
  }
  /**
   * Optional. If true, use the text removal server to remove the shadow text on
   * background image for native pdf translation. Shadow removal feature can
   * only be enabled when is_translate_native_pdf_only: false &&
   * pdf_native_only: false
   *
   * @param bool $enableShadowRemovalNativePdf
   */
  public function setEnableShadowRemovalNativePdf($enableShadowRemovalNativePdf)
  {
    $this->enableShadowRemovalNativePdf = $enableShadowRemovalNativePdf;
  }
  /**
   * @return bool
   */
  public function getEnableShadowRemovalNativePdf()
  {
    return $this->enableShadowRemovalNativePdf;
  }
  /**
   * Optional. The file format conversion map that is applied to all input
   * files. The map key is the original mime_type. The map value is the target
   * mime_type of translated documents. Supported file format conversion
   * includes: - `application/pdf` to `application/vnd.openxmlformats-
   * officedocument.wordprocessingml.document` If nothing specified, output
   * files will be in the same format as the original file.
   *
   * @param string[] $formatConversions
   */
  public function setFormatConversions($formatConversions)
  {
    $this->formatConversions = $formatConversions;
  }
  /**
   * @return string[]
   */
  public function getFormatConversions()
  {
    return $this->formatConversions;
  }
  /**
   * Optional. Glossaries to be applied. It's keyed by target language code.
   *
   * @param TranslateTextGlossaryConfig[] $glossaries
   */
  public function setGlossaries($glossaries)
  {
    $this->glossaries = $glossaries;
  }
  /**
   * @return TranslateTextGlossaryConfig[]
   */
  public function getGlossaries()
  {
    return $this->glossaries;
  }
  /**
   * Required. Input configurations. The total number of files matched should be
   * <= 100. The total content size to translate should be <= 100M Unicode
   * codepoints. The files must use UTF-8 encoding.
   *
   * @param BatchDocumentInputConfig[] $inputConfigs
   */
  public function setInputConfigs($inputConfigs)
  {
    $this->inputConfigs = $inputConfigs;
  }
  /**
   * @return BatchDocumentInputConfig[]
   */
  public function getInputConfigs()
  {
    return $this->inputConfigs;
  }
  /**
   * Optional. The models to use for translation. Map's key is target language
   * code. Map's value is the model name. Value can be a built-in general model,
   * or an AutoML Translation model. The value format depends on model type: -
   * AutoML Translation models: `projects/{project-number-or-
   * id}/locations/{location-id}/models/{model-id}` - General (built-in) models:
   * `projects/{project-number-or-id}/locations/{location-
   * id}/models/general/nmt`, If the map is empty or a specific model is not
   * requested for a language pair, then default google model (nmt) is used.
   *
   * @param string[] $models
   */
  public function setModels($models)
  {
    $this->models = $models;
  }
  /**
   * @return string[]
   */
  public function getModels()
  {
    return $this->models;
  }
  /**
   * Required. Output configuration. If 2 input configs match to the same file
   * (that is, same input path), we don't generate output for duplicate inputs.
   *
   * @param BatchDocumentOutputConfig $outputConfig
   */
  public function setOutputConfig(BatchDocumentOutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return BatchDocumentOutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * Required. The ISO-639 language code of the input document if known, for
   * example, "en-US" or "sr-Latn". Supported language codes are listed in
   * [Language Support](https://cloud.google.com/translate/docs/languages).
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
   * document. Specify up to 10 language codes here.
   *
   * @param string[] $targetLanguageCodes
   */
  public function setTargetLanguageCodes($targetLanguageCodes)
  {
    $this->targetLanguageCodes = $targetLanguageCodes;
  }
  /**
   * @return string[]
   */
  public function getTargetLanguageCodes()
  {
    return $this->targetLanguageCodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchTranslateDocumentRequest::class, 'Google_Service_Translate_BatchTranslateDocumentRequest');
