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

class Backend extends \Google\Collection
{
  /**
   * Balance based on the number of simultaneous connections.
   */
  public const BALANCING_MODE_CONNECTION = 'CONNECTION';
  /**
   * Based on custom defined and reported metrics.
   */
  public const BALANCING_MODE_CUSTOM_METRICS = 'CUSTOM_METRICS';
  /**
   * Balance based on requests per second (RPS).
   */
  public const BALANCING_MODE_RATE = 'RATE';
  /**
   * Balance based on the backend utilization.
   */
  public const BALANCING_MODE_UTILIZATION = 'UTILIZATION';
  /**
   * No preference.
   */
  public const PREFERENCE_DEFAULT = 'DEFAULT';
  /**
   * If preference is unspecified, we set it to the DEFAULT value
   */
  public const PREFERENCE_PREFERENCE_UNSPECIFIED = 'PREFERENCE_UNSPECIFIED';
  /**
   * Traffic will be sent to this backend first.
   */
  public const PREFERENCE_PREFERRED = 'PREFERRED';
  protected $collection_key = 'customMetrics';
  /**
   * Specifies how to determine whether the backend of a load balancer can
   * handle additional traffic or is fully loaded. For usage guidelines, see
   * Connection balancing mode.
   *
   * Backends must use compatible balancing modes. For more information, see
   * Supported balancing modes and target capacity settings and Restrictions and
   * guidance for instance groups.
   *
   * Note: Currently, if you use the API to configure incompatible balancing
   * modes, the configuration might be accepted even though it has no impact and
   * is ignored. Specifically, Backend.maxUtilization is ignored when
   * Backend.balancingMode is RATE. In the future, this incompatible combination
   * will be rejected.
   *
   * @var string
   */
  public $balancingMode;
  /**
   * A multiplier applied to the backend's target capacity of its balancing
   * mode. The default value is 1, which means the group serves up to 100% of
   * its configured capacity (depending onbalancingMode). A setting of 0 means
   * the group is completely drained, offering 0% of its available capacity. The
   * valid ranges are 0.0 and [0.1,1.0]. You cannot configure a setting larger
   * than 0 and smaller than0.1. You cannot configure a setting of 0 when there
   * is only one backend attached to the backend service.
   *
   * Not available with backends that don't support using abalancingMode. This
   * includes backends such as global internet NEGs, regional serverless NEGs,
   * and PSC NEGs.
   *
   * @var float
   */
  public $capacityScaler;
  protected $customMetricsType = BackendCustomMetric::class;
  protected $customMetricsDataType = 'array';
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * This field designates whether this is a failover backend. More than one
   * failover backend can be configured for a given BackendService.
   *
   * @var bool
   */
  public $failover;
  /**
   * The fully-qualified URL of aninstance group or network endpoint group (NEG)
   * resource. To determine what types of backends a load balancer supports, see
   * the [Backend services overview](https://cloud.google.com/load-
   * balancing/docs/backend-service#backends).
   *
   * You must use the *fully-qualified* URL (starting
   * withhttps://www.googleapis.com/) to specify the instance group or NEG.
   * Partial URLs are not supported.
   *
   * If haPolicy is specified, backends must refer to NEG resources of type
   * GCE_VM_IP.
   *
   * @var string
   */
  public $group;
  /**
   * Defines a target maximum number of simultaneous connections. For usage
   * guidelines, seeConnection balancing mode and Utilization balancing mode.
   * Not available if the backend'sbalancingMode is RATE.
   *
   * @var int
   */
  public $maxConnections;
  /**
   * Defines a target maximum number of simultaneous connections.  For usage
   * guidelines, seeConnection balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isRATE.
   *
   * @var int
   */
  public $maxConnectionsPerEndpoint;
  /**
   * Defines a target maximum number of simultaneous connections. For usage
   * guidelines, seeConnection balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isRATE.
   *
   * @var int
   */
  public $maxConnectionsPerInstance;
  /**
   * Defines a maximum number of HTTP requests per second (RPS). For usage
   * guidelines, seeRate balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isCONNECTION.
   *
   * @var int
   */
  public $maxRate;
  /**
   * Defines a maximum target for requests per second (RPS). For usage
   * guidelines, seeRate balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isCONNECTION.
   *
   * @var float
   */
  public $maxRatePerEndpoint;
  /**
   * Defines a maximum target for requests per second (RPS). For usage
   * guidelines, seeRate balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isCONNECTION.
   *
   * @var float
   */
  public $maxRatePerInstance;
  /**
   * Optional parameter to define a target capacity for theUTILIZATION balancing
   * mode. The valid range is[0.0, 1.0].
   *
   * For usage guidelines, seeUtilization balancing mode.
   *
   * @var float
   */
  public $maxUtilization;
  /**
   * This field indicates whether this backend should be fully utilized before
   * sending traffic to backends with default preference. The possible values
   * are:        - PREFERRED: Backends with this preference level will be
   * filled up to their capacity limits first, based on RTT.    - DEFAULT:  If
   * preferred backends don't have enough    capacity, backends in this layer
   * would be used and traffic would be    assigned based on the load balancing
   * algorithm you use. This is the    default
   *
   * @var string
   */
  public $preference;

  /**
   * Specifies how to determine whether the backend of a load balancer can
   * handle additional traffic or is fully loaded. For usage guidelines, see
   * Connection balancing mode.
   *
   * Backends must use compatible balancing modes. For more information, see
   * Supported balancing modes and target capacity settings and Restrictions and
   * guidance for instance groups.
   *
   * Note: Currently, if you use the API to configure incompatible balancing
   * modes, the configuration might be accepted even though it has no impact and
   * is ignored. Specifically, Backend.maxUtilization is ignored when
   * Backend.balancingMode is RATE. In the future, this incompatible combination
   * will be rejected.
   *
   * Accepted values: CONNECTION, CUSTOM_METRICS, RATE, UTILIZATION
   *
   * @param self::BALANCING_MODE_* $balancingMode
   */
  public function setBalancingMode($balancingMode)
  {
    $this->balancingMode = $balancingMode;
  }
  /**
   * @return self::BALANCING_MODE_*
   */
  public function getBalancingMode()
  {
    return $this->balancingMode;
  }
  /**
   * A multiplier applied to the backend's target capacity of its balancing
   * mode. The default value is 1, which means the group serves up to 100% of
   * its configured capacity (depending onbalancingMode). A setting of 0 means
   * the group is completely drained, offering 0% of its available capacity. The
   * valid ranges are 0.0 and [0.1,1.0]. You cannot configure a setting larger
   * than 0 and smaller than0.1. You cannot configure a setting of 0 when there
   * is only one backend attached to the backend service.
   *
   * Not available with backends that don't support using abalancingMode. This
   * includes backends such as global internet NEGs, regional serverless NEGs,
   * and PSC NEGs.
   *
   * @param float $capacityScaler
   */
  public function setCapacityScaler($capacityScaler)
  {
    $this->capacityScaler = $capacityScaler;
  }
  /**
   * @return float
   */
  public function getCapacityScaler()
  {
    return $this->capacityScaler;
  }
  /**
   * List of custom metrics that are used for CUSTOM_METRICS BalancingMode.
   *
   * @param BackendCustomMetric[] $customMetrics
   */
  public function setCustomMetrics($customMetrics)
  {
    $this->customMetrics = $customMetrics;
  }
  /**
   * @return BackendCustomMetric[]
   */
  public function getCustomMetrics()
  {
    return $this->customMetrics;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * This field designates whether this is a failover backend. More than one
   * failover backend can be configured for a given BackendService.
   *
   * @param bool $failover
   */
  public function setFailover($failover)
  {
    $this->failover = $failover;
  }
  /**
   * @return bool
   */
  public function getFailover()
  {
    return $this->failover;
  }
  /**
   * The fully-qualified URL of aninstance group or network endpoint group (NEG)
   * resource. To determine what types of backends a load balancer supports, see
   * the [Backend services overview](https://cloud.google.com/load-
   * balancing/docs/backend-service#backends).
   *
   * You must use the *fully-qualified* URL (starting
   * withhttps://www.googleapis.com/) to specify the instance group or NEG.
   * Partial URLs are not supported.
   *
   * If haPolicy is specified, backends must refer to NEG resources of type
   * GCE_VM_IP.
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Defines a target maximum number of simultaneous connections. For usage
   * guidelines, seeConnection balancing mode and Utilization balancing mode.
   * Not available if the backend'sbalancingMode is RATE.
   *
   * @param int $maxConnections
   */
  public function setMaxConnections($maxConnections)
  {
    $this->maxConnections = $maxConnections;
  }
  /**
   * @return int
   */
  public function getMaxConnections()
  {
    return $this->maxConnections;
  }
  /**
   * Defines a target maximum number of simultaneous connections.  For usage
   * guidelines, seeConnection balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isRATE.
   *
   * @param int $maxConnectionsPerEndpoint
   */
  public function setMaxConnectionsPerEndpoint($maxConnectionsPerEndpoint)
  {
    $this->maxConnectionsPerEndpoint = $maxConnectionsPerEndpoint;
  }
  /**
   * @return int
   */
  public function getMaxConnectionsPerEndpoint()
  {
    return $this->maxConnectionsPerEndpoint;
  }
  /**
   * Defines a target maximum number of simultaneous connections. For usage
   * guidelines, seeConnection balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isRATE.
   *
   * @param int $maxConnectionsPerInstance
   */
  public function setMaxConnectionsPerInstance($maxConnectionsPerInstance)
  {
    $this->maxConnectionsPerInstance = $maxConnectionsPerInstance;
  }
  /**
   * @return int
   */
  public function getMaxConnectionsPerInstance()
  {
    return $this->maxConnectionsPerInstance;
  }
  /**
   * Defines a maximum number of HTTP requests per second (RPS). For usage
   * guidelines, seeRate balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isCONNECTION.
   *
   * @param int $maxRate
   */
  public function setMaxRate($maxRate)
  {
    $this->maxRate = $maxRate;
  }
  /**
   * @return int
   */
  public function getMaxRate()
  {
    return $this->maxRate;
  }
  /**
   * Defines a maximum target for requests per second (RPS). For usage
   * guidelines, seeRate balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isCONNECTION.
   *
   * @param float $maxRatePerEndpoint
   */
  public function setMaxRatePerEndpoint($maxRatePerEndpoint)
  {
    $this->maxRatePerEndpoint = $maxRatePerEndpoint;
  }
  /**
   * @return float
   */
  public function getMaxRatePerEndpoint()
  {
    return $this->maxRatePerEndpoint;
  }
  /**
   * Defines a maximum target for requests per second (RPS). For usage
   * guidelines, seeRate balancing mode and Utilization balancing mode.
   *
   * Not available if the backend's balancingMode isCONNECTION.
   *
   * @param float $maxRatePerInstance
   */
  public function setMaxRatePerInstance($maxRatePerInstance)
  {
    $this->maxRatePerInstance = $maxRatePerInstance;
  }
  /**
   * @return float
   */
  public function getMaxRatePerInstance()
  {
    return $this->maxRatePerInstance;
  }
  /**
   * Optional parameter to define a target capacity for theUTILIZATION balancing
   * mode. The valid range is[0.0, 1.0].
   *
   * For usage guidelines, seeUtilization balancing mode.
   *
   * @param float $maxUtilization
   */
  public function setMaxUtilization($maxUtilization)
  {
    $this->maxUtilization = $maxUtilization;
  }
  /**
   * @return float
   */
  public function getMaxUtilization()
  {
    return $this->maxUtilization;
  }
  /**
   * This field indicates whether this backend should be fully utilized before
   * sending traffic to backends with default preference. The possible values
   * are:        - PREFERRED: Backends with this preference level will be
   * filled up to their capacity limits first, based on RTT.    - DEFAULT:  If
   * preferred backends don't have enough    capacity, backends in this layer
   * would be used and traffic would be    assigned based on the load balancing
   * algorithm you use. This is the    default
   *
   * Accepted values: DEFAULT, PREFERENCE_UNSPECIFIED, PREFERRED
   *
   * @param self::PREFERENCE_* $preference
   */
  public function setPreference($preference)
  {
    $this->preference = $preference;
  }
  /**
   * @return self::PREFERENCE_*
   */
  public function getPreference()
  {
    return $this->preference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backend::class, 'Google_Service_Compute_Backend');
