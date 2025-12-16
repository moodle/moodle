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

class GoogleCloudDiscoveryengineV1alphaAnswerReferenceChunkInfo extends \Google\Collection
{
  protected $collection_key = 'blobAttachmentIndexes';
  /**
   * Output only. Stores indexes of blobattachments linked to this chunk.
   *
   * @var string[]
   */
  public $blobAttachmentIndexes;
  /**
   * Chunk resource name.
   *
   * @var string
   */
  public $chunk;
  /**
   * Chunk textual content.
   *
   * @var string
   */
  public $content;
  protected $documentMetadataType = GoogleCloudDiscoveryengineV1alphaAnswerReferenceChunkInfoDocumentMetadata::class;
  protected $documentMetadataDataType = '';
  /**
   * The relevance of the chunk for a given query. Values range from 0.0
   * (completely irrelevant) to 1.0 (completely relevant). This value is for
   * informational purpose only. It may change for the same query and chunk at
   * any time due to a model retraining or change in implementation.
   *
   * @var float
   */
  public $relevanceScore;

  /**
   * Output only. Stores indexes of blobattachments linked to this chunk.
   *
   * @param string[] $blobAttachmentIndexes
   */
  public function setBlobAttachmentIndexes($blobAttachmentIndexes)
  {
    $this->blobAttachmentIndexes = $blobAttachmentIndexes;
  }
  /**
   * @return string[]
   */
  public function getBlobAttachmentIndexes()
  {
    return $this->blobAttachmentIndexes;
  }
  /**
   * Chunk resource name.
   *
   * @param string $chunk
   */
  public function setChunk($chunk)
  {
    $this->chunk = $chunk;
  }
  /**
   * @return string
   */
  public function getChunk()
  {
    return $this->chunk;
  }
  /**
   * Chunk textual content.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Document metadata.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerReferenceChunkInfoDocumentMetadata $documentMetadata
   */
  public function setDocumentMetadata(GoogleCloudDiscoveryengineV1alphaAnswerReferenceChunkInfoDocumentMetadata $documentMetadata)
  {
    $this->documentMetadata = $documentMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerReferenceChunkInfoDocumentMetadata
   */
  public function getDocumentMetadata()
  {
    return $this->documentMetadata;
  }
  /**
   * The relevance of the chunk for a given query. Values range from 0.0
   * (completely irrelevant) to 1.0 (completely relevant). This value is for
   * informational purpose only. It may change for the same query and chunk at
   * any time due to a model retraining or change in implementation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAnswerReferenceChunkInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAnswerReferenceChunkInfo');
