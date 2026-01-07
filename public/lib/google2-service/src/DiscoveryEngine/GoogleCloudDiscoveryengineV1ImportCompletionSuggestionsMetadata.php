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

class GoogleCloudDiscoveryengineV1ImportCompletionSuggestionsMetadata extends \Google\Model
{
  /**
   * Operation create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Count of CompletionSuggestions that failed to be imported.
   *
   * @var string
   */
  public $failureCount;
  /**
   * Count of CompletionSuggestions successfully imported.
   *
   * @var string
   */
  public $successCount;
  /**
   * Operation last update time. If the operation is done, this is also the
   * finish time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Operation create time.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Count of CompletionSuggestions that failed to be imported.
   *
   * @param string $failureCount
   */
  public function setFailureCount($failureCount)
  {
    $this->failureCount = $failureCount;
  }
  /**
   * @return string
   */
  public function getFailureCount()
  {
    return $this->failureCount;
  }
  /**
   * Count of CompletionSuggestions successfully imported.
   *
   * @param string $successCount
   */
  public function setSuccessCount($successCount)
  {
    $this->successCount = $successCount;
  }
  /**
   * @return string
   */
  public function getSuccessCount()
  {
    return $this->successCount;
  }
  /**
   * Operation last update time. If the operation is done, this is also the
   * finish time.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ImportCompletionSuggestionsMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ImportCompletionSuggestionsMetadata');
