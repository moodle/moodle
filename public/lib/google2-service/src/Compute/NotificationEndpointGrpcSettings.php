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

namespace Google\Service\Compute;

class NotificationEndpointGrpcSettings extends \Google\Model
{
  /**
   * Optional. If specified, this field is used to set the authority header by
   * the sender of notifications. See
   * https://tools.ietf.org/html/rfc7540#section-8.1.2.3
   *
   * @var string
   */
  public $authority;
  /**
   * Endpoint to which gRPC notifications are sent. This must be a valid gRPCLB
   * DNS name.
   *
   * @var string
   */
  public $endpoint;
  /**
   * Optional. If specified, this field is used to populate the "name" field in
   * gRPC requests.
   *
   * @var string
   */
  public $payloadName;
  protected $resendIntervalType = Duration::class;
  protected $resendIntervalDataType = '';
  /**
   * How much time (in seconds) is spent attempting notification retries until a
   * successful response is received. Default is 30s. Limit is 20m (1200s). Must
   * be a positive number.
   *
   * @var string
   */
  public $retryDurationSec;

  /**
   * Optional. If specified, this field is used to set the authority header by
   * the sender of notifications. See
   * https://tools.ietf.org/html/rfc7540#section-8.1.2.3
   *
   * @param string $authority
   */
  public function setAuthority($authority)
  {
    $this->authority = $authority;
  }
  /**
   * @return string
   */
  public function getAuthority()
  {
    return $this->authority;
  }
  /**
   * Endpoint to which gRPC notifications are sent. This must be a valid gRPCLB
   * DNS name.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Optional. If specified, this field is used to populate the "name" field in
   * gRPC requests.
   *
   * @param string $payloadName
   */
  public function setPayloadName($payloadName)
  {
    $this->payloadName = $payloadName;
  }
  /**
   * @return string
   */
  public function getPayloadName()
  {
    return $this->payloadName;
  }
  /**
   * Optional. This field is used to configure how often to send a full update
   * of all non-healthy backends. If unspecified, full updates are not sent. If
   * specified, must be in the range between 600 seconds to 3600 seconds. Nanos
   * are disallowed. Can only be set for regional notification endpoints.
   *
   * @param Duration $resendInterval
   */
  public function setResendInterval(Duration $resendInterval)
  {
    $this->resendInterval = $resendInterval;
  }
  /**
   * @return Duration
   */
  public function getResendInterval()
  {
    return $this->resendInterval;
  }
  /**
   * How much time (in seconds) is spent attempting notification retries until a
   * successful response is received. Default is 30s. Limit is 20m (1200s). Must
   * be a positive number.
   *
   * @param string $retryDurationSec
   */
  public function setRetryDurationSec($retryDurationSec)
  {
    $this->retryDurationSec = $retryDurationSec;
  }
  /**
   * @return string
   */
  public function getRetryDurationSec()
  {
    return $this->retryDurationSec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationEndpointGrpcSettings::class, 'Google_Service_Compute_NotificationEndpointGrpcSettings');
