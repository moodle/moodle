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

class BatchTranslateTextRequest extends \Google\Collection
{
  protected $collection_key = 'targetLanguageCodes';
  protected $glossariesType = TranslateTextGlossaryConfig::class;
  protected $glossariesDataType = 'map';
  protected $inputConfigsType = InputConfig::class;
  protected $inputConfigsDataType = 'array';
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
   * Optional. The models to use for translation. Map's key is target language
   * code. Map's value is model name. Value can be a built-in general model, or
   * an AutoML Translation model. The value format depends on model type: -
   * AutoML Translation models: `projects/{project-number-or-
   * id}/locations/{location-id}/models/{model-id}` - General (built-in) models:
   * `projects/{project-number-or-id}/locations/{location-
   * id}/models/general/nmt`, If the map is empty or a specific model is not
   * requested for a language pair, then default google model (nmt) is used.
   *
   * @var string[]
   */
  public $models;
  protected $outputConfigType = OutputConfig::class;
  protected $outputConfigDataType = '';
  /**
   * Required. Source language code.
   *
   * @var string
   */
  public $sourceLanguageCode;
  /**
   * Required. Specify up to 10 language codes here.
   *
   * @var string[]
   */
  public $targetLanguageCodes;

  /**
   * Optional. Glossaries to be applied for translation. It's keyed by target
   * language code.
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
   * <= 100. The total content size should be <= 100M Unicode codepoints. The
   * files must use UTF-8 encoding.
   *
   * @param InputConfig[] $inputConfigs
   */
  public function setInputConfigs($inputConfigs)
  {
    $this->inputConfigs = $inputConfigs;
  }
  /**
   * @return InputConfig[]
   */
  public function getInputConfigs()
  {
    return $this->inputConfigs;
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
   * Optional. The models to use for translation. Map's key is target language
   * code. Map's value is model name. Value can be a built-in general model, or
   * an AutoML Translation model. The value format depends on model type: -
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
   * @param OutputConfig $outputConfig
   */
  public function setOutputConfig(OutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return OutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * Required. Source language code.
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
   * Required. Specify up to 10 language codes here.
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
class_alias(BatchTranslateTextRequest::class, 'Google_Service_Translate_BatchTranslateTextRequest');
