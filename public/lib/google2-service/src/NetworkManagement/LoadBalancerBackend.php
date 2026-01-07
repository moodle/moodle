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

namespace Google\Service\NetworkManagement;

class LoadBalancerBackend extends \Google\Collection
{
  /**
   * State is unspecified. Default state if not populated.
   */
  public const HEALTH_CHECK_FIREWALL_STATE_HEALTH_CHECK_FIREWALL_STATE_UNSPECIFIED = 'HEALTH_CHECK_FIREWALL_STATE_UNSPECIFIED';
  /**
   * There are configured firewall rules to allow health check probes to the
   * backend.
   */
  public const HEALTH_CHECK_FIREWALL_STATE_CONFIGURED = 'CONFIGURED';
  /**
   * There are firewall rules configured to allow partial health check ranges or
   * block all health check ranges. If a health check probe is sent from denied
   * IP ranges, the health check to the backend will fail. Then, the backend
   * will be marked unhealthy and will not receive traffic sent to the load
   * balancer.
   */
  public const HEALTH_CHECK_FIREWALL_STATE_MISCONFIGURED = 'MISCONFIGURED';
  protected $collection_key = 'healthCheckBlockingFirewallRules';
  /**
   * Name of a Compute Engine instance or network endpoint.
   *
   * @var string
   */
  public $displayName;
  /**
   * A list of firewall rule URIs allowing probes from health check IP ranges.
   *
   * @var string[]
   */
  public $healthCheckAllowingFirewallRules;
  /**
   * A list of firewall rule URIs blocking probes from health check IP ranges.
   *
   * @var string[]
   */
  public $healthCheckBlockingFirewallRules;
  /**
   * State of the health check firewall configuration.
   *
   * @var string
   */
  public $healthCheckFirewallState;
  /**
   * URI of a Compute Engine instance or network endpoint.
   *
   * @var string
   */
  public $uri;

  /**
   * Name of a Compute Engine instance or network endpoint.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * A list of firewall rule URIs allowing probes from health check IP ranges.
   *
   * @param string[] $healthCheckAllowingFirewallRules
   */
  public function setHealthCheckAllowingFirewallRules($healthCheckAllowingFirewallRules)
  {
    $this->healthCheckAllowingFirewallRules = $healthCheckAllowingFirewallRules;
  }
  /**
   * @return string[]
   */
  public function getHealthCheckAllowingFirewallRules()
  {
    return $this->healthCheckAllowingFirewallRules;
  }
  /**
   * A list of firewall rule URIs blocking probes from health check IP ranges.
   *
   * @param string[] $healthCheckBlockingFirewallRules
   */
  public function setHealthCheckBlockingFirewallRules($healthCheckBlockingFirewallRules)
  {
    $this->healthCheckBlockingFirewallRules = $healthCheckBlockingFirewallRules;
  }
  /**
   * @return string[]
   */
  public function getHealthCheckBlockingFirewallRules()
  {
    return $this->healthCheckBlockingFirewallRules;
  }
  /**
   * State of the health check firewall configuration.
   *
   * Accepted values: HEALTH_CHECK_FIREWALL_STATE_UNSPECIFIED, CONFIGURED,
   * MISCONFIGURED
   *
   * @param self::HEALTH_CHECK_FIREWALL_STATE_* $healthCheckFirewallState
   */
  public function setHealthCheckFirewallState($healthCheckFirewallState)
  {
    $this->healthCheckFirewallState = $healthCheckFirewallState;
  }
  /**
   * @return self::HEALTH_CHECK_FIREWALL_STATE_*
   */
  public function getHealthCheckFirewallState()
  {
    return $this->healthCheckFirewallState;
  }
  /**
   * URI of a Compute Engine instance or network endpoint.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoadBalancerBackend::class, 'Google_Service_NetworkManagement_LoadBalancerBackend');
