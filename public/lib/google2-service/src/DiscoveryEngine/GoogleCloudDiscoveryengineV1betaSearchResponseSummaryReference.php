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

class GoogleCloudDiscoveryengineV1betaSearchResponseSummaryReference extends \Google\Collection
{
  protected $collection_key = 'chunkContents';
  protected $chunkContentsType = GoogleCloudDiscoveryengineV1betaSearchResponseSummaryReferenceChunkContent::class;
  protected $chunkContentsDataType = 'array';
  /**
   * @var string
   */
  public $document;
  /**
   * @var string
   */
  public $title;
  /**
   * @var string
   */
  public $uri;

  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseSummaryReferenceChunkContent[]
   */
  public function setChunkContents($chunkContents)
  {
    $this->chunkContents = $chunkContents;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseSummaryReferenceChunkContent[]
   */
  public function getChunkContents()
  {
    return $this->chunkContents;
  }
  /**
   * @param string
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
   * @param string
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
   * @param string
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
class_alias(GoogleCloudDiscoveryengineV1betaSearchResponseSummaryReference::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchResponseSummaryReference');
