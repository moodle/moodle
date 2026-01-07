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

class GoogleCloudDiscoveryengineV1SearchResponseSearchResult extends \Google\Model
{
  protected $chunkType = GoogleCloudDiscoveryengineV1Chunk::class;
  protected $chunkDataType = '';
  protected $documentType = GoogleCloudDiscoveryengineV1Document::class;
  protected $documentDataType = '';
  /**
   * Document.id of the searched Document.
   *
   * @var string
   */
  public $id;
  protected $modelScoresType = GoogleCloudDiscoveryengineV1DoubleList::class;
  protected $modelScoresDataType = 'map';
  protected $rankSignalsType = GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignals::class;
  protected $rankSignalsDataType = '';

  /**
   * The chunk data in the search response if the
   * SearchRequest.ContentSearchSpec.search_result_mode is set to CHUNKS.
   *
   * @param GoogleCloudDiscoveryengineV1Chunk $chunk
   */
  public function setChunk(GoogleCloudDiscoveryengineV1Chunk $chunk)
  {
    $this->chunk = $chunk;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Chunk
   */
  public function getChunk()
  {
    return $this->chunk;
  }
  /**
   * The document data snippet in the search response. Only fields that are
   * marked as `retrievable` are populated.
   *
   * @param GoogleCloudDiscoveryengineV1Document $document
   */
  public function setDocument(GoogleCloudDiscoveryengineV1Document $document)
  {
    $this->document = $document;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Document
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Document.id of the searched Document.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Google provided available scores.
   *
   * @param GoogleCloudDiscoveryengineV1DoubleList[] $modelScores
   */
  public function setModelScores($modelScores)
  {
    $this->modelScores = $modelScores;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1DoubleList[]
   */
  public function getModelScores()
  {
    return $this->modelScores;
  }
  /**
   * Optional. A set of ranking signals associated with the result.
   *
   * @param GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignals $rankSignals
   */
  public function setRankSignals(GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignals $rankSignals)
  {
    $this->rankSignals = $rankSignals;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchResponseSearchResultRankSignals
   */
  public function getRankSignals()
  {
    return $this->rankSignals;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponseSearchResult::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponseSearchResult');
