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

namespace Google\Service\Meet;

class DocsDestination extends \Google\Model
{
  /**
   * Output only. The document ID for the underlying Google Docs transcript
   * file. For example, "1kuceFZohVoCh6FulBHxwy6I15Ogpc4hP". Use the
   * `documents.get` method of the Google Docs API
   * (https://developers.google.com/docs/api/reference/rest/v1/documents/get) to
   * fetch the content.
   *
   * @var string
   */
  public $document;
  /**
   * Output only. URI for the Google Docs transcript file. Use
   * `https://docs.google.com/document/d/{$DocumentId}/view` to browse the
   * transcript in the browser.
   *
   * @var string
   */
  public $exportUri;

  /**
   * Output only. The document ID for the underlying Google Docs transcript
   * file. For example, "1kuceFZohVoCh6FulBHxwy6I15Ogpc4hP". Use the
   * `documents.get` method of the Google Docs API
   * (https://developers.google.com/docs/api/reference/rest/v1/documents/get) to
   * fetch the content.
   *
   * @param string $document
   */
  public function setDocument($document)
  {
    $this->document = $document;
  }
  /**
   * @return string
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Output only. URI for the Google Docs transcript file. Use
   * `https://docs.google.com/document/d/{$DocumentId}/view` to browse the
   * transcript in the browser.
   *
   * @param string $exportUri
   */
  public function setExportUri($exportUri)
  {
    $this->exportUri = $exportUri;
  }
  /**
   * @return string
   */
  public function getExportUri()
  {
    return $this->exportUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DocsDestination::class, 'Google_Service_Meet_DocsDestination');
