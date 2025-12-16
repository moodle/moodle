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

class GoogleCloudDiscoveryengineV1ChunkDocumentMetadata extends \Google\Model
{
  /**
   * The mime type of the document. https://www.iana.org/assignments/media-
   * types/media-types.xhtml.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Data representation. The structured JSON data for the document. It should
   * conform to the registered Schema or an `INVALID_ARGUMENT` error is thrown.
   *
   * @var array[]
   */
  public $structData;
  /**
   * Title of the document.
   *
   * @var string
   */
  public $title;
  /**
   * Uri of the document.
   *
   * @var string
   */
  public $uri;

  /**
   * The mime type of the document. https://www.iana.org/assignments/media-
   * types/media-types.xhtml.
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
  /**
   * Data representation. The structured JSON data for the document. It should
   * conform to the registered Schema or an `INVALID_ARGUMENT` error is thrown.
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
   * Title of the document.
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
   * Uri of the document.
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
class_alias(GoogleCloudDiscoveryengineV1ChunkDocumentMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ChunkDocumentMetadata');
