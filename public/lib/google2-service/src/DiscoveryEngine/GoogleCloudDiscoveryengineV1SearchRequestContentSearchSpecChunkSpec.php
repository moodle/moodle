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

class GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecChunkSpec extends \Google\Model
{
  /**
   * The number of next chunks to be returned of the current chunk. The maximum
   * allowed value is 3. If not specified, no next chunks will be returned.
   *
   * @var int
   */
  public $numNextChunks;
  /**
   * The number of previous chunks to be returned of the current chunk. The
   * maximum allowed value is 3. If not specified, no previous chunks will be
   * returned.
   *
   * @var int
   */
  public $numPreviousChunks;

  /**
   * The number of next chunks to be returned of the current chunk. The maximum
   * allowed value is 3. If not specified, no next chunks will be returned.
   *
   * @param int $numNextChunks
   */
  public function setNumNextChunks($numNextChunks)
  {
    $this->numNextChunks = $numNextChunks;
  }
  /**
   * @return int
   */
  public function getNumNextChunks()
  {
    return $this->numNextChunks;
  }
  /**
   * The number of previous chunks to be returned of the current chunk. The
   * maximum allowed value is 3. If not specified, no previous chunks will be
   * returned.
   *
   * @param int $numPreviousChunks
   */
  public function setNumPreviousChunks($numPreviousChunks)
  {
    $this->numPreviousChunks = $numPreviousChunks;
  }
  /**
   * @return int
   */
  public function getNumPreviousChunks()
  {
    return $this->numPreviousChunks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecChunkSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecChunkSpec');
