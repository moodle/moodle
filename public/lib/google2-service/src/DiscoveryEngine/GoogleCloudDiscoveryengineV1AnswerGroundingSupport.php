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

class GoogleCloudDiscoveryengineV1AnswerGroundingSupport extends \Google\Collection
{
  protected $collection_key = 'sources';
  /**
   * Required. End of the claim, exclusive.
   *
   * @var string
   */
  public $endIndex;
  /**
   * Indicates that this claim required grounding check. When the system decided
   * this claim didn't require attribution/grounding check, this field is set to
   * false. In that case, no grounding check was done for the claim and
   * therefore `grounding_score`, `sources` is not returned.
   *
   * @var bool
   */
  public $groundingCheckRequired;
  /**
   * A score in the range of [0, 1] describing how grounded is a specific claim
   * by the references. Higher value means that the claim is better supported by
   * the reference chunks.
   *
   * @var 
   */
  public $groundingScore;
  protected $sourcesType = GoogleCloudDiscoveryengineV1AnswerCitationSource::class;
  protected $sourcesDataType = 'array';
  /**
   * Required. Index indicates the start of the claim, measured in bytes (UTF-8
   * unicode).
   *
   * @var string
   */
  public $startIndex;

  /**
   * Required. End of the claim, exclusive.
   *
   * @param string $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return string
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * Indicates that this claim required grounding check. When the system decided
   * this claim didn't require attribution/grounding check, this field is set to
   * false. In that case, no grounding check was done for the claim and
   * therefore `grounding_score`, `sources` is not returned.
   *
   * @param bool $groundingCheckRequired
   */
  public function setGroundingCheckRequired($groundingCheckRequired)
  {
    $this->groundingCheckRequired = $groundingCheckRequired;
  }
  /**
   * @return bool
   */
  public function getGroundingCheckRequired()
  {
    return $this->groundingCheckRequired;
  }
  public function setGroundingScore($groundingScore)
  {
    $this->groundingScore = $groundingScore;
  }
  public function getGroundingScore()
  {
    return $this->groundingScore;
  }
  /**
   * Optional. Citation sources for the claim.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerCitationSource[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerCitationSource[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * Required. Index indicates the start of the claim, measured in bytes (UTF-8
   * unicode).
   *
   * @param string $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return string
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerGroundingSupport::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerGroundingSupport');
