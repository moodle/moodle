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

class BackendServiceConnectionTrackingPolicy extends \Google\Model
{
  public const CONNECTION_PERSISTENCE_ON_UNHEALTHY_BACKENDS_ALWAYS_PERSIST = 'ALWAYS_PERSIST';
  public const CONNECTION_PERSISTENCE_ON_UNHEALTHY_BACKENDS_DEFAULT_FOR_PROTOCOL = 'DEFAULT_FOR_PROTOCOL';
  public const CONNECTION_PERSISTENCE_ON_UNHEALTHY_BACKENDS_NEVER_PERSIST = 'NEVER_PERSIST';
  public const TRACKING_MODE_INVALID_TRACKING_MODE = 'INVALID_TRACKING_MODE';
  public const TRACKING_MODE_PER_CONNECTION = 'PER_CONNECTION';
  public const TRACKING_MODE_PER_SESSION = 'PER_SESSION';
  /**
   * Specifies connection persistence when backends are unhealthy. The default
   * value is DEFAULT_FOR_PROTOCOL.
   *
   * If set to DEFAULT_FOR_PROTOCOL, the existing connections persist on
   * unhealthy backends only for connection-oriented protocols (TCP and SCTP)
   * and only if the Tracking Mode isPER_CONNECTION (default tracking mode) or
   * the Session Affinity is configured for 5-tuple. They do not persist forUDP.
   *
   * If set to NEVER_PERSIST, after a backend becomes unhealthy, the existing
   * connections on the unhealthy backend are never persisted on the unhealthy
   * backend. They are always diverted to newly selected healthy backends
   * (unless all backends are unhealthy).
   *
   * If set to ALWAYS_PERSIST, existing connections always persist on unhealthy
   * backends regardless of protocol and session affinity. It is generally not
   * recommended to use this mode overriding the default.
   *
   * For more details, see [Connection Persistence for Network Load
   * Balancing](https://cloud.google.com/load-balancing/docs/network/networklb-
   * backend-service#connection-persistence) and [Connection Persistence for
   * Internal TCP/UDP Load Balancing](https://cloud.google.com/load-
   * balancing/docs/internal#connection-persistence).
   *
   * @var string
   */
  public $connectionPersistenceOnUnhealthyBackends;
  /**
   * Enable Strong Session Affinity for external passthrough Network Load
   * Balancers. This option is not available publicly.
   *
   * @var bool
   */
  public $enableStrongAffinity;
  /**
   * Specifies how long to keep a Connection Tracking entry while there is no
   * matching traffic (in seconds).
   *
   * For internal passthrough Network Load Balancers:        - The minimum
   * (default) is 10 minutes and the maximum is 16 hours.    - It can be set
   * only if Connection Tracking is less than 5-tuple    (i.e. Session Affinity
   * is CLIENT_IP_NO_DESTINATION,CLIENT_IP or CLIENT_IP_PROTO, and Tracking
   * Mode is PER_SESSION).
   *
   * For external passthrough Network Load Balancers the default is 60 seconds.
   * This option is not available publicly.
   *
   * @var int
   */
  public $idleTimeoutSec;
  /**
   * Specifies the key used for connection tracking. There are two options:
   * - PER_CONNECTION: This is the default mode. The Connection    Tracking is
   * performed as per the Connection Key (default Hash Method) for    the
   * specific protocol.    - PER_SESSION: The Connection Tracking is performed
   * as per    the configured Session Affinity. It matches the configured
   * Session    Affinity.
   *
   * For more details, see [Tracking Mode for Network Load
   * Balancing](https://cloud.google.com/load-balancing/docs/network/networklb-
   * backend-service#tracking-mode) and [Tracking Mode for Internal TCP/UDP Load
   * Balancing](https://cloud.google.com/load-balancing/docs/internal#tracking-
   * mode).
   *
   * @var string
   */
  public $trackingMode;

  /**
   * Specifies connection persistence when backends are unhealthy. The default
   * value is DEFAULT_FOR_PROTOCOL.
   *
   * If set to DEFAULT_FOR_PROTOCOL, the existing connections persist on
   * unhealthy backends only for connection-oriented protocols (TCP and SCTP)
   * and only if the Tracking Mode isPER_CONNECTION (default tracking mode) or
   * the Session Affinity is configured for 5-tuple. They do not persist forUDP.
   *
   * If set to NEVER_PERSIST, after a backend becomes unhealthy, the existing
   * connections on the unhealthy backend are never persisted on the unhealthy
   * backend. They are always diverted to newly selected healthy backends
   * (unless all backends are unhealthy).
   *
   * If set to ALWAYS_PERSIST, existing connections always persist on unhealthy
   * backends regardless of protocol and session affinity. It is generally not
   * recommended to use this mode overriding the default.
   *
   * For more details, see [Connection Persistence for Network Load
   * Balancing](https://cloud.google.com/load-balancing/docs/network/networklb-
   * backend-service#connection-persistence) and [Connection Persistence for
   * Internal TCP/UDP Load Balancing](https://cloud.google.com/load-
   * balancing/docs/internal#connection-persistence).
   *
   * Accepted values: ALWAYS_PERSIST, DEFAULT_FOR_PROTOCOL, NEVER_PERSIST
   *
   * @param self::CONNECTION_PERSISTENCE_ON_UNHEALTHY_BACKENDS_* $connectionPersistenceOnUnhealthyBackends
   */
  public function setConnectionPersistenceOnUnhealthyBackends($connectionPersistenceOnUnhealthyBackends)
  {
    $this->connectionPersistenceOnUnhealthyBackends = $connectionPersistenceOnUnhealthyBackends;
  }
  /**
   * @return self::CONNECTION_PERSISTENCE_ON_UNHEALTHY_BACKENDS_*
   */
  public function getConnectionPersistenceOnUnhealthyBackends()
  {
    return $this->connectionPersistenceOnUnhealthyBackends;
  }
  /**
   * Enable Strong Session Affinity for external passthrough Network Load
   * Balancers. This option is not available publicly.
   *
   * @param bool $enableStrongAffinity
   */
  public function setEnableStrongAffinity($enableStrongAffinity)
  {
    $this->enableStrongAffinity = $enableStrongAffinity;
  }
  /**
   * @return bool
   */
  public function getEnableStrongAffinity()
  {
    return $this->enableStrongAffinity;
  }
  /**
   * Specifies how long to keep a Connection Tracking entry while there is no
   * matching traffic (in seconds).
   *
   * For internal passthrough Network Load Balancers:        - The minimum
   * (default) is 10 minutes and the maximum is 16 hours.    - It can be set
   * only if Connection Tracking is less than 5-tuple    (i.e. Session Affinity
   * is CLIENT_IP_NO_DESTINATION,CLIENT_IP or CLIENT_IP_PROTO, and Tracking
   * Mode is PER_SESSION).
   *
   * For external passthrough Network Load Balancers the default is 60 seconds.
   * This option is not available publicly.
   *
   * @param int $idleTimeoutSec
   */
  public function setIdleTimeoutSec($idleTimeoutSec)
  {
    $this->idleTimeoutSec = $idleTimeoutSec;
  }
  /**
   * @return int
   */
  public function getIdleTimeoutSec()
  {
    return $this->idleTimeoutSec;
  }
  /**
   * Specifies the key used for connection tracking. There are two options:
   * - PER_CONNECTION: This is the default mode. The Connection    Tracking is
   * performed as per the Connection Key (default Hash Method) for    the
   * specific protocol.    - PER_SESSION: The Connection Tracking is performed
   * as per    the configured Session Affinity. It matches the configured
   * Session    Affinity.
   *
   * For more details, see [Tracking Mode for Network Load
   * Balancing](https://cloud.google.com/load-balancing/docs/network/networklb-
   * backend-service#tracking-mode) and [Tracking Mode for Internal TCP/UDP Load
   * Balancing](https://cloud.google.com/load-balancing/docs/internal#tracking-
   * mode).
   *
   * Accepted values: INVALID_TRACKING_MODE, PER_CONNECTION, PER_SESSION
   *
   * @param self::TRACKING_MODE_* $trackingMode
   */
  public function setTrackingMode($trackingMode)
  {
    $this->trackingMode = $trackingMode;
  }
  /**
   * @return self::TRACKING_MODE_*
   */
  public function getTrackingMode()
  {
    return $this->trackingMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceConnectionTrackingPolicy::class, 'Google_Service_Compute_BackendServiceConnectionTrackingPolicy');
