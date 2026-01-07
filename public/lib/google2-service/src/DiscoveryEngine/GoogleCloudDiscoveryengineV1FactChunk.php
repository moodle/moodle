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

class GoogleCloudDiscoveryengineV1FactChunk extends \Google\Model
{
  /**
   * Text content of the fact chunk. Can be at most 10K characters long.
   *
   * @var string
   */
  public $chunkText;
  /**
   * The domain of the source.
   *
   * @var string
   */
  public $domain;
  /**
   * The index of this chunk. Currently, only used for the streaming mode.
   *
   * @var int
   */
  public $index;
  /**
   * Source from which this fact chunk was retrieved. If it was retrieved from
   * the GroundingFacts provided in the request then this field will contain the
   * index of the specific fact from which this chunk was retrieved.
   *
   * @var string
   */
  public $source;
  /**
   * More fine-grained information for the source reference.
   *
   * @var string[]
   */
  public $sourceMetadata;
  /**
   * The title of the source.
   *
   * @var string
   */
  public $title;
  /**
   * The URI of the source.
   *
   * @var string
   */
  public $uri;

  /**
   * Text content of the fact chunk. Can be at most 10K characters long.
   *
   * @param string $chunkText
   */
  public function setChunkText($chunkText)
  {
    $this->chunkText = $chunkText;
  }
  /**
   * @return string
   */
  public function getChunkText()
  {
    return $this->chunkText;
  }
  /**
   * The domain of the source.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The index of this chunk. Currently, only used for the streaming mode.
   *
   * @param int $index
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
  /**
   * Source from which this fact chunk was retrieved. If it was retrieved from
   * the GroundingFacts provided in the request then this field will contain the
   * index of the specific fact from which this chunk was retrieved.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * More fine-grained information for the source reference.
   *
   * @param string[] $sourceMetadata
   */
  public function setSourceMetadata($sourceMetadata)
  {
    $this->sourceMetadata = $sourceMetadata;
  }
  /**
   * @return string[]
   */
  public function getSourceMetadata()
  {
    return $this->sourceMetadata;
  }
  /**
   * The title of the source.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The URI of the source.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1FactChunk::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1FactChunk');
