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

class GoogleCloudAiplatformV1RetrieveMemoriesRequestSimpleRetrievalParams extends \Google\Model
{
  /**
   * Optional. The maximum number of memories to return. The service may return
   * fewer than this value. If unspecified, at most 3 memories will be returned.
   * The maximum value is 100; values above 100 will be coerced to 100.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token, received from a previous `RetrieveMemories` call.
   * Provide this to retrieve the subsequent page.
   *
   * @var string
   */
  public $pageToken;

  /**
   * Optional. The maximum number of memories to return. The service may return
   * fewer than this value. If unspecified, at most 3 memories will be returned.
   * The maximum value is 100; values above 100 will be coerced to 100.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Optional. A page token, received from a previous `RetrieveMemories` call.
   * Provide this to retrieve the subsequent page.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RetrieveMemoriesRequestSimpleRetrievalParams::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RetrieveMemoriesRequestSimpleRetrievalParams');
