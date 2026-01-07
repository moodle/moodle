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

class GoogleCloudDiscoveryengineV1alphaEstimateDataSizeResponse extends \Google\Model
{
  /**
   * Data size in terms of bytes.
   *
   * @var string
   */
  public $dataSizeBytes;
  /**
   * Total number of documents.
   *
   * @var string
   */
  public $documentCount;

  /**
   * Data size in terms of bytes.
   *
   * @param string $dataSizeBytes
   */
  public function setDataSizeBytes($dataSizeBytes)
  {
    $this->dataSizeBytes = $dataSizeBytes;
  }
  /**
   * @return string
   */
  public function getDataSizeBytes()
  {
    return $this->dataSizeBytes;
  }
  /**
   * Total number of documents.
   *
   * @param string $documentCount
   */
  public function setDocumentCount($documentCount)
  {
    $this->documentCount = $documentCount;
  }
  /**
   * @return string
   */
  public function getDocumentCount()
  {
    return $this->documentCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaEstimateDataSizeResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaEstimateDataSizeResponse');
