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

class DocumentTranslation extends \Google\Collection
{
  protected $collection_key = 'byteStreamOutputs';
  /**
   * The array of translated documents. It is expected to be size 1 for now. We
   * may produce multiple translated documents in the future for other type of
   * file formats.
   *
   * @var string[]
   */
  public $byteStreamOutputs;
  /**
   * The detected language for the input document. If the user did not provide
   * the source language for the input document, this field will have the
   * language code automatically detected. If the source language was passed,
   * auto-detection of the language does not occur and this field is empty.
   *
   * @var string
   */
  public $detectedLanguageCode;
  /**
   * The translated document's mime type.
   *
   * @var string
   */
  public $mimeType;

  /**
   * The array of translated documents. It is expected to be size 1 for now. We
   * may produce multiple translated documents in the future for other type of
   * file formats.
   *
   * @param string[] $byteStreamOutputs
   */
  public function setByteStreamOutputs($byteStreamOutputs)
  {
    $this->byteStreamOutputs = $byteStreamOutputs;
  }
  /**
   * @return string[]
   */
  public function getByteStreamOutputs()
  {
    return $this->byteStreamOutputs;
  }
  /**
   * The detected language for the input document. If the user did not provide
   * the source language for the input document, this field will have the
   * language code automatically detected. If the source language was passed,
   * auto-detection of the language does not occur and this field is empty.
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
   * The translated document's mime type.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DocumentTranslation::class, 'Google_Service_Translate_DocumentTranslation');
