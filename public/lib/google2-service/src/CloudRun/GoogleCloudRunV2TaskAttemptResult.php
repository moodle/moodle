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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2TaskAttemptResult extends \Google\Model
{
  /**
   * Output only. The exit code of this attempt. This may be unset if the
   * container was unable to exit cleanly with a code due to some other failure.
   * See status field for possible failure details. At most one of exit_code or
   * term_signal will be set.
   *
   * @var int
   */
  public $exitCode;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';
  /**
   * Output only. Termination signal of the container. This is set to non-zero
   * if the container is terminated by the system. At most one of exit_code or
   * term_signal will be set.
   *
   * @var int
   */
  public $termSignal;

  /**
   * Output only. The exit code of this attempt. This may be unset if the
   * container was unable to exit cleanly with a code due to some other failure.
   * See status field for possible failure details. At most one of exit_code or
   * term_signal will be set.
   *
   * @param int $exitCode
   */
  public function setExitCode($exitCode)
  {
    $this->exitCode = $exitCode;
  }
  /**
   * @return int
   */
  public function getExitCode()
  {
    return $this->exitCode;
  }
  /**
   * Output only. The status of this attempt. If the status code is OK, then the
   * attempt succeeded.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. Termination signal of the container. This is set to non-zero
   * if the container is terminated by the system. At most one of exit_code or
   * term_signal will be set.
   *
   * @param int $termSignal
   */
  public function setTermSignal($termSignal)
  {
    $this->termSignal = $termSignal;
  }
  /**
   * @return int
   */
  public function getTermSignal()
  {
    return $this->termSignal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2TaskAttemptResult::class, 'Google_Service_CloudRun_GoogleCloudRunV2TaskAttemptResult');
