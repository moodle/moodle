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

class GoogleCloudDiscoveryengineV1DocumentIndexStatus extends \Google\Collection
{
  protected $collection_key = 'errorSamples';
  protected $errorSamplesType = GoogleRpcStatus::class;
  protected $errorSamplesDataType = 'array';
  /**
   * The time when the document was indexed. If this field is populated, it
   * means the document has been indexed.
   *
   * @var string
   */
  public $indexTime;
  /**
   * Immutable. The message indicates the document index is in progress. If this
   * field is populated, the document index is pending.
   *
   * @var string
   */
  public $pendingMessage;

  /**
   * A sample of errors encountered while indexing the document. If this field
   * is populated, the document is not indexed due to errors.
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
   * The time when the document was indexed. If this field is populated, it
   * means the document has been indexed.
   *
   * @param string $indexTime
   */
  public function setIndexTime($indexTime)
  {
    $this->indexTime = $indexTime;
  }
  /**
   * @return string
   */
  public function getIndexTime()
  {
    return $this->indexTime;
  }
  /**
   * Immutable. The message indicates the document index is in progress. If this
   * field is populated, the document index is pending.
   *
   * @param string $pendingMessage
   */
  public function setPendingMessage($pendingMessage)
  {
    $this->pendingMessage = $pendingMessage;
  }
  /**
   * @return string
   */
  public function getPendingMessage()
  {
    return $this->pendingMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1DocumentIndexStatus::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1DocumentIndexStatus');
