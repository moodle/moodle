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

class TestExecution extends \Google\Model
{
  /**
   * Do not use. For proto versioning only.
   */
  public const STATE_TEST_STATE_UNSPECIFIED = 'TEST_STATE_UNSPECIFIED';
  /**
   * The execution or matrix is being validated.
   */
  public const STATE_VALIDATING = 'VALIDATING';
  /**
   * The execution or matrix is waiting for resources to become available.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The execution is currently being processed. Can only be set on an
   * execution.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The execution or matrix has terminated normally. On a matrix this means
   * that the matrix level processing completed normally, but individual
   * executions may be in an ERROR state.
   */
  public const STATE_FINISHED = 'FINISHED';
  /**
   * The execution or matrix has stopped because it encountered an
   * infrastructure failure.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The execution was not run because it corresponds to a unsupported
   * environment. Can only be set on an execution.
   */
  public const STATE_UNSUPPORTED_ENVIRONMENT = 'UNSUPPORTED_ENVIRONMENT';
  /**
   * The execution was not run because the provided inputs are incompatible with
   * the requested environment. Example: requested AndroidVersion is lower than
   * APK's minSdkVersion Can only be set on an execution.
   */
  public const STATE_INCOMPATIBLE_ENVIRONMENT = 'INCOMPATIBLE_ENVIRONMENT';
  /**
   * The execution was not run because the provided inputs are incompatible with
   * the requested architecture. Example: requested device does not support
   * running the native code in the supplied APK Can only be set on an
   * execution.
   */
  public const STATE_INCOMPATIBLE_ARCHITECTURE = 'INCOMPATIBLE_ARCHITECTURE';
  /**
   * The user cancelled the execution. Can only be set on an execution.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The execution or matrix was not run because the provided inputs are not
   * valid. Examples: input file is not of the expected type, is
   * malformed/corrupt, or was flagged as malware
   */
  public const STATE_INVALID = 'INVALID';
  protected $environmentType = Environment::class;
  protected $environmentDataType = '';
  /**
   * Output only. Unique id set by the service.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Id of the containing TestMatrix.
   *
   * @var string
   */
  public $matrixId;
  /**
   * Output only. The cloud project that owns the test execution.
   *
   * @var string
   */
  public $projectId;
  protected $shardType = Shard::class;
  protected $shardDataType = '';
  /**
   * Output only. Indicates the current progress of the test execution (e.g.,
   * FINISHED).
   *
   * @var string
   */
  public $state;
  protected $testDetailsType = TestDetails::class;
  protected $testDetailsDataType = '';
  protected $testSpecificationType = TestSpecification::class;
  protected $testSpecificationDataType = '';
  /**
   * Output only. The time this test execution was initially created.
   *
   * @var string
   */
  public $timestamp;
  protected $toolResultsStepType = ToolResultsStep::class;
  protected $toolResultsStepDataType = '';

  /**
   * Output only. How the host machine(s) are configured.
   *
   * @param Environment $environment
   */
  public function setEnvironment(Environment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return Environment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Output only. Unique id set by the service.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Id of the containing TestMatrix.
   *
   * @param string $matrixId
   */
  public function setMatrixId($matrixId)
  {
    $this->matrixId = $matrixId;
  }
  /**
   * @return string
   */
  public function getMatrixId()
  {
    return $this->matrixId;
  }
  /**
   * Output only. The cloud project that owns the test execution.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Output only. Details about the shard.
   *
   * @param Shard $shard
   */
  public function setShard(Shard $shard)
  {
    $this->shard = $shard;
  }
  /**
   * @return Shard
   */
  public function getShard()
  {
    return $this->shard;
  }
  /**
   * Output only. Indicates the current progress of the test execution (e.g.,
   * FINISHED).
   *
   * Accepted values: TEST_STATE_UNSPECIFIED, VALIDATING, PENDING, RUNNING,
   * FINISHED, ERROR, UNSUPPORTED_ENVIRONMENT, INCOMPATIBLE_ENVIRONMENT,
   * INCOMPATIBLE_ARCHITECTURE, CANCELLED, INVALID
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
   * Output only. Additional details about the running test.
   *
   * @param TestDetails $testDetails
   */
  public function setTestDetails(TestDetails $testDetails)
  {
    $this->testDetails = $testDetails;
  }
  /**
   * @return TestDetails
   */
  public function getTestDetails()
  {
    return $this->testDetails;
  }
  /**
   * Output only. How to run the test.
   *
   * @param TestSpecification $testSpecification
   */
  public function setTestSpecification(TestSpecification $testSpecification)
  {
    $this->testSpecification = $testSpecification;
  }
  /**
   * @return TestSpecification
   */
  public function getTestSpecification()
  {
    return $this->testSpecification;
  }
  /**
   * Output only. The time this test execution was initially created.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
  /**
   * Output only. Where the results for this execution are written.
   *
   * @param ToolResultsStep $toolResultsStep
   */
  public function setToolResultsStep(ToolResultsStep $toolResultsStep)
  {
    $this->toolResultsStep = $toolResultsStep;
  }
  /**
   * @return ToolResultsStep
   */
  public function getToolResultsStep()
  {
    return $this->toolResultsStep;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestExecution::class, 'Google_Service_Testing_TestExecution');
