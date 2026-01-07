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

class GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignals extends \Google\Collection
{
  protected $collection_key = 'customSignals';
  /**
   * Optional. Combined custom boosts for a doc.
   *
   * @var float
   */
  public $boostingFactor;
  protected $customSignalsType = GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignalsCustomSignal::class;
  protected $customSignalsDataType = 'array';
  /**
   * Optional. The default rank of the result.
   *
   * @var float
   */
  public $defaultRank;
  /**
   * Optional. Age of the document in hours.
   *
   * @var float
   */
  public $documentAge;
  /**
   * Optional. Keyword matching adjustment.
   *
   * @var float
   */
  public $keywordSimilarityScore;
  /**
   * Optional. Predicted conversion rate adjustment as a rank.
   *
   * @var float
   */
  public $pctrRank;
  /**
   * Optional. Semantic relevance adjustment.
   *
   * @var float
   */
  public $relevanceScore;
  /**
   * Optional. Semantic similarity adjustment.
   *
   * @var float
   */
  public $semanticSimilarityScore;
  /**
   * Optional. Topicality adjustment as a rank.
   *
   * @var float
   */
  public $topicalityRank;

  /**
   * Optional. Combined custom boosts for a doc.
   *
   * @param float $boostingFactor
   */
  public function setBoostingFactor($boostingFactor)
  {
    $this->boostingFactor = $boostingFactor;
  }
  /**
   * @return float
   */
  public function getBoostingFactor()
  {
    return $this->boostingFactor;
  }
  /**
   * Optional. A list of custom clearbox signals.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignalsCustomSignal[] $customSignals
   */
  public function setCustomSignals($customSignals)
  {
    $this->customSignals = $customSignals;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignalsCustomSignal[]
   */
  public function getCustomSignals()
  {
    return $this->customSignals;
  }
  /**
   * Optional. The default rank of the result.
   *
   * @param float $defaultRank
   */
  public function setDefaultRank($defaultRank)
  {
    $this->defaultRank = $defaultRank;
  }
  /**
   * @return float
   */
  public function getDefaultRank()
  {
    return $this->defaultRank;
  }
  /**
   * Optional. Age of the document in hours.
   *
   * @param float $documentAge
   */
  public function setDocumentAge($documentAge)
  {
    $this->documentAge = $documentAge;
  }
  /**
   * @return float
   */
  public function getDocumentAge()
  {
    return $this->documentAge;
  }
  /**
   * Optional. Keyword matching adjustment.
   *
   * @param float $keywordSimilarityScore
   */
  public function setKeywordSimilarityScore($keywordSimilarityScore)
  {
    $this->keywordSimilarityScore = $keywordSimilarityScore;
  }
  /**
   * @return float
   */
  public function getKeywordSimilarityScore()
  {
    return $this->keywordSimilarityScore;
  }
  /**
   * Optional. Predicted conversion rate adjustment as a rank.
   *
   * @param float $pctrRank
   */
  public function setPctrRank($pctrRank)
  {
    $this->pctrRank = $pctrRank;
  }
  /**
   * @return float
   */
  public function getPctrRank()
  {
    return $this->pctrRank;
  }
  /**
   * Optional. Semantic relevance adjustment.
   *
   * @param float $relevanceScore
   */
  public function setRelevanceScore($relevanceScore)
  {
    $this->relevanceScore = $relevanceScore;
  }
  /**
   * @return float
   */
  public function getRelevanceScore()
  {
    return $this->relevanceScore;
  }
  /**
   * Optional. Semantic similarity adjustment.
   *
   * @param float $semanticSimilarityScore
   */
  public function setSemanticSimilarityScore($semanticSimilarityScore)
  {
    $this->semanticSimilarityScore = $semanticSimilarityScore;
  }
  /**
   * @return float
   */
  public function getSemanticSimilarityScore()
  {
    return $this->semanticSimilarityScore;
  }
  /**
   * Optional. Topicality adjustment as a rank.
   *
   * @param float $topicalityRank
   */
  public function setTopicalityRank($topicalityRank)
  {
    $this->topicalityRank = $topicalityRank;
  }
  /**
   * @return float
   */
  public function getTopicalityRank()
  {
    return $this->topicalityRank;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignals::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignals');
