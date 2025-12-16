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

class LoadBalancerBackendInfo extends \Google\Model
{
  /**
   * Configuration state unspecified. It usually means that the backend has no
   * health check attached, or there was an unexpected configuration error
   * preventing Connectivity tests from verifying health check configuration.
   */
  public const HEALTH_CHECK_FIREWALLS_CONFIG_STATE_HEALTH_CHECK_FIREWALLS_CONFIG_STATE_UNSPECIFIED = 'HEALTH_CHECK_FIREWALLS_CONFIG_STATE_UNSPECIFIED';
  /**
   * Firewall rules (policies) allowing health check traffic from all required
   * IP ranges to the backend are configured.
   */
  public const HEALTH_CHECK_FIREWALLS_CONFIG_STATE_FIREWALLS_CONFIGURED = 'FIREWALLS_CONFIGURED';
  /**
   * Firewall rules (policies) allow health check traffic only from a part of
   * required IP ranges.
   */
  public const HEALTH_CHECK_FIREWALLS_CONFIG_STATE_FIREWALLS_PARTIALLY_CONFIGURED = 'FIREWALLS_PARTIALLY_CONFIGURED';
  /**
   * Firewall rules (policies) deny health check traffic from all required IP
   * ranges to the backend.
   */
  public const HEALTH_CHECK_FIREWALLS_CONFIG_STATE_FIREWALLS_NOT_CONFIGURED = 'FIREWALLS_NOT_CONFIGURED';
  /**
   * The network contains firewall rules of unsupported types, so Connectivity
   * tests were not able to verify health check configuration status. Please
   * refer to the documentation for the list of unsupported configurations:
   * https://cloud.google.com/network-intelligence-center/docs/connectivity-
   * tests/concepts/overview#unsupported-configs
   */
  public const HEALTH_CHECK_FIREWALLS_CONFIG_STATE_FIREWALLS_UNSUPPORTED = 'FIREWALLS_UNSUPPORTED';
  /**
   * URI of the backend bucket this backend targets (if applicable).
   *
   * @var string
   */
  public $backendBucketUri;
  /**
   * URI of the backend service this backend belongs to (if applicable).
   *
   * @var string
   */
  public $backendServiceUri;
  /**
   * Output only. Health check firewalls configuration state for the backend.
   * This is a result of the static firewall analysis (verifying that health
   * check traffic from required IP ranges to the backend is allowed or not).
   * The backend might still be unhealthy even if these firewalls are
   * configured. Please refer to the documentation for more information:
   * https://cloud.google.com/load-balancing/docs/firewall-rules
   *
   * @var string
   */
  public $healthCheckFirewallsConfigState;
  /**
   * URI of the health check attached to this backend (if applicable).
   *
   * @var string
   */
  public $healthCheckUri;
  /**
   * URI of the instance group this backend belongs to (if applicable).
   *
   * @var string
   */
  public $instanceGroupUri;
  /**
   * URI of the backend instance (if applicable). Populated for instance group
   * backends, and zonal NEG backends.
   *
   * @var string
   */
  public $instanceUri;
  /**
   * Display name of the backend. For example, it might be an instance name for
   * the instance group backends, or an IP address and port for zonal network
   * endpoint group backends.
   *
   * @var string
   */
  public $name;
  /**
   * URI of the network endpoint group this backend belongs to (if applicable).
   *
   * @var string
   */
  public $networkEndpointGroupUri;
  /**
   * PSC Google API target this PSC NEG backend targets (if applicable).
   *
   * @var string
   */
  public $pscGoogleApiTarget;
  /**
   * URI of the PSC service attachment this PSC NEG backend targets (if
   * applicable).
   *
   * @var string
   */
  public $pscServiceAttachmentUri;

  /**
   * URI of the backend bucket this backend targets (if applicable).
   *
   * @param string $backendBucketUri
   */
  public function setBackendBucketUri($backendBucketUri)
  {
    $this->backendBucketUri = $backendBucketUri;
  }
  /**
   * @return string
   */
  public function getBackendBucketUri()
  {
    return $this->backendBucketUri;
  }
  /**
   * URI of the backend service this backend belongs to (if applicable).
   *
   * @param string $backendServiceUri
   */
  public function setBackendServiceUri($backendServiceUri)
  {
    $this->backendServiceUri = $backendServiceUri;
  }
  /**
   * @return string
   */
  public function getBackendServiceUri()
  {
    return $this->backendServiceUri;
  }
  /**
   * Output only. Health check firewalls configuration state for the backend.
   * This is a result of the static firewall analysis (verifying that health
   * check traffic from required IP ranges to the backend is allowed or not).
   * The backend might still be unhealthy even if these firewalls are
   * configured. Please refer to the documentation for more information:
   * https://cloud.google.com/load-balancing/docs/firewall-rules
   *
   * Accepted values: HEALTH_CHECK_FIREWALLS_CONFIG_STATE_UNSPECIFIED,
   * FIREWALLS_CONFIGURED, FIREWALLS_PARTIALLY_CONFIGURED,
   * FIREWALLS_NOT_CONFIGURED, FIREWALLS_UNSUPPORTED
   *
   * @param self::HEALTH_CHECK_FIREWALLS_CONFIG_STATE_* $healthCheckFirewallsConfigState
   */
  public function setHealthCheckFirewallsConfigState($healthCheckFirewallsConfigState)
  {
    $this->healthCheckFirewallsConfigState = $healthCheckFirewallsConfigState;
  }
  /**
   * @return self::HEALTH_CHECK_FIREWALLS_CONFIG_STATE_*
   */
  public function getHealthCheckFirewallsConfigState()
  {
    return $this->healthCheckFirewallsConfigState;
  }
  /**
   * URI of the health check attached to this backend (if applicable).
   *
   * @param string $healthCheckUri
   */
  public function setHealthCheckUri($healthCheckUri)
  {
    $this->healthCheckUri = $healthCheckUri;
  }
  /**
   * @return string
   */
  public function getHealthCheckUri()
  {
    return $this->healthCheckUri;
  }
  /**
   * URI of the instance group this backend belongs to (if applicable).
   *
   * @param string $instanceGroupUri
   */
  public function setInstanceGroupUri($instanceGroupUri)
  {
    $this->instanceGroupUri = $instanceGroupUri;
  }
  /**
   * @return string
   */
  public function getInstanceGroupUri()
  {
    return $this->instanceGroupUri;
  }
  /**
   * URI of the backend instance (if applicable). Populated for instance group
   * backends, and zonal NEG backends.
   *
   * @param string $instanceUri
   */
  public function setInstanceUri($instanceUri)
  {
    $this->instanceUri = $instanceUri;
  }
  /**
   * @return string
   */
  public function getInstanceUri()
  {
    return $this->instanceUri;
  }
  /**
   * Display name of the backend. For example, it might be an instance name for
   * the instance group backends, or an IP address and port for zonal network
   * endpoint group backends.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * URI of the network endpoint group this backend belongs to (if applicable).
   *
   * @param string $networkEndpointGroupUri
   */
  public function setNetworkEndpointGroupUri($networkEndpointGroupUri)
  {
    $this->networkEndpointGroupUri = $networkEndpointGroupUri;
  }
  /**
   * @return string
   */
  public function getNetworkEndpointGroupUri()
  {
    return $this->networkEndpointGroupUri;
  }
  /**
   * PSC Google API target this PSC NEG backend targets (if applicable).
   *
   * @param string $pscGoogleApiTarget
   */
  public function setPscGoogleApiTarget($pscGoogleApiTarget)
  {
    $this->pscGoogleApiTarget = $pscGoogleApiTarget;
  }
  /**
   * @return string
   */
  public function getPscGoogleApiTarget()
  {
    return $this->pscGoogleApiTarget;
  }
  /**
   * URI of the PSC service attachment this PSC NEG backend targets (if
   * applicable).
   *
   * @param string $pscServiceAttachmentUri
   */
  public function setPscServiceAttachmentUri($pscServiceAttachmentUri)
  {
    $this->pscServiceAttachmentUri = $pscServiceAttachmentUri;
  }
  /**
   * @return string
   */
  public function getPscServiceAttachmentUri()
  {
    return $this->pscServiceAttachmentUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoadBalancerBackendInfo::class, 'Google_Service_NetworkManagement_LoadBalancerBackendInfo');
