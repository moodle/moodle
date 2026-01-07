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

class GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadata extends \Google\Collection
{
  protected $collection_key = 'segments';
  protected $referencesType = GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataReference::class;
  protected $referencesDataType = 'array';
  protected $segmentsType = GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataSegment::class;
  protected $segmentsDataType = 'array';

  /**
   * References for the grounded text.
   *
   * @param GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataReference[] $references
   */
  public function setReferences($references)
  {
    $this->references = $references;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataReference[]
   */
  public function getReferences()
  {
    return $this->references;
  }
  /**
   * Grounding information for parts of the text.
   *
   * @param GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataSegment[] $segments
   */
  public function setSegments($segments)
  {
    $this->segments = $segments;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataSegment[]
   */
  public function getSegments()
  {
    return $this->segments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadata');
