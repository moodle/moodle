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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSearchSnippet extends \Google\Model
{
  /**
   * Title of the enclosing document.
   *
   * @var string
   */
  public $documentTitle;
  /**
   * Uri for the document. Present if specified for the document.
   *
   * @var string
   */
  public $documentUri;
  /**
   * Metadata associated with the document.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * Text included in the prompt.
   *
   * @var string
   */
  public $text;

  /**
   * Title of the enclosing document.
   *
   * @param string $documentTitle
   */
  public function setDocumentTitle($documentTitle)
  {
    $this->documentTitle = $documentTitle;
  }
  /**
   * @return string
   */
  public function getDocumentTitle()
  {
    return $this->documentTitle;
  }
  /**
   * Uri for the document. Present if specified for the document.
   *
   * @param string $documentUri
   */
  public function setDocumentUri($documentUri)
  {
    $this->documentUri = $documentUri;
  }
  /**
   * @return string
   */
  public function getDocumentUri()
  {
    return $this->documentUri;
  }
  /**
   * Metadata associated with the document.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Text included in the prompt.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSearchSnippet::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSearchSnippet');
