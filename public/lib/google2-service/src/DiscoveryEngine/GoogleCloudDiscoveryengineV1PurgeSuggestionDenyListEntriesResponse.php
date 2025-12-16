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

class GoogleCloudDiscoveryengineV1PurgeSuggestionDenyListEntriesResponse extends \Google\Collection
{
  protected $collection_key = 'errorSamples';
  protected $errorSamplesType = GoogleRpcStatus::class;
  protected $errorSamplesDataType = 'array';
  /**
   * Number of suggestion deny list entries purged.
   *
   * @var string
   */
  public $purgeCount;

  /**
   * A sample of errors encountered while processing the request.
   *
   * @param GoogleRpcStatus[] $errorSamples
   */
  public function setErrorSamples($errorSamples)
  {
    $this->errorSamples = $errorSamples;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrorSamples()
  {
    return $this->errorSamples;
  }
  /**
   * Number of suggestion deny list entries purged.
   *
   * @param string $purgeCount
   */
  public function setPurgeCount($purgeCount)
  {
    $this->purgeCount = $purgeCount;
  }
  /**
   * @return string
   */
  public function getPurgeCount()
  {
    return $this->purgeCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1PurgeSuggestionDenyListEntriesResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1PurgeSuggestionDenyListEntriesResponse');
