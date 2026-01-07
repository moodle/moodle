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

class GoogleCloudDiscoveryengineV1betaFactChunk extends \Google\Model
{
  /**
   * @var string
   */
  public $chunkText;
  /**
   * @var string
   */
  public $source;
  /**
   * @var string[]
   */
  public $sourceMetadata;

  /**
   * @param string
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
   * @param string
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
   * @param string[]
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaFactChunk::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaFactChunk');
