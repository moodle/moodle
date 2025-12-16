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

class GoogleCloudDiscoveryengineV1CheckGroundingResponse extends \Google\Collection
{
  protected $collection_key = 'claims';
  protected $citedChunksType = GoogleCloudDiscoveryengineV1FactChunk::class;
  protected $citedChunksDataType = 'array';
  protected $citedFactsType = GoogleCloudDiscoveryengineV1CheckGroundingResponseCheckGroundingFactChunk::class;
  protected $citedFactsDataType = 'array';
  protected $claimsType = GoogleCloudDiscoveryengineV1CheckGroundingResponseClaim::class;
  protected $claimsDataType = 'array';
  /**
   * The support score for the input answer candidate. Higher the score, higher
   * is the fraction of claims that are supported by the provided facts. This is
   * always set when a response is returned.
   *
   * @var float
   */
  public $supportScore;

  /**
   * List of facts cited across all claims in the answer candidate. These are
   * derived from the facts supplied in the request.
   *
   * @param GoogleCloudDiscoveryengineV1FactChunk[] $citedChunks
   */
  public function setCitedChunks($citedChunks)
  {
    $this->citedChunks = $citedChunks;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1FactChunk[]
   */
  public function getCitedChunks()
  {
    return $this->citedChunks;
  }
  /**
   * List of facts cited across all claims in the answer candidate. These are
   * derived from the facts supplied in the request.
   *
   * @param GoogleCloudDiscoveryengineV1CheckGroundingResponseCheckGroundingFactChunk[] $citedFacts
   */
  public function setCitedFacts($citedFacts)
  {
    $this->citedFacts = $citedFacts;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1CheckGroundingResponseCheckGroundingFactChunk[]
   */
  public function getCitedFacts()
  {
    return $this->citedFacts;
  }
  /**
   * Claim texts and citation info across all claims in the answer candidate.
   *
   * @param GoogleCloudDiscoveryengineV1CheckGroundingResponseClaim[] $claims
   */
  public function setClaims($claims)
  {
    $this->claims = $claims;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1CheckGroundingResponseClaim[]
   */
  public function getClaims()
  {
    return $this->claims;
  }
  /**
   * The support score for the input answer candidate. Higher the score, higher
   * is the fraction of claims that are supported by the provided facts. This is
   * always set when a response is returned.
   *
   * @param float $supportScore
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
class_alias(GoogleCloudDiscoveryengineV1CheckGroundingResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CheckGroundingResponse');
