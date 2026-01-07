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

class GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataGroundingSupport extends \Google\Collection
{
  protected $collection_key = 'supportChunkIndices';
  /**
   * @var string
   */
  public $claimText;
  /**
   * @var int[]
   */
  public $supportChunkIndices;
  /**
   * @var float
   */
  public $supportScore;

  /**
   * @param string
   */
  public function setClaimText($claimText)
  {
    $this->claimText = $claimText;
  }
  /**
   * @return string
   */
  public function getClaimText()
  {
    return $this->claimText;
  }
  /**
   * @param int[]
   */
  public function setSupportChunkIndices($supportChunkIndices)
  {
    $this->supportChunkIndices = $supportChunkIndices;
  }
  /**
   * @return int[]
   */
  public function getSupportChunkIndices()
  {
    return $this->supportChunkIndices;
  }
  /**
   * @param float
   */
  public function setSupportScore($supportScore)
  {
    $this->supportScore = $supportScore;
  }
  /**
   * @return float
   */
  public function getSupportScore()
  {
    return $this->supportScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataGroundingSupport::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentResponseCandidateGroundingMetadataGroundingSupport');
