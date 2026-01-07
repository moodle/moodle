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

class GoogleCloudDiscoveryengineV1betaIdentityMappingEntryOperationMetadata extends \Google\Model
{
  /**
   * The number of IdentityMappingEntries that failed to be processed.
   *
   * @var string
   */
  public $failureCount;
  /**
   * The number of IdentityMappingEntries that were successfully processed.
   *
   * @var string
   */
  public $successCount;
  /**
   * The total number of IdentityMappingEntries that were processed.
   *
   * @var string
   */
  public $totalCount;

  /**
   * The number of IdentityMappingEntries that failed to be processed.
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
   * The number of IdentityMappingEntries that were successfully processed.
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
   * The total number of IdentityMappingEntries that were processed.
   *
   * @param string $totalCount
   */
  public function setTotalCount($totalCount)
  {
    $this->totalCount = $totalCount;
  }
  /**
   * @return string
   */
  public function getTotalCount()
  {
    return $this->totalCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaIdentityMappingEntryOperationMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaIdentityMappingEntryOperationMetadata');
