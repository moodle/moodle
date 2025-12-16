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

class LoadBalancerInfo extends \Google\Collection
{
  /**
   * Type is unspecified.
   */
  public const BACKEND_TYPE_BACKEND_TYPE_UNSPECIFIED = 'BACKEND_TYPE_UNSPECIFIED';
  /**
   * Backend Service as the load balancer's backend.
   */
  public const BACKEND_TYPE_BACKEND_SERVICE = 'BACKEND_SERVICE';
  /**
   * Target Pool as the load balancer's backend.
   */
  public const BACKEND_TYPE_TARGET_POOL = 'TARGET_POOL';
  /**
   * Target Instance as the load balancer's backend.
   */
  public const BACKEND_TYPE_TARGET_INSTANCE = 'TARGET_INSTANCE';
  /**
   * Type is unspecified.
   */
  public const LOAD_BALANCER_TYPE_LOAD_BALANCER_TYPE_UNSPECIFIED = 'LOAD_BALANCER_TYPE_UNSPECIFIED';
  /**
   * Internal TCP/UDP load balancer.
   */
  public const LOAD_BALANCER_TYPE_INTERNAL_TCP_UDP = 'INTERNAL_TCP_UDP';
  /**
   * Network TCP/UDP load balancer.
   */
  public const LOAD_BALANCER_TYPE_NETWORK_TCP_UDP = 'NETWORK_TCP_UDP';
  /**
   * HTTP(S) proxy load balancer.
   */
  public const LOAD_BALANCER_TYPE_HTTP_PROXY = 'HTTP_PROXY';
  /**
   * TCP proxy load balancer.
   */
  public const LOAD_BALANCER_TYPE_TCP_PROXY = 'TCP_PROXY';
  /**
   * SSL proxy load balancer.
   */
  public const LOAD_BALANCER_TYPE_SSL_PROXY = 'SSL_PROXY';
  protected $collection_key = 'backends';
  /**
   * Type of load balancer's backend configuration.
   *
   * @var string
   */
  public $backendType;
  /**
   * Backend configuration URI.
   *
   * @var string
   */
  public $backendUri;
  protected $backendsType = LoadBalancerBackend::class;
  protected $backendsDataType = 'array';
  /**
   * URI of the health check for the load balancer. Deprecated and no longer
   * populated as different load balancer backends might have different health
   * checks.
   *
   * @deprecated
   * @var string
   */
  public $healthCheckUri;
  /**
   * Type of the load balancer.
   *
   * @var string
   */
  public $loadBalancerType;

  /**
   * Type of load balancer's backend configuration.
   *
   * Accepted values: BACKEND_TYPE_UNSPECIFIED, BACKEND_SERVICE, TARGET_POOL,
   * TARGET_INSTANCE
   *
   * @param self::BACKEND_TYPE_* $backendType
   */
  public function setBackendType($backendType)
  {
    $this->backendType = $backendType;
  }
  /**
   * @return self::BACKEND_TYPE_*
   */
  public function getBackendType()
  {
    return $this->backendType;
  }
  /**
   * Backend configuration URI.
   *
   * @param string $backendUri
   */
  public function setBackendUri($backendUri)
  {
    $this->backendUri = $backendUri;
  }
  /**
   * @return string
   */
  public function getBackendUri()
  {
    return $this->backendUri;
  }
  /**
   * Information for the loadbalancer backends.
   *
   * @param LoadBalancerBackend[] $backends
   */
  public function setBackends($backends)
  {
    $this->backends = $backends;
  }
  /**
   * @return LoadBalancerBackend[]
   */
  public function getBackends()
  {
    return $this->backends;
  }
  /**
   * URI of the health check for the load balancer. Deprecated and no longer
   * populated as different load balancer backends might have different health
   * checks.
   *
   * @deprecated
   * @param string $healthCheckUri
   */
  public function setHealthCheckUri($healthCheckUri)
  {
    $this->healthCheckUri = $healthCheckUri;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getHealthCheckUri()
  {
    return $this->healthCheckUri;
  }
  /**
   * Type of the load balancer.
   *
   * Accepted values: LOAD_BALANCER_TYPE_UNSPECIFIED, INTERNAL_TCP_UDP,
   * NETWORK_TCP_UDP, HTTP_PROXY, TCP_PROXY, SSL_PROXY
   *
   * @param self::LOAD_BALANCER_TYPE_* $loadBalancerType
   */
  public function setLoadBalancerType($loadBalancerType)
  {
    $this->loadBalancerType = $loadBalancerType;
  }
  /**
   * @return self::LOAD_BALANCER_TYPE_*
   */
  public function getLoadBalancerType()
  {
    return $this->loadBalancerType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoadBalancerInfo::class, 'Google_Service_NetworkManagement_LoadBalancerInfo');
