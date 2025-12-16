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

class GoogleCloudDocumentaiV1RawDocument extends \Google\Model
{
  /**
   * Inline document content.
   *
   * @var string
   */
  public $content;
  /**
   * The display name of the document, it supports all Unicode characters except
   * the following: `*`, `?`, `[`, `]`, `%`, `{`, `}`,`'`, `\"`, `,` `~`, `=`
   * and `:` are reserved. If not specified, a default ID is generated.
   *
   * @var string
   */
  public $displayName;
  /**
   * An IANA MIME type (RFC6838) indicating the nature and format of the
   * content.
   *
   * @var string
   */
  public $mimeType;

  /**
   * Inline document content.
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
   * The display name of the document, it supports all Unicode characters except
   * the following: `*`, `?`, `[`, `]`, `%`, `{`, `}`,`'`, `\"`, `,` `~`, `=`
   * and `:` are reserved. If not specified, a default ID is generated.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * An IANA MIME type (RFC6838) indicating the nature and format of the
   * content.
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
class_alias(GoogleCloudDocumentaiV1RawDocument::class, 'Google_Service_Document_GoogleCloudDocumentaiV1RawDocument');
