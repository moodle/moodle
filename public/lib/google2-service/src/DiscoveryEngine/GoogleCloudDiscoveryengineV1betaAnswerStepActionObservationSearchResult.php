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

class GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResult extends \Google\Collection
{
  protected $collection_key = 'snippetInfo';
  protected $chunkInfoType = GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResultChunkInfo::class;
  protected $chunkInfoDataType = 'array';
  /**
   * @var string
   */
  public $document;
  protected $snippetInfoType = GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResultSnippetInfo::class;
  protected $snippetInfoDataType = 'array';
  /**
   * @var string
   */
  public $title;
  /**
   * @var string
   */
  public $uri;

  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResultChunkInfo[]
   */
  public function setChunkInfo($chunkInfo)
  {
    $this->chunkInfo = $chunkInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResultChunkInfo[]
   */
  public function getChunkInfo()
  {
    return $this->chunkInfo;
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
   * @param GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResultSnippetInfo[]
   */
  public function setSnippetInfo($snippetInfo)
  {
    $this->snippetInfo = $snippetInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResultSnippetInfo[]
   */
  public function getSnippetInfo()
  {
    return $this->snippetInfo;
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
class_alias(GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResult::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaAnswerStepActionObservationSearchResult');
