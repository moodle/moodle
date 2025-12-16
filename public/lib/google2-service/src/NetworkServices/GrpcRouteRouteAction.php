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

namespace Google\Service\NetworkServices;

class GrpcRouteRouteAction extends \Google\Collection
{
  protected $collection_key = 'destinations';
  protected $destinationsType = GrpcRouteDestination::class;
  protected $destinationsDataType = 'array';
  protected $faultInjectionPolicyType = GrpcRouteFaultInjectionPolicy::class;
  protected $faultInjectionPolicyDataType = '';
  /**
   * Optional. Specifies the idle timeout for the selected route. The idle
   * timeout is defined as the period in which there are no bytes sent or
   * received on either the upstream or downstream connection. If not set, the
   * default idle timeout is 1 hour. If set to 0s, the timeout will be disabled.
   *
   * @var string
   */
  public $idleTimeout;
  protected $retryPolicyType = GrpcRouteRetryPolicy::class;
  protected $retryPolicyDataType = '';
  protected $statefulSessionAffinityType = GrpcRouteStatefulSessionAffinityPolicy::class;
  protected $statefulSessionAffinityDataType = '';
  /**
   * Optional. Specifies the timeout for selected route. Timeout is computed
   * from the time the request has been fully processed (i.e. end of stream) up
   * until the response has been completely processed. Timeout includes all
   * retries.
   *
   * @var string
   */
  public $timeout;

  /**
   * Optional. The destination services to which traffic should be forwarded. If
   * multiple destinations are specified, traffic will be split between Backend
   * Service(s) according to the weight field of these destinations.
   *
   * @param GrpcRouteDestination[] $destinations
   */
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  /**
   * @return GrpcRouteDestination[]
   */
  public function getDestinations()
  {
    return $this->destinations;
  }
  /**
   * Optional. The specification for fault injection introduced into traffic to
   * test the resiliency of clients to destination service failure. As part of
   * fault injection, when clients send requests to a destination, delays can be
   * introduced on a percentage of requests before sending those requests to the
   * destination service. Similarly requests from clients can be aborted by for
   * a percentage of requests. timeout and retry_policy will be ignored by
   * clients that are configured with a fault_injection_policy
   *
   * @param GrpcRouteFaultInjectionPolicy $faultInjectionPolicy
   */
  public function setFaultInjectionPolicy(GrpcRouteFaultInjectionPolicy $faultInjectionPolicy)
  {
    $this->faultInjectionPolicy = $faultInjectionPolicy;
  }
  /**
   * @return GrpcRouteFaultInjectionPolicy
   */
  public function getFaultInjectionPolicy()
  {
    return $this->faultInjectionPolicy;
  }
  /**
   * Optional. Specifies the idle timeout for the selected route. The idle
   * timeout is defined as the period in which there are no bytes sent or
   * received on either the upstream or downstream connection. If not set, the
   * default idle timeout is 1 hour. If set to 0s, the timeout will be disabled.
   *
   * @param string $idleTimeout
   */
  public function setIdleTimeout($idleTimeout)
  {
    $this->idleTimeout = $idleTimeout;
  }
  /**
   * @return string
   */
  public function getIdleTimeout()
  {
    return $this->idleTimeout;
  }
  /**
   * Optional. Specifies the retry policy associated with this route.
   *
   * @param GrpcRouteRetryPolicy $retryPolicy
   */
  public function setRetryPolicy(GrpcRouteRetryPolicy $retryPolicy)
  {
    $this->retryPolicy = $retryPolicy;
  }
  /**
   * @return GrpcRouteRetryPolicy
   */
  public function getRetryPolicy()
  {
    return $this->retryPolicy;
  }
  /**
   * Optional. Specifies cookie-based stateful session affinity.
   *
   * @param GrpcRouteStatefulSessionAffinityPolicy $statefulSessionAffinity
   */
  public function setStatefulSessionAffinity(GrpcRouteStatefulSessionAffinityPolicy $statefulSessionAffinity)
  {
    $this->statefulSessionAffinity = $statefulSessionAffinity;
  }
  /**
   * @return GrpcRouteStatefulSessionAffinityPolicy
   */
  public function getStatefulSessionAffinity()
  {
    return $this->statefulSessionAffinity;
  }
  /**
   * Optional. Specifies the timeout for selected route. Timeout is computed
   * from the time the request has been fully processed (i.e. end of stream) up
   * until the response has been completely processed. Timeout includes all
   * retries.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GrpcRouteRouteAction::class, 'Google_Service_NetworkServices_GrpcRouteRouteAction');
