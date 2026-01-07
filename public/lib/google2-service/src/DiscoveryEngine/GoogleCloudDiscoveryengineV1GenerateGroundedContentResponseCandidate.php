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

class GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidate extends \Google\Model
{
  protected $contentType = GoogleCloudDiscoveryengineV1GroundedGenerationContent::class;
  protected $contentDataType = '';
  protected $groundingMetadataType = GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadata::class;
  protected $groundingMetadataDataType = '';
  /**
   * @var float
   */
  public $groundingScore;
  /**
   * @var int
   */
  public $index;

  /**
   * @param GoogleCloudDiscoveryengineV1GroundedGenerationContent
   */
  public function setContent(GoogleCloudDiscoveryengineV1GroundedGenerationContent $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GroundedGenerationContent
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadata
   */
  public function setGroundingMetadata(GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadata $groundingMetadata)
  {
    $this->groundingMetadata = $groundingMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadata
   */
  public function getGroundingMetadata()
  {
    return $this->groundingMetadata;
  }
  /**
   * @param float
   */
  public function setGroundingScore($groundingScore)
  {
    $this->groundingScore = $groundingScore;
  }
  /**
   * @return float
   */
  public function getGroundingScore()
  {
    return $this->groundingScore;
  }
  /**
   * @param int
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidate::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidate');
