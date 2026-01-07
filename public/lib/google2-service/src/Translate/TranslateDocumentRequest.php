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

class TranslateDocumentRequest extends \Google\Model
{
  /**
   * Optional. This flag is to support user customized attribution. If not
   * provided, the default is `Machine Translated by Google`. Customized
   * attribution should follow rules in
   * https://cloud.google.com/translate/attribution#attribution_and_logos
   *
   * @var string
   */
  public $customizedAttribution;
  protected $documentInputConfigType = DocumentInputConfig::class;
  protected $documentInputConfigDataType = '';
  protected $documentOutputConfigType = DocumentOutputConfig::class;
  protected $documentOutputConfigDataType = '';
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
  protected $glossaryConfigType = TranslateTextGlossaryConfig::class;
  protected $glossaryConfigDataType = '';
  /**
   * Optional. is_translate_native_pdf_only field for external customers. If
   * true, the page limit of online native pdf translation is 300 and only
   * native pdf pages will be translated.
   *
   * @var bool
   */
  public $isTranslateNativePdfOnly;
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
   * Optional. The `model` type requested for this translation. The format
   * depends on model type: - AutoML Translation models: `projects/{project-
   * number-or-id}/locations/{location-id}/models/{model-id}` - General (built-
   * in) models: `projects/{project-number-or-id}/locations/{location-
   * id}/models/general/nmt`, If not provided, the default Google model (NMT)
   * will be used for translation.
   *
   * @var string
   */
  public $model;
  /**
   * Optional. The ISO-639 language code of the input document if known, for
   * example, "en-US" or "sr-Latn". Supported language codes are listed in
   * Language Support. If the source language isn't specified, the API attempts
   * to identify the source language automatically and returns the source
   * language within the response. Source language must be specified if the
   * request contains a glossary or a custom model.
   *
   * @var string
   */
  public $sourceLanguageCode;
  /**
   * Required. The ISO-639 language code to use for translation of the input
   * document, set to one of the language codes listed in Language Support.
   *
   * @var string
   */
  public $targetLanguageCode;

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
   * Required. Input configurations.
   *
   * @param DocumentInputConfig $documentInputConfig
   */
  public function setDocumentInputConfig(DocumentInputConfig $documentInputConfig)
  {
    $this->documentInputConfig = $documentInputConfig;
  }
  /**
   * @return DocumentInputConfig
   */
  public function getDocumentInputConfig()
  {
    return $this->documentInputConfig;
  }
  /**
   * Optional. Output configurations. Defines if the output file should be
   * stored within Cloud Storage as well as the desired output format. If not
   * provided the translated file will only be returned through a byte-stream
   * and its output mime type will be the same as the input file's mime type.
   *
   * @param DocumentOutputConfig $documentOutputConfig
   */
  public function setDocumentOutputConfig(DocumentOutputConfig $documentOutputConfig)
  {
    $this->documentOutputConfig = $documentOutputConfig;
  }
  /**
   * @return DocumentOutputConfig
   */
  public function getDocumentOutputConfig()
  {
    return $this->documentOutputConfig;
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
   * Optional. is_translate_native_pdf_only field for external customers. If
   * true, the page limit of online native pdf translation is 300 and only
   * native pdf pages will be translated.
   *
   * @param bool $isTranslateNativePdfOnly
   */
  public function setIsTranslateNativePdfOnly($isTranslateNativePdfOnly)
  {
    $this->isTranslateNativePdfOnly = $isTranslateNativePdfOnly;
  }
  /**
   * @return bool
   */
  public function getIsTranslateNativePdfOnly()
  {
    return $this->isTranslateNativePdfOnly;
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
   * Optional. The `model` type requested for this translation. The format
   * depends on model type: - AutoML Translation models: `projects/{project-
   * number-or-id}/locations/{location-id}/models/{model-id}` - General (built-
   * in) models: `projects/{project-number-or-id}/locations/{location-
   * id}/models/general/nmt`, If not provided, the default Google model (NMT)
   * will be used for translation.
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
   * Optional. The ISO-639 language code of the input document if known, for
   * example, "en-US" or "sr-Latn". Supported language codes are listed in
   * Language Support. If the source language isn't specified, the API attempts
   * to identify the source language automatically and returns the source
   * language within the response. Source language must be specified if the
   * request contains a glossary or a custom model.
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
   * document, set to one of the language codes listed in Language Support.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TranslateDocumentRequest::class, 'Google_Service_Translate_TranslateDocumentRequest');
