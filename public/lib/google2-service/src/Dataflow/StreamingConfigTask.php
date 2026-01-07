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

namespace Google\Service\Dataflow;

class StreamingConfigTask extends \Google\Collection
{
  protected $collection_key = 'streamingComputationConfigs';
  /**
   * Chunk size for commit streams from the harness to windmill.
   *
   * @var string
   */
  public $commitStreamChunkSizeBytes;
  /**
   * Chunk size for get data streams from the harness to windmill.
   *
   * @var string
   */
  public $getDataStreamChunkSizeBytes;
  /**
   * Maximum size for work item commit supported windmill storage layer.
   *
   * @var string
   */
  public $maxWorkItemCommitBytes;
  protected $operationalLimitsType = StreamingOperationalLimits::class;
  protected $operationalLimitsDataType = '';
  protected $streamingComputationConfigsType = StreamingComputationConfig::class;
  protected $streamingComputationConfigsDataType = 'array';
  /**
   * Map from user step names to state families.
   *
   * @var string[]
   */
  public $userStepToStateFamilyNameMap;
  /**
   * Binary encoded proto to control runtime behavior of the java runner v1 user
   * worker.
   *
   * @var string
   */
  public $userWorkerRunnerV1Settings;
  /**
   * Binary encoded proto to control runtime behavior of the runner v2 user
   * worker.
   *
   * @var string
   */
  public $userWorkerRunnerV2Settings;
  /**
   * If present, the worker must use this endpoint to communicate with Windmill
   * Service dispatchers, otherwise the worker must continue to use whatever
   * endpoint it had been using.
   *
   * @var string
   */
  public $windmillServiceEndpoint;
  /**
   * If present, the worker must use this port to communicate with Windmill
   * Service dispatchers. Only applicable when windmill_service_endpoint is
   * specified.
   *
   * @var string
   */
  public $windmillServicePort;

  /**
   * Chunk size for commit streams from the harness to windmill.
   *
   * @param string $commitStreamChunkSizeBytes
   */
  public function setCommitStreamChunkSizeBytes($commitStreamChunkSizeBytes)
  {
    $this->commitStreamChunkSizeBytes = $commitStreamChunkSizeBytes;
  }
  /**
   * @return string
   */
  public function getCommitStreamChunkSizeBytes()
  {
    return $this->commitStreamChunkSizeBytes;
  }
  /**
   * Chunk size for get data streams from the harness to windmill.
   *
   * @param string $getDataStreamChunkSizeBytes
   */
  public function setGetDataStreamChunkSizeBytes($getDataStreamChunkSizeBytes)
  {
    $this->getDataStreamChunkSizeBytes = $getDataStreamChunkSizeBytes;
  }
  /**
   * @return string
   */
  public function getGetDataStreamChunkSizeBytes()
  {
    return $this->getDataStreamChunkSizeBytes;
  }
  /**
   * Maximum size for work item commit supported windmill storage layer.
   *
   * @param string $maxWorkItemCommitBytes
   */
  public function setMaxWorkItemCommitBytes($maxWorkItemCommitBytes)
  {
    $this->maxWorkItemCommitBytes = $maxWorkItemCommitBytes;
  }
  /**
   * @return string
   */
  public function getMaxWorkItemCommitBytes()
  {
    return $this->maxWorkItemCommitBytes;
  }
  /**
   * Operational limits for the streaming job. Can be used by the worker to
   * validate outputs sent to the backend.
   *
   * @param StreamingOperationalLimits $operationalLimits
   */
  public function setOperationalLimits(StreamingOperationalLimits $operationalLimits)
  {
    $this->operationalLimits = $operationalLimits;
  }
  /**
   * @return StreamingOperationalLimits
   */
  public function getOperationalLimits()
  {
    return $this->operationalLimits;
  }
  /**
   * Set of computation configuration information.
   *
   * @param StreamingComputationConfig[] $streamingComputationConfigs
   */
  public function setStreamingComputationConfigs($streamingComputationConfigs)
  {
    $this->streamingComputationConfigs = $streamingComputationConfigs;
  }
  /**
   * @return StreamingComputationConfig[]
   */
  public function getStreamingComputationConfigs()
  {
    return $this->streamingComputationConfigs;
  }
  /**
   * Map from user step names to state families.
   *
   * @param string[] $userStepToStateFamilyNameMap
   */
  public function setUserStepToStateFamilyNameMap($userStepToStateFamilyNameMap)
  {
    $this->userStepToStateFamilyNameMap = $userStepToStateFamilyNameMap;
  }
  /**
   * @return string[]
   */
  public function getUserStepToStateFamilyNameMap()
  {
    return $this->userStepToStateFamilyNameMap;
  }
  /**
   * Binary encoded proto to control runtime behavior of the java runner v1 user
   * worker.
   *
   * @param string $userWorkerRunnerV1Settings
   */
  public function setUserWorkerRunnerV1Settings($userWorkerRunnerV1Settings)
  {
    $this->userWorkerRunnerV1Settings = $userWorkerRunnerV1Settings;
  }
  /**
   * @return string
   */
  public function getUserWorkerRunnerV1Settings()
  {
    return $this->userWorkerRunnerV1Settings;
  }
  /**
   * Binary encoded proto to control runtime behavior of the runner v2 user
   * worker.
   *
   * @param string $userWorkerRunnerV2Settings
   */
  public function setUserWorkerRunnerV2Settings($userWorkerRunnerV2Settings)
  {
    $this->userWorkerRunnerV2Settings = $userWorkerRunnerV2Settings;
  }
  /**
   * @return string
   */
  public function getUserWorkerRunnerV2Settings()
  {
    return $this->userWorkerRunnerV2Settings;
  }
  /**
   * If present, the worker must use this endpoint to communicate with Windmill
   * Service dispatchers, otherwise the worker must continue to use whatever
   * endpoint it had been using.
   *
   * @param string $windmillServiceEndpoint
   */
  public function setWindmillServiceEndpoint($windmillServiceEndpoint)
  {
    $this->windmillServiceEndpoint = $windmillServiceEndpoint;
  }
  /**
   * @return string
   */
  public function getWindmillServiceEndpoint()
  {
    return $this->windmillServiceEndpoint;
  }
  /**
   * If present, the worker must use this port to communicate with Windmill
   * Service dispatchers. Only applicable when windmill_service_endpoint is
   * specified.
   *
   * @param string $windmillServicePort
   */
  public function setWindmillServicePort($windmillServicePort)
  {
    $this->windmillServicePort = $windmillServicePort;
  }
  /**
   * @return string
   */
  public function getWindmillServicePort()
  {
    return $this->windmillServicePort;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamingConfigTask::class, 'Google_Service_Dataflow_StreamingConfigTask');
