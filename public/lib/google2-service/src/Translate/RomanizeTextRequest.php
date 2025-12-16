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

class RomanizeTextRequest extends \Google\Collection
{
  protected $collection_key = 'contents';
  /**
   * Required. The content of the input in string format.
   *
   * @var string[]
   */
  public $contents;
  /**
   * Optional. The ISO-639 language code of the input text if known, for
   * example, "hi" or "zh". If the source language isn't specified, the API
   * attempts to identify the source language automatically and returns the
   * source language for each content in the response.
   *
   * @var string
   */
  public $sourceLanguageCode;

  /**
   * Required. The content of the input in string format.
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
   * Optional. The ISO-639 language code of the input text if known, for
   * example, "hi" or "zh". If the source language isn't specified, the API
   * attempts to identify the source language automatically and returns the
   * source language for each content in the response.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RomanizeTextRequest::class, 'Google_Service_Translate_RomanizeTextRequest');
