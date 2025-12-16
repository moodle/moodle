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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1OcrConfigHints extends \Google\Collection
{
  protected $collection_key = 'languageHints';
  /**
   * List of BCP-47 language codes to use for OCR. In most cases, not specifying
   * it yields the best results since it enables automatic language detection.
   * For languages based on the Latin alphabet, setting hints is not needed. In
   * rare cases, when the language of the text in the image is known, setting a
   * hint will help get better results (although it will be a significant
   * hindrance if the hint is wrong).
   *
   * @var string[]
   */
  public $languageHints;

  /**
   * List of BCP-47 language codes to use for OCR. In most cases, not specifying
   * it yields the best results since it enables automatic language detection.
   * For languages based on the Latin alphabet, setting hints is not needed. In
   * rare cases, when the language of the text in the image is known, setting a
   * hint will help get better results (although it will be a significant
   * hindrance if the hint is wrong).
   *
   * @param string[] $languageHints
   */
  public function setLanguageHints($languageHints)
  {
    $this->languageHints = $languageHints;
  }
  /**
   * @return string[]
   */
  public function getLanguageHints()
  {
    return $this->languageHints;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1OcrConfigHints::class, 'Google_Service_Document_GoogleCloudDocumentaiV1OcrConfigHints');
