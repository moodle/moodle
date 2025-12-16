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

class HealthStatus extends \Google\Model
{
  public const HEALTH_STATE_HEALTHY = 'HEALTHY';
  public const HEALTH_STATE_UNHEALTHY = 'UNHEALTHY';
  public const IPV6_HEALTH_STATE_HEALTHY = 'HEALTHY';
  public const IPV6_HEALTH_STATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * The response to a Health Check probe had the HTTP response header field
   * X-Load-Balancing-Endpoint-Weight, but its content was invalid (i.e., not a
   * non-negative single-precision floating-point number in decimal string
   * representation).
   */
  public const WEIGHT_ERROR_INVALID_WEIGHT = 'INVALID_WEIGHT';
  /**
   * The response to a Health Check probe did not have the HTTP response header
   * field X-Load-Balancing-Endpoint-Weight.
   */
  public const WEIGHT_ERROR_MISSING_WEIGHT = 'MISSING_WEIGHT';
  /**
   * This is the value when the accompanied health status is either TIMEOUT
   * (i.e.,the Health Check probe was not able to get a response in time) or
   * UNKNOWN. For the latter, it should be typically because there has not been
   * sufficient time to parse and report the weight for a new backend (which is
   * with 0.0.0.0 ip address). However, it can be also due to an outage case for
   * which the health status is explicitly reset to UNKNOWN.
   */
  public const WEIGHT_ERROR_UNAVAILABLE_WEIGHT = 'UNAVAILABLE_WEIGHT';
  /**
   * This is the default value when WeightReportMode is DISABLE, and is also the
   * initial value when WeightReportMode has just updated to ENABLE or DRY_RUN
   * and there has not been sufficient time to parse and report the backend
   * weight.
   */
  public const WEIGHT_ERROR_WEIGHT_NONE = 'WEIGHT_NONE';
  /**
   * Metadata defined as annotations for network endpoint.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * URL of the forwarding rule associated with the health status of the
   * instance.
   *
   * @var string
   */
  public $forwardingRule;
  /**
   * A forwarding rule IP address assigned to this instance.
   *
   * @var string
   */
  public $forwardingRuleIp;
  /**
   * Health state of the IPv4 address of the instance.
   *
   * @var string
   */
  public $healthState;
  /**
   * URL of the instance resource.
   *
   * @var string
   */
  public $instance;
  /**
   * For target pool based Network Load Balancing, it indicates the forwarding
   * rule's IP address assigned to this instance. For other types of load
   * balancing, the field indicates VM internal ip.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * @var string
   */
  public $ipv6Address;
  /**
   * Health state of the IPv6 address of the instance.
   *
   * @var string
   */
  public $ipv6HealthState;
  /**
   * The named port of the instance group, not necessarily the port that is
   * health-checked.
   *
   * @var int
   */
  public $port;
  /**
   * @var string
   */
  public $weight;
  /**
   * @var string
   */
  public $weightError;

  /**
   * Metadata defined as annotations for network endpoint.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * URL of the forwarding rule associated with the health status of the
   * instance.
   *
   * @param string $forwardingRule
   */
  public function setForwardingRule($forwardingRule)
  {
    $this->forwardingRule = $forwardingRule;
  }
  /**
   * @return string
   */
  public function getForwardingRule()
  {
    return $this->forwardingRule;
  }
  /**
   * A forwarding rule IP address assigned to this instance.
   *
   * @param string $forwardingRuleIp
   */
  public function setForwardingRuleIp($forwardingRuleIp)
  {
    $this->forwardingRuleIp = $forwardingRuleIp;
  }
  /**
   * @return string
   */
  public function getForwardingRuleIp()
  {
    return $this->forwardingRuleIp;
  }
  /**
   * Health state of the IPv4 address of the instance.
   *
   * Accepted values: HEALTHY, UNHEALTHY
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
   * URL of the instance resource.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * For target pool based Network Load Balancing, it indicates the forwarding
   * rule's IP address assigned to this instance. For other types of load
   * balancing, the field indicates VM internal ip.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * @param string $ipv6Address
   */
  public function setIpv6Address($ipv6Address)
  {
    $this->ipv6Address = $ipv6Address;
  }
  /**
   * @return string
   */
  public function getIpv6Address()
  {
    return $this->ipv6Address;
  }
  /**
   * Health state of the IPv6 address of the instance.
   *
   * Accepted values: HEALTHY, UNHEALTHY
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
  /**
   * The named port of the instance group, not necessarily the port that is
   * health-checked.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * @param string $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return string
   */
  public function getWeight()
  {
    return $this->weight;
  }
  /**
   * @param self::WEIGHT_ERROR_* $weightError
   */
  public function setWeightError($weightError)
  {
    $this->weightError = $weightError;
  }
  /**
   * @return self::WEIGHT_ERROR_*
   */
  public function getWeightError()
  {
    return $this->weightError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HealthStatus::class, 'Google_Service_Compute_HealthStatus');
