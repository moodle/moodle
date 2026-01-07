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

namespace Google\Service\ChecksService;

class GoogleChecksAisafetyV1alphaTextInput extends \Google\Model
{
  /**
   * Actual piece of text to be classified.
   *
   * @var string
   */
  public $content;
  /**
   * Optional. Language of the text in ISO 639-1 format. If the language is
   * invalid or not specified, the system will try to detect it.
   *
   * @var string
   */
  public $languageCode;

  /**
   * Actual piece of text to be classified.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Optional. Language of the text in ISO 639-1 format. If the language is
   * invalid or not specified, the system will try to detect it.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksAisafetyV1alphaTextInput::class, 'Google_Service_ChecksService_GoogleChecksAisafetyV1alphaTextInput');
