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

class GoogleCloudDiscoveryengineV1CheckGroundingResponseCheckGroundingFactChunk extends \Google\Model
{
  /**
   * Text content of the fact chunk. Can be at most 10K characters long.
   *
   * @var string
   */
  public $chunkText;

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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CheckGroundingResponseCheckGroundingFactChunk::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CheckGroundingResponseCheckGroundingFactChunk');
