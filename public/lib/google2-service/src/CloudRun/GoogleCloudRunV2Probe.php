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

class GoogleCloudRunV2Probe extends \Google\Model
{
  /**
   * Optional. Minimum consecutive failures for the probe to be considered
   * failed after having succeeded. Defaults to 3. Minimum value is 1.
   *
   * @var int
   */
  public $failureThreshold;
  protected $grpcType = GoogleCloudRunV2GRPCAction::class;
  protected $grpcDataType = '';
  protected $httpGetType = GoogleCloudRunV2HTTPGetAction::class;
  protected $httpGetDataType = '';
  /**
   * Optional. Number of seconds after the container has started before the
   * probe is initiated. Defaults to 0 seconds. Minimum value is 0. Maximum
   * value for liveness probe is 3600. Maximum value for startup probe is 240.
   *
   * @var int
   */
  public $initialDelaySeconds;
  /**
   * Optional. How often (in seconds) to perform the probe. Default to 10
   * seconds. Minimum value is 1. Maximum value for liveness probe is 3600.
   * Maximum value for startup probe is 240. Must be greater or equal than
   * timeout_seconds.
   *
   * @var int
   */
  public $periodSeconds;
  protected $tcpSocketType = GoogleCloudRunV2TCPSocketAction::class;
  protected $tcpSocketDataType = '';
  /**
   * Optional. Number of seconds after which the probe times out. Defaults to 1
   * second. Minimum value is 1. Maximum value is 3600. Must be smaller than
   * period_seconds.
   *
   * @var int
   */
  public $timeoutSeconds;

  /**
   * Optional. Minimum consecutive failures for the probe to be considered
   * failed after having succeeded. Defaults to 3. Minimum value is 1.
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
   * Optional. GRPC specifies an action involving a gRPC port. Exactly one of
   * httpGet, tcpSocket, or grpc must be specified.
   *
   * @param GoogleCloudRunV2GRPCAction $grpc
   */
  public function setGrpc(GoogleCloudRunV2GRPCAction $grpc)
  {
    $this->grpc = $grpc;
  }
  /**
   * @return GoogleCloudRunV2GRPCAction
   */
  public function getGrpc()
  {
    return $this->grpc;
  }
  /**
   * Optional. HTTPGet specifies the http request to perform. Exactly one of
   * httpGet, tcpSocket, or grpc must be specified.
   *
   * @param GoogleCloudRunV2HTTPGetAction $httpGet
   */
  public function setHttpGet(GoogleCloudRunV2HTTPGetAction $httpGet)
  {
    $this->httpGet = $httpGet;
  }
  /**
   * @return GoogleCloudRunV2HTTPGetAction
   */
  public function getHttpGet()
  {
    return $this->httpGet;
  }
  /**
   * Optional. Number of seconds after the container has started before the
   * probe is initiated. Defaults to 0 seconds. Minimum value is 0. Maximum
   * value for liveness probe is 3600. Maximum value for startup probe is 240.
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
   * Optional. How often (in seconds) to perform the probe. Default to 10
   * seconds. Minimum value is 1. Maximum value for liveness probe is 3600.
   * Maximum value for startup probe is 240. Must be greater or equal than
   * timeout_seconds.
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
   * Optional. TCPSocket specifies an action involving a TCP port. Exactly one
   * of httpGet, tcpSocket, or grpc must be specified.
   *
   * @param GoogleCloudRunV2TCPSocketAction $tcpSocket
   */
  public function setTcpSocket(GoogleCloudRunV2TCPSocketAction $tcpSocket)
  {
    $this->tcpSocket = $tcpSocket;
  }
  /**
   * @return GoogleCloudRunV2TCPSocketAction
   */
  public function getTcpSocket()
  {
    return $this->tcpSocket;
  }
  /**
   * Optional. Number of seconds after which the probe times out. Defaults to 1
   * second. Minimum value is 1. Maximum value is 3600. Must be smaller than
   * period_seconds.
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
class_alias(GoogleCloudRunV2Probe::class, 'Google_Service_CloudRun_GoogleCloudRunV2Probe');
