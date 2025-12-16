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

class GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataRetrievalMetadata extends \Google\Model
{
  protected $dynamicRetrievalMetadataType = GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataDynamicRetrievalMetadata::class;
  protected $dynamicRetrievalMetadataDataType = '';
  /**
   * @var string
   */
  public $source;

  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataDynamicRetrievalMetadata
   */
  public function setDynamicRetrievalMetadata(GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataDynamicRetrievalMetadata $dynamicRetrievalMetadata)
  {
    $this->dynamicRetrievalMetadata = $dynamicRetrievalMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataDynamicRetrievalMetadata
   */
  public function getDynamicRetrievalMetadata()
  {
    return $this->dynamicRetrievalMetadata;
  }
  /**
   * @param string
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataRetrievalMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataRetrievalMetadata');
