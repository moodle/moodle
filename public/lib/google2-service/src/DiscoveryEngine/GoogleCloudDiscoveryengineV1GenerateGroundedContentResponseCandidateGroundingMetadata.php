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

class GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadata extends \Google\Collection
{
  protected $collection_key = 'webSearchQueries';
  protected $groundingSupportType = GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataGroundingSupport::class;
  protected $groundingSupportDataType = 'array';
  protected $retrievalMetadataType = GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataRetrievalMetadata::class;
  protected $retrievalMetadataDataType = 'array';
  protected $searchEntryPointType = GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataSearchEntryPoint::class;
  protected $searchEntryPointDataType = '';
  protected $supportChunksType = GoogleCloudDiscoveryengineV1FactChunk::class;
  protected $supportChunksDataType = 'array';
  /**
   * @var string[]
   */
  public $webSearchQueries;

  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataGroundingSupport[]
   */
  public function setGroundingSupport($groundingSupport)
  {
    $this->groundingSupport = $groundingSupport;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataGroundingSupport[]
   */
  public function getGroundingSupport()
  {
    return $this->groundingSupport;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataRetrievalMetadata[]
   */
  public function setRetrievalMetadata($retrievalMetadata)
  {
    $this->retrievalMetadata = $retrievalMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataRetrievalMetadata[]
   */
  public function getRetrievalMetadata()
  {
    return $this->retrievalMetadata;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataSearchEntryPoint
   */
  public function setSearchEntryPoint(GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataSearchEntryPoint $searchEntryPoint)
  {
    $this->searchEntryPoint = $searchEntryPoint;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataSearchEntryPoint
   */
  public function getSearchEntryPoint()
  {
    return $this->searchEntryPoint;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1FactChunk[]
   */
  public function setSupportChunks($supportChunks)
  {
    $this->supportChunks = $supportChunks;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1FactChunk[]
   */
  public function getSupportChunks()
  {
    return $this->supportChunks;
  }
  /**
   * @param string[]
   */
  public function setWebSearchQueries($webSearchQueries)
  {
    $this->webSearchQueries = $webSearchQueries;
  }
  /**
   * @return string[]
   */
  public function getWebSearchQueries()
  {
    return $this->webSearchQueries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadata');
