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

class GoogleCloudAiplatformV1RetrieveMemoriesResponse extends \Google\Collection
{
  protected $collection_key = 'retrievedMemories';
  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted, there are no subsequent pages. This token is not set if
   * similarity search was used for retrieval.
   *
   * @var string
   */
  public $nextPageToken;
  protected $retrievedMemoriesType = GoogleCloudAiplatformV1RetrieveMemoriesResponseRetrievedMemory::class;
  protected $retrievedMemoriesDataType = 'array';

  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted, there are no subsequent pages. This token is not set if
   * similarity search was used for retrieval.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * The retrieved memories.
   *
   * @param GoogleCloudAiplatformV1RetrieveMemoriesResponseRetrievedMemory[] $retrievedMemories
   */
  public function setRetrievedMemories($retrievedMemories)
  {
    $this->retrievedMemories = $retrievedMemories;
  }
  /**
   * @return GoogleCloudAiplatformV1RetrieveMemoriesResponseRetrievedMemory[]
   */
  public function getRetrievedMemories()
  {
    return $this->retrievedMemories;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RetrieveMemoriesResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RetrieveMemoriesResponse');
