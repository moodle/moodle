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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1GroundingChunk extends \Google\Model
{
  protected $mapsType = GoogleCloudAiplatformV1GroundingChunkMaps::class;
  protected $mapsDataType = '';
  protected $retrievedContextType = GoogleCloudAiplatformV1GroundingChunkRetrievedContext::class;
  protected $retrievedContextDataType = '';
  protected $webType = GoogleCloudAiplatformV1GroundingChunkWeb::class;
  protected $webDataType = '';

  /**
   * A grounding chunk from Google Maps. See the `Maps` message for details.
   *
   * @param GoogleCloudAiplatformV1GroundingChunkMaps $maps
   */
  public function setMaps(GoogleCloudAiplatformV1GroundingChunkMaps $maps)
  {
    $this->maps = $maps;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingChunkMaps
   */
  public function getMaps()
  {
    return $this->maps;
  }
  /**
   * A grounding chunk from a data source retrieved by a retrieval tool, such as
   * Vertex AI Search. See the `RetrievedContext` message for details
   *
   * @param GoogleCloudAiplatformV1GroundingChunkRetrievedContext $retrievedContext
   */
  public function setRetrievedContext(GoogleCloudAiplatformV1GroundingChunkRetrievedContext $retrievedContext)
  {
    $this->retrievedContext = $retrievedContext;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingChunkRetrievedContext
   */
  public function getRetrievedContext()
  {
    return $this->retrievedContext;
  }
  /**
   * A grounding chunk from a web page, typically from Google Search. See the
   * `Web` message for details.
   *
   * @param GoogleCloudAiplatformV1GroundingChunkWeb $web
   */
  public function setWeb(GoogleCloudAiplatformV1GroundingChunkWeb $web)
  {
    $this->web = $web;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingChunkWeb
   */
  public function getWeb()
  {
    return $this->web;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingChunk::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingChunk');
