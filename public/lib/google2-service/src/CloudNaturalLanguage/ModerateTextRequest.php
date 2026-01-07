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

namespace Google\Service\CloudNaturalLanguage;

class ModerateTextRequest extends \Google\Model
{
  /**
   * The default model version.
   */
  public const MODEL_VERSION_MODEL_VERSION_UNSPECIFIED = 'MODEL_VERSION_UNSPECIFIED';
  /**
   * Use the v1 model, this model is used by default when not provided. The v1
   * model only returns probability (confidence) score for each category.
   */
  public const MODEL_VERSION_MODEL_VERSION_1 = 'MODEL_VERSION_1';
  /**
   * Use the v2 model. The v2 model only returns probability (confidence) score
   * for each category, and returns severity score for a subset of the
   * categories.
   */
  public const MODEL_VERSION_MODEL_VERSION_2 = 'MODEL_VERSION_2';
  protected $documentType = Document::class;
  protected $documentDataType = '';
  /**
   * Optional. The model version to use for ModerateText.
   *
   * @var string
   */
  public $modelVersion;

  /**
   * Required. Input document.
   *
   * @param Document $document
   */
  public function setDocument(Document $document)
  {
    $this->document = $document;
  }
  /**
   * @return Document
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Optional. The model version to use for ModerateText.
   *
   * Accepted values: MODEL_VERSION_UNSPECIFIED, MODEL_VERSION_1,
   * MODEL_VERSION_2
   *
   * @param self::MODEL_VERSION_* $modelVersion
   */
  public function setModelVersion($modelVersion)
  {
    $this->modelVersion = $modelVersion;
  }
  /**
   * @return self::MODEL_VERSION_*
   */
  public function getModelVersion()
  {
    return $this->modelVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModerateTextRequest::class, 'Google_Service_CloudNaturalLanguage_ModerateTextRequest');
