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

namespace Google\Service\CloudComposer;

class ComposerWorkloadStatus extends \Google\Model
{
  /**
   * Not able to determine the status of the workload.
   */
  public const STATE_COMPOSER_WORKLOAD_STATE_UNSPECIFIED = 'COMPOSER_WORKLOAD_STATE_UNSPECIFIED';
  /**
   * Workload is in pending state and has not yet started.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Workload is running fine.
   */
  public const STATE_OK = 'OK';
  /**
   * Workload is running but there are some non-critical problems.
   */
  public const STATE_WARNING = 'WARNING';
  /**
   * Workload is not running due to an error.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Workload has finished execution with success.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Workload has finished execution with failure.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. Detailed message of the status.
   *
   * @var string
   */
  public $detailedStatusMessage;
  /**
   * Output only. Workload state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Text to provide more descriptive status.
   *
   * @var string
   */
  public $statusMessage;

  /**
   * Output only. Detailed message of the status.
   *
   * @param string $detailedStatusMessage
   */
  public function setDetailedStatusMessage($detailedStatusMessage)
  {
    $this->detailedStatusMessage = $detailedStatusMessage;
  }
  /**
   * @return string
   */
  public function getDetailedStatusMessage()
  {
    return $this->detailedStatusMessage;
  }
  /**
   * Output only. Workload state.
   *
   * Accepted values: COMPOSER_WORKLOAD_STATE_UNSPECIFIED, PENDING, OK, WARNING,
   * ERROR, SUCCEEDED, FAILED
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
   * Output only. Text to provide more descriptive status.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComposerWorkloadStatus::class, 'Google_Service_CloudComposer_ComposerWorkloadStatus');
