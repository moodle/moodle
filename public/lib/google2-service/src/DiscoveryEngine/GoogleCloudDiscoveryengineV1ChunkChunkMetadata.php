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

class GoogleCloudDiscoveryengineV1ChunkChunkMetadata extends \Google\Collection
{
  protected $collection_key = 'previousChunks';
  protected $nextChunksType = GoogleCloudDiscoveryengineV1Chunk::class;
  protected $nextChunksDataType = 'array';
  protected $previousChunksType = GoogleCloudDiscoveryengineV1Chunk::class;
  protected $previousChunksDataType = 'array';

  /**
   * The next chunks of the current chunk. The number is controlled by
   * SearchRequest.ContentSearchSpec.ChunkSpec.num_next_chunks. This field is
   * only populated on SearchService.Search API.
   *
   * @param GoogleCloudDiscoveryengineV1Chunk[] $nextChunks
   */
  public function setNextChunks($nextChunks)
  {
    $this->nextChunks = $nextChunks;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Chunk[]
   */
  public function getNextChunks()
  {
    return $this->nextChunks;
  }
  /**
   * The previous chunks of the current chunk. The number is controlled by
   * SearchRequest.ContentSearchSpec.ChunkSpec.num_previous_chunks. This field
   * is only populated on SearchService.Search API.
   *
   * @param GoogleCloudDiscoveryengineV1Chunk[] $previousChunks
   */
  public function setPreviousChunks($previousChunks)
  {
    $this->previousChunks = $previousChunks;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Chunk[]
   */
  public function getPreviousChunks()
  {
    return $this->previousChunks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ChunkChunkMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ChunkChunkMetadata');
