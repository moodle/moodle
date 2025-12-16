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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1beta3BatchProcessMetadata extends \Google\Collection
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Request operation is waiting for scheduling.
   */
  public const STATE_WAITING = 'WAITING';
  /**
   * Request is being processed.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The batch processing completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The batch processing was being cancelled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The batch processing was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The batch processing has failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'individualProcessStatuses';
  /**
   * The creation time of the operation.
   *
   * @var string
   */
  public $createTime;
  protected $individualProcessStatusesType = GoogleCloudDocumentaiV1beta3BatchProcessMetadataIndividualProcessStatus::class;
  protected $individualProcessStatusesDataType = 'array';
  /**
   * The state of the current batch processing.
   *
   * @var string
   */
  public $state;
  /**
   * A message providing more details about the current state of processing. For
   * example, the error message if the operation is failed.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * The last update time of the operation.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The creation time of the operation.
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
   * The list of response details of each document.
   *
   * @param GoogleCloudDocumentaiV1beta3BatchProcessMetadataIndividualProcessStatus[] $individualProcessStatuses
   */
  public function setIndividualProcessStatuses($individualProcessStatuses)
  {
    $this->individualProcessStatuses = $individualProcessStatuses;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3BatchProcessMetadataIndividualProcessStatus[]
   */
  public function getIndividualProcessStatuses()
  {
    return $this->individualProcessStatuses;
  }
  /**
   * The state of the current batch processing.
   *
   * Accepted values: STATE_UNSPECIFIED, WAITING, RUNNING, SUCCEEDED,
   * CANCELLING, CANCELLED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * A message providing more details about the current state of processing. For
   * example, the error message if the operation is failed.
   *
   * @param string $stateMessage
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
  /**
   * The last update time of the operation.
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
class_alias(GoogleCloudDocumentaiV1beta3BatchProcessMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3BatchProcessMetadata');
