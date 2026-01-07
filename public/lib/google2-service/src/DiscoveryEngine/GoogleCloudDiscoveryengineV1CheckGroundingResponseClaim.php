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

class GoogleCloudDiscoveryengineV1CheckGroundingResponseClaim extends \Google\Collection
{
  protected $collection_key = 'citationIndices';
  /**
   * A list of indices (into 'cited_chunks') specifying the citations associated
   * with the claim. For instance [1,3,4] means that cited_chunks[1],
   * cited_chunks[3], cited_chunks[4] are the facts cited supporting for the
   * claim. A citation to a fact indicates that the claim is supported by the
   * fact.
   *
   * @var int[]
   */
  public $citationIndices;
  /**
   * Text for the claim in the answer candidate. Always provided regardless of
   * whether citations or anti-citations are found.
   *
   * @var string
   */
  public $claimText;
  /**
   * Position indicating the end of the claim in the answer candidate,
   * exclusive, in bytes. Note that this is not measured in characters and,
   * therefore, must be rendered as such. For example, if the claim text
   * contains non-ASCII characters, the start and end positions vary when
   * measured in characters (programming-language-dependent) and when measured
   * in bytes (programming-language-independent).
   *
   * @var int
   */
  public $endPos;
  /**
   * Indicates that this claim required grounding check. When the system decided
   * this claim doesn't require attribution/grounding check, this field will be
   * set to false. In that case, no grounding check was done for the claim and
   * therefore citation_indices should not be returned.
   *
   * @var bool
   */
  public $groundingCheckRequired;
  /**
   * Confidence score for the claim in the answer candidate, in the range of [0,
   * 1]. This is set only when
   * `CheckGroundingRequest.grounding_spec.enable_claim_level_score` is true.
   *
   * @var 
   */
  public $score;
  /**
   * Position indicating the start of the claim in the answer candidate,
   * measured in bytes. Note that this is not measured in characters and,
   * therefore, must be rendered in the user interface keeping in mind that some
   * characters may take more than one byte. For example, if the claim text
   * contains non-ASCII characters, the start and end positions vary when
   * measured in characters (programming-language-dependent) and when measured
   * in bytes (programming-language-independent).
   *
   * @var int
   */
  public $startPos;

  /**
   * A list of indices (into 'cited_chunks') specifying the citations associated
   * with the claim. For instance [1,3,4] means that cited_chunks[1],
   * cited_chunks[3], cited_chunks[4] are the facts cited supporting for the
   * claim. A citation to a fact indicates that the claim is supported by the
   * fact.
   *
   * @param int[] $citationIndices
   */
  public function setCitationIndices($citationIndices)
  {
    $this->citationIndices = $citationIndices;
  }
  /**
   * @return int[]
   */
  public function getCitationIndices()
  {
    return $this->citationIndices;
  }
  /**
   * Text for the claim in the answer candidate. Always provided regardless of
   * whether citations or anti-citations are found.
   *
   * @param string $claimText
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
   * Position indicating the end of the claim in the answer candidate,
   * exclusive, in bytes. Note that this is not measured in characters and,
   * therefore, must be rendered as such. For example, if the claim text
   * contains non-ASCII characters, the start and end positions vary when
   * measured in characters (programming-language-dependent) and when measured
   * in bytes (programming-language-independent).
   *
   * @param int $endPos
   */
  public function setEndPos($endPos)
  {
    $this->endPos = $endPos;
  }
  /**
   * @return int
   */
  public function getEndPos()
  {
    return $this->endPos;
  }
  /**
   * Indicates that this claim required grounding check. When the system decided
   * this claim doesn't require attribution/grounding check, this field will be
   * set to false. In that case, no grounding check was done for the claim and
   * therefore citation_indices should not be returned.
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
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * Position indicating the start of the claim in the answer candidate,
   * measured in bytes. Note that this is not measured in characters and,
   * therefore, must be rendered in the user interface keeping in mind that some
   * characters may take more than one byte. For example, if the claim text
   * contains non-ASCII characters, the start and end positions vary when
   * measured in characters (programming-language-dependent) and when measured
   * in bytes (programming-language-independent).
   *
   * @param int $startPos
   */
  public function setStartPos($startPos)
  {
    $this->startPos = $startPos;
  }
  /**
   * @return int
   */
  public function getStartPos()
  {
    return $this->startPos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CheckGroundingResponseClaim::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CheckGroundingResponseClaim');
