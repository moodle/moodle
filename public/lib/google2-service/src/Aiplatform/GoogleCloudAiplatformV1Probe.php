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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Probe extends \Google\Model
{
  protected $execType = GoogleCloudAiplatformV1ProbeExecAction::class;
  protected $execDataType = '';
  /**
   * Number of consecutive failures before the probe is considered failed.
   * Defaults to 3. Minimum value is 1. Maps to Kubernetes probe argument
   * 'failureThreshold'.
   *
   * @var int
   */
  public $failureThreshold;
  protected $grpcType = GoogleCloudAiplatformV1ProbeGrpcAction::class;
  protected $grpcDataType = '';
  protected $httpGetType = GoogleCloudAiplatformV1ProbeHttpGetAction::class;
  protected $httpGetDataType = '';
  /**
   * Number of seconds to wait before starting the probe. Defaults to 0. Minimum
   * value is 0. Maps to Kubernetes probe argument 'initialDelaySeconds'.
   *
   * @var int
   */
  public $initialDelaySeconds;
  /**
   * How often (in seconds) to perform the probe. Default to 10 seconds. Minimum
   * value is 1. Must be less than timeout_seconds. Maps to Kubernetes probe
   * argument 'periodSeconds'.
   *
   * @var int
   */
  public $periodSeconds;
  /**
   * Number of consecutive successes before the probe is considered successful.
   * Defaults to 1. Minimum value is 1. Maps to Kubernetes probe argument
   * 'successThreshold'.
   *
   * @var int
   */
  public $successThreshold;
  protected $tcpSocketType = GoogleCloudAiplatformV1ProbeTcpSocketAction::class;
  protected $tcpSocketDataType = '';
  /**
   * Number of seconds after which the probe times out. Defaults to 1 second.
   * Minimum value is 1. Must be greater or equal to period_seconds. Maps to
   * Kubernetes probe argument 'timeoutSeconds'.
   *
   * @var int
   */
  public $timeoutSeconds;

  /**
   * ExecAction probes the health of a container by executing a command.
   *
   * @param GoogleCloudAiplatformV1ProbeExecAction $exec
   */
  public function setExec(GoogleCloudAiplatformV1ProbeExecAction $exec)
  {
    $this->exec = $exec;
  }
  /**
   * @return GoogleCloudAiplatformV1ProbeExecAction
   */
  public function getExec()
  {
    return $this->exec;
  }
  /**
   * Number of consecutive failures before the probe is considered failed.
   * Defaults to 3. Minimum value is 1. Maps to Kubernetes probe argument
   * 'failureThreshold'.
   *
   * @param int $failureThreshold
   */
  public function setFailureThreshold($failureThreshold)
  {
    $this->failureThreshold = $failureThreshold;
  }
  /**
   * @return int
   */
  public function getFailureThreshold()
  {
    return $this->failureThreshold;
  }
  /**
   * GrpcAction probes the health of a container by sending a gRPC request.
   *
   * @param GoogleCloudAiplatformV1ProbeGrpcAction $grpc
   */
  public function setGrpc(GoogleCloudAiplatformV1ProbeGrpcAction $grpc)
  {
    $this->grpc = $grpc;
  }
  /**
   * @return GoogleCloudAiplatformV1ProbeGrpcAction
   */
  public function getGrpc()
  {
    return $this->grpc;
  }
  /**
   * HttpGetAction probes the health of a container by sending an HTTP GET
   * request.
   *
   * @param GoogleCloudAiplatformV1ProbeHttpGetAction $httpGet
   */
  public function setHttpGet(GoogleCloudAiplatformV1ProbeHttpGetAction $httpGet)
  {
    $this->httpGet = $httpGet;
  }
  /**
   * @return GoogleCloudAiplatformV1ProbeHttpGetAction
   */
  public function getHttpGet()
  {
    return $this->httpGet;
  }
  /**
   * Number of seconds to wait before starting the probe. Defaults to 0. Minimum
   * value is 0. Maps to Kubernetes probe argument 'initialDelaySeconds'.
   *
   * @param int $initialDelaySeconds
   */
  public function setInitialDelaySeconds($initialDelaySeconds)
  {
    $this->initialDelaySeconds = $initialDelaySeconds;
  }
  /**
   * @return int
   */
  public function getInitialDelaySeconds()
  {
    return $this->initialDelaySeconds;
  }
  /**
   * How often (in seconds) to perform the probe. Default to 10 seconds. Minimum
   * value is 1. Must be less than timeout_seconds. Maps to Kubernetes probe
   * argument 'periodSeconds'.
   *
   * @param int $periodSeconds
   */
  public function setPeriodSeconds($periodSeconds)
  {
    $this->periodSeconds = $periodSeconds;
  }
  /**
   * @return int
   */
  public function getPeriodSeconds()
  {
    return $this->periodSeconds;
  }
  /**
   * Number of consecutive successes before the probe is considered successful.
   * Defaults to 1. Minimum value is 1. Maps to Kubernetes probe argument
   * 'successThreshold'.
   *
   * @param int $successThreshold
   */
  public function setSuccessThreshold($successThreshold)
  {
    $this->successThreshold = $successThreshold;
  }
  /**
   * @return int
   */
  public function getSuccessThreshold()
  {
    return $this->successThreshold;
  }
  /**
   * TcpSocketAction probes the health of a container by opening a TCP socket
   * connection.
   *
   * @param GoogleCloudAiplatformV1ProbeTcpSocketAction $tcpSocket
   */
  public function setTcpSocket(GoogleCloudAiplatformV1ProbeTcpSocketAction $tcpSocket)
  {
    $this->tcpSocket = $tcpSocket;
  }
  /**
   * @return GoogleCloudAiplatformV1ProbeTcpSocketAction
   */
  public function getTcpSocket()
  {
    return $this->tcpSocket;
  }
  /**
   * Number of seconds after which the probe times out. Defaults to 1 second.
   * Minimum value is 1. Must be greater or equal to period_seconds. Maps to
   * Kubernetes probe argument 'timeoutSeconds'.
   *
   * @param int $timeoutSeconds
   */
  public function setTimeoutSeconds($timeoutSeconds)
  {
    $this->timeoutSeconds = $timeoutSeconds;
  }
  /**
   * @return int
   */
  public function getTimeoutSeconds()
  {
    return $this->timeoutSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Probe::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Probe');
