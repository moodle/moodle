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

namespace Google\Service\ToolResults;

class InconclusiveDetail extends \Google\Model
{
  /**
   * If the end user aborted the test execution before a pass or fail could be
   * determined. For example, the user pressed ctrl-c which sent a kill signal
   * to the test runner while the test was running.
   *
   * @var bool
   */
  public $abortedByUser;
  /**
   * If results are being provided to the user in certain cases of
   * infrastructure failures
   *
   * @var bool
   */
  public $hasErrorLogs;
  /**
   * If the test runner could not determine success or failure because the test
   * depends on a component other than the system under test which failed. For
   * example, a mobile test requires provisioning a device where the test
   * executes, and that provisioning can fail.
   *
   * @var bool
   */
  public $infrastructureFailure;

  /**
   * If the end user aborted the test execution before a pass or fail could be
   * determined. For example, the user pressed ctrl-c which sent a kill signal
   * to the test runner while the test was running.
   *
   * @param bool $abortedByUser
   */
  public function setAbortedByUser($abortedByUser)
  {
    $this->abortedByUser = $abortedByUser;
  }
  /**
   * @return bool
   */
  public function getAbortedByUser()
  {
    return $this->abortedByUser;
  }
  /**
   * If results are being provided to the user in certain cases of
   * infrastructure failures
   *
   * @param bool $hasErrorLogs
   */
  public function setHasErrorLogs($hasErrorLogs)
  {
    $this->hasErrorLogs = $hasErrorLogs;
  }
  /**
   * @return bool
   */
  public function getHasErrorLogs()
  {
    return $this->hasErrorLogs;
  }
  /**
   * If the test runner could not determine success or failure because the test
   * depends on a component other than the system under test which failed. For
   * example, a mobile test requires provisioning a device where the test
   * executes, and that provisioning can fail.
   *
   * @param bool $infrastructureFailure
   */
  public function setInfrastructureFailure($infrastructureFailure)
  {
    $this->infrastructureFailure = $infrastructureFailure;
  }
  /**
   * @return bool
   */
  public function getInfrastructureFailure()
  {
    return $this->infrastructureFailure;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InconclusiveDetail::class, 'Google_Service_ToolResults_InconclusiveDetail');
