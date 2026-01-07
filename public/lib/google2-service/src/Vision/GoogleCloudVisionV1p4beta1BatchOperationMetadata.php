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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p4beta1BatchOperationMetadata extends \Google\Model
{
  /**
   * Invalid.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Request is actively being processed.
   */
  public const STATE_PROCESSING = 'PROCESSING';
  /**
   * The request is done and at least one item has been successfully processed.
   */
  public const STATE_SUCCESSFUL = 'SUCCESSFUL';
  /**
   * The request is done and no item has been successfully processed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The request is done after the longrunning.Operations.CancelOperation has
   * been called by the user. Any records that were processed before the cancel
   * command are output as specified in the request.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The time when the batch request is finished and
   * google.longrunning.Operation.done is set to true.
   *
   * @var string
   */
  public $endTime;
  /**
   * The current state of the batch operation.
   *
   * @var string
   */
  public $state;
  /**
   * The time when the batch request was submitted to the server.
   *
   * @var string
   */
  public $submitTime;

  /**
   * The time when the batch request is finished and
   * google.longrunning.Operation.done is set to true.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The current state of the batch operation.
   *
   * Accepted values: STATE_UNSPECIFIED, PROCESSING, SUCCESSFUL, FAILED,
   * CANCELLED
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
   * The time when the batch request was submitted to the server.
   *
   * @param string $submitTime
   */
  public function setSubmitTime($submitTime)
  {
    $this->submitTime = $submitTime;
  }
  /**
   * @return string
   */
  public function getSubmitTime()
  {
    return $this->submitTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p4beta1BatchOperationMetadata::class, 'Google_Service_Vision_GoogleCloudVisionV1p4beta1BatchOperationMetadata');
