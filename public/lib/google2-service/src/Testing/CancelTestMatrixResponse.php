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

namespace Google\Service\Testing;

class CancelTestMatrixResponse extends \Google\Model
{
  /**
   * Do not use. For proto versioning only.
   */
  public const TEST_STATE_TEST_STATE_UNSPECIFIED = 'TEST_STATE_UNSPECIFIED';
  /**
   * The execution or matrix is being validated.
   */
  public const TEST_STATE_VALIDATING = 'VALIDATING';
  /**
   * The execution or matrix is waiting for resources to become available.
   */
  public const TEST_STATE_PENDING = 'PENDING';
  /**
   * The execution is currently being processed. Can only be set on an
   * execution.
   */
  public const TEST_STATE_RUNNING = 'RUNNING';
  /**
   * The execution or matrix has terminated normally. On a matrix this means
   * that the matrix level processing completed normally, but individual
   * executions may be in an ERROR state.
   */
  public const TEST_STATE_FINISHED = 'FINISHED';
  /**
   * The execution or matrix has stopped because it encountered an
   * infrastructure failure.
   */
  public const TEST_STATE_ERROR = 'ERROR';
  /**
   * The execution was not run because it corresponds to a unsupported
   * environment. Can only be set on an execution.
   */
  public const TEST_STATE_UNSUPPORTED_ENVIRONMENT = 'UNSUPPORTED_ENVIRONMENT';
  /**
   * The execution was not run because the provided inputs are incompatible with
   * the requested environment. Example: requested AndroidVersion is lower than
   * APK's minSdkVersion Can only be set on an execution.
   */
  public const TEST_STATE_INCOMPATIBLE_ENVIRONMENT = 'INCOMPATIBLE_ENVIRONMENT';
  /**
   * The execution was not run because the provided inputs are incompatible with
   * the requested architecture. Example: requested device does not support
   * running the native code in the supplied APK Can only be set on an
   * execution.
   */
  public const TEST_STATE_INCOMPATIBLE_ARCHITECTURE = 'INCOMPATIBLE_ARCHITECTURE';
  /**
   * The user cancelled the execution. Can only be set on an execution.
   */
  public const TEST_STATE_CANCELLED = 'CANCELLED';
  /**
   * The execution or matrix was not run because the provided inputs are not
   * valid. Examples: input file is not of the expected type, is
   * malformed/corrupt, or was flagged as malware
   */
  public const TEST_STATE_INVALID = 'INVALID';
  /**
   * The current rolled-up state of the test matrix. If this state is already
   * final, then the cancelation request will have no effect.
   *
   * @var string
   */
  public $testState;

  /**
   * The current rolled-up state of the test matrix. If this state is already
   * final, then the cancelation request will have no effect.
   *
   * Accepted values: TEST_STATE_UNSPECIFIED, VALIDATING, PENDING, RUNNING,
   * FINISHED, ERROR, UNSUPPORTED_ENVIRONMENT, INCOMPATIBLE_ENVIRONMENT,
   * INCOMPATIBLE_ARCHITECTURE, CANCELLED, INVALID
   *
   * @param self::TEST_STATE_* $testState
   */
  public function setTestState($testState)
  {
    $this->testState = $testState;
  }
  /**
   * @return self::TEST_STATE_*
   */
  public function getTestState()
  {
    return $this->testState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CancelTestMatrixResponse::class, 'Google_Service_Testing_CancelTestMatrixResponse');
