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

class GoogleCloudDiscoveryengineV1alphaDeleteUserStoreMetadata extends \Google\Model
{
  /**
   * The number of end users under the user store that failed to be deleted.
   *
   * @var string
   */
  public $failureCount;
  /**
   * The number of end users under the user store that were successfully
   * deleted.
   *
   * @var string
   */
  public $successCount;

  /**
   * The number of end users under the user store that failed to be deleted.
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
   * The number of end users under the user store that were successfully
   * deleted.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDeleteUserStoreMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDeleteUserStoreMetadata');
