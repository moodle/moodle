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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1AnswerReferenceChunkInfoDocumentMetadata extends \Google\Model
{
  /**
   * Document resource name.
   *
   * @var string
   */
  public $document;
  /**
   * Page identifier.
   *
   * @var string
   */
  public $pageIdentifier;
  /**
   * The structured JSON metadata for the document. It is populated from the
   * struct data from the Chunk in search result.
   *
   * @var array[]
   */
  public $structData;
  /**
   * Title.
   *
   * @var string
   */
  public $title;
  /**
   * URI for the document.
   *
   * @var string
   */
  public $uri;

  /**
   * Document resource name.
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
   * Page identifier.
   *
   * @param string $pageIdentifier
   */
  public function setPageIdentifier($pageIdentifier)
  {
    $this->pageIdentifier = $pageIdentifier;
  }
  /**
   * @return string
   */
  public function getPageIdentifier()
  {
    return $this->pageIdentifier;
  }
  /**
   * The structured JSON metadata for the document. It is populated from the
   * struct data from the Chunk in search result.
   *
   * @param array[] $structData
   */
  public function setStructData($structData)
  {
    $this->structData = $structData;
  }
  /**
   * @return array[]
   */
  public function getStructData()
  {
    return $this->structData;
  }
  /**
   * Title.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * URI for the document.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerReferenceChunkInfoDocumentMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerReferenceChunkInfoDocumentMetadata');
