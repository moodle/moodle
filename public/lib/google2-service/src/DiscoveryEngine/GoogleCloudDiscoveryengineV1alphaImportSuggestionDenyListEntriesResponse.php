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

class GoogleCloudDiscoveryengineV1alphaImportSuggestionDenyListEntriesResponse extends \Google\Collection
{
  protected $collection_key = 'errorSamples';
  protected $errorSamplesType = GoogleRpcStatus::class;
  protected $errorSamplesDataType = 'array';
  /**
   * Count of deny list entries that failed to be imported.
   *
   * @var string
   */
  public $failedEntriesCount;
  /**
   * Count of deny list entries successfully imported.
   *
   * @var string
   */
  public $importedEntriesCount;

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
   * Count of deny list entries that failed to be imported.
   *
   * @param string $failedEntriesCount
   */
  public function setFailedEntriesCount($failedEntriesCount)
  {
    $this->failedEntriesCount = $failedEntriesCount;
  }
  /**
   * @return string
   */
  public function getFailedEntriesCount()
  {
    return $this->failedEntriesCount;
  }
  /**
   * Count of deny list entries successfully imported.
   *
   * @param string $importedEntriesCount
   */
  public function setImportedEntriesCount($importedEntriesCount)
  {
    $this->importedEntriesCount = $importedEntriesCount;
  }
  /**
   * @return string
   */
  public function getImportedEntriesCount()
  {
    return $this->importedEntriesCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaImportSuggestionDenyListEntriesResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaImportSuggestionDenyListEntriesResponse');
