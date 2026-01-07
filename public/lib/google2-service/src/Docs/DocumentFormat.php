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

namespace Google\Service\Docs;

class DocumentFormat extends \Google\Model
{
  /**
   * The document mode is unspecified.
   */
  public const DOCUMENT_MODE_DOCUMENT_MODE_UNSPECIFIED = 'DOCUMENT_MODE_UNSPECIFIED';
  /**
   * The document has pages.
   */
  public const DOCUMENT_MODE_PAGES = 'PAGES';
  /**
   * The document is pageless.
   */
  public const DOCUMENT_MODE_PAGELESS = 'PAGELESS';
  /**
   * Whether the document has pages or is pageless.
   *
   * @var string
   */
  public $documentMode;

  /**
   * Whether the document has pages or is pageless.
   *
   * Accepted values: DOCUMENT_MODE_UNSPECIFIED, PAGES, PAGELESS
   *
   * @param self::DOCUMENT_MODE_* $documentMode
   */
  public function setDocumentMode($documentMode)
  {
    $this->documentMode = $documentMode;
  }
  /**
   * @return self::DOCUMENT_MODE_*
   */
  public function getDocumentMode()
  {
    return $this->documentMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DocumentFormat::class, 'Google_Service_Docs_DocumentFormat');
