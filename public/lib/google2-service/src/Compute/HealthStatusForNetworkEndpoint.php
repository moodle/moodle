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

class HealthStatusForNetworkEndpoint extends \Google\Model
{
  /**
   * Endpoint is being drained.
   */
  public const HEALTH_STATE_DRAINING = 'DRAINING';
  /**
   * Endpoint is healthy.
   */
  public const HEALTH_STATE_HEALTHY = 'HEALTHY';
  /**
   * Endpoint is unhealthy.
   */
  public const HEALTH_STATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * Health status of the endpoint is unknown.
   */
  public const HEALTH_STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Endpoint is being drained.
   */
  public const IPV6_HEALTH_STATE_DRAINING = 'DRAINING';
  /**
   * Endpoint is healthy.
   */
  public const IPV6_HEALTH_STATE_HEALTHY = 'HEALTHY';
  /**
   * Endpoint is unhealthy.
   */
  public const IPV6_HEALTH_STATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * Health status of the endpoint is unknown.
   */
  public const IPV6_HEALTH_STATE_UNKNOWN = 'UNKNOWN';
  protected $backendServiceType = BackendServiceReference::class;
  protected $backendServiceDataType = '';
  protected $forwardingRuleType = ForwardingRuleReference::class;
  protected $forwardingRuleDataType = '';
  protected $healthCheckType = HealthCheckReference::class;
  protected $healthCheckDataType = '';
  protected $healthCheckServiceType = HealthCheckServiceReference::class;
  protected $healthCheckServiceDataType = '';
  /**
   * Health state of the network endpoint determined based on the health checks
   * configured.
   *
   * @var string
   */
  public $healthState;
  /**
   * Health state of the ipv6 network endpoint determined based on the health
   * checks configured.
   *
   * @var string
   */
  public $ipv6HealthState;

  /**
   * URL of the backend service associated with the health state of the network
   * endpoint.
   *
   * @param BackendServiceReference $backendService
   */
  public function setBackendService(BackendServiceReference $backendService)
  {
    $this->backendService = $backendService;
  }
  /**
   * @return BackendServiceReference
   */
  public function getBackendService()
  {
    return $this->backendService;
  }
  /**
   * URL of the forwarding rule associated with the health state of the network
   * endpoint.
   *
   * @param ForwardingRuleReference $forwardingRule
   */
  public function setForwardingRule(ForwardingRuleReference $forwardingRule)
  {
    $this->forwardingRule = $forwardingRule;
  }
  /**
   * @return ForwardingRuleReference
   */
  public function getForwardingRule()
  {
    return $this->forwardingRule;
  }
  /**
   * URL of the health check associated with the health state of the network
   * endpoint.
   *
   * @param HealthCheckReference $healthCheck
   */
  public function setHealthCheck(HealthCheckReference $healthCheck)
  {
    $this->healthCheck = $healthCheck;
  }
  /**
   * @return HealthCheckReference
   */
  public function getHealthCheck()
  {
    return $this->healthCheck;
  }
  /**
   * URL of the health check service associated with the health state of the
   * network endpoint.
   *
   * @param HealthCheckServiceReference $healthCheckService
   */
  public function setHealthCheckService(HealthCheckServiceReference $healthCheckService)
  {
    $this->healthCheckService = $healthCheckService;
  }
  /**
   * @return HealthCheckServiceReference
   */
  public function getHealthCheckService()
  {
    return $this->healthCheckService;
  }
  /**
   * Health state of the network endpoint determined based on the health checks
   * configured.
   *
   * Accepted values: DRAINING, HEALTHY, UNHEALTHY, UNKNOWN
   *
   * @param self::HEALTH_STATE_* $healthState
   */
  public function setHealthState($healthState)
  {
    $this->healthState = $healthState;
  }
  /**
   * @return self::HEALTH_STATE_*
   */
  public function getHealthState()
  {
    return $this->healthState;
  }
  /**
   * Health state of the ipv6 network endpoint determined based on the health
   * checks configured.
   *
   * Accepted values: DRAINING, HEALTHY, UNHEALTHY, UNKNOWN
   *
   * @param self::IPV6_HEALTH_STATE_* $ipv6HealthState
   */
  public function setIpv6HealthState($ipv6HealthState)
  {
    $this->ipv6HealthState = $ipv6HealthState;
  }
  /**
   * @return self::IPV6_HEALTH_STATE_*
   */
  public function getIpv6HealthState()
  {
    return $this->ipv6HealthState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HealthStatusForNetworkEndpoint::class, 'Google_Service_Compute_HealthStatusForNetworkEndpoint');
