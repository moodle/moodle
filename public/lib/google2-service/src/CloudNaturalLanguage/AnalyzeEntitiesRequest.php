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

class AnalyzeEntitiesRequest extends \Google\Model
{
  /**
   * If `EncodingType` is not specified, encoding-dependent information (such as
   * `begin_offset`) will be set at `-1`.
   */
  public const ENCODING_TYPE_NONE = 'NONE';
  /**
   * Encoding-dependent information (such as `begin_offset`) is calculated based
   * on the UTF-8 encoding of the input. C++ and Go are examples of languages
   * that use this encoding natively.
   */
  public const ENCODING_TYPE_UTF8 = 'UTF8';
  /**
   * Encoding-dependent information (such as `begin_offset`) is calculated based
   * on the UTF-16 encoding of the input. Java and JavaScript are examples of
   * languages that use this encoding natively.
   */
  public const ENCODING_TYPE_UTF16 = 'UTF16';
  /**
   * Encoding-dependent information (such as `begin_offset`) is calculated based
   * on the UTF-32 encoding of the input. Python is an example of a language
   * that uses this encoding natively.
   */
  public const ENCODING_TYPE_UTF32 = 'UTF32';
  protected $documentType = Document::class;
  protected $documentDataType = '';
  /**
   * The encoding type used by the API to calculate offsets.
   *
   * @var string
   */
  public $encodingType;

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
   * The encoding type used by the API to calculate offsets.
   *
   * Accepted values: NONE, UTF8, UTF16, UTF32
   *
   * @param self::ENCODING_TYPE_* $encodingType
   */
  public function setEncodingType($encodingType)
  {
    $this->encodingType = $encodingType;
  }
  /**
   * @return self::ENCODING_TYPE_*
   */
  public function getEncodingType()
  {
    return $this->encodingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzeEntitiesRequest::class, 'Google_Service_CloudNaturalLanguage_AnalyzeEntitiesRequest');
