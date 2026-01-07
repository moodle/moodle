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

class BackendServiceLocalityLoadBalancingPolicyConfigPolicy extends \Google\Model
{
  public const NAME_INVALID_LB_POLICY = 'INVALID_LB_POLICY';
  /**
   * An O(1) algorithm which selects two random healthy hosts and picks the host
   * which has fewer active requests.
   */
  public const NAME_LEAST_REQUEST = 'LEAST_REQUEST';
  /**
   * This algorithm implements consistent hashing to backends. Maglev can be
   * used as a drop in replacement for the ring hash load balancer. Maglev is
   * not as stable as ring hash but has faster table lookup build times and host
   * selection times. For more information about Maglev, seeMaglev: A Fast and
   * Reliable Software Network Load Balancer.
   */
  public const NAME_MAGLEV = 'MAGLEV';
  /**
   * Backend host is selected based on the client connection metadata, i.e.,
   * connections are opened to the same address as the destination address of
   * the incoming connection before the connection was redirected to the load
   * balancer.
   */
  public const NAME_ORIGINAL_DESTINATION = 'ORIGINAL_DESTINATION';
  /**
   * The load balancer selects a random healthy host.
   */
  public const NAME_RANDOM = 'RANDOM';
  /**
   * The ring/modulo hash load balancer implements consistent hashing to
   * backends. The algorithm has the property that the addition/removal of a
   * host from a set of N hosts only affects 1/N of the requests.
   */
  public const NAME_RING_HASH = 'RING_HASH';
  /**
   * This is a simple policy in which each healthy backend is selected in round
   * robin order. This is the default.
   */
  public const NAME_ROUND_ROBIN = 'ROUND_ROBIN';
  /**
   * Per-instance weighted Load Balancing via health check reported weights. In
   * internal passthrough network load balancing, it is weighted rendezvous
   * hashing. This option is only supported in internal passthrough network load
   * balancing.
   */
  public const NAME_WEIGHTED_GCP_RENDEZVOUS = 'WEIGHTED_GCP_RENDEZVOUS';
  /**
   * Per-instance weighted Load Balancing via health check reported weights. If
   * set, the Backend Service must configure a non legacy HTTP-based Health
   * Check, and health check replies are expected to contain non-standard HTTP
   * response header field X-Load-Balancing-Endpoint-Weight to specify the per-
   * instance weights. If set, Load Balancing is weighted based on the per-
   * instance weights reported in the last processed health check replies, as
   * long as every instance either reported a valid weight or had
   * UNAVAILABLE_WEIGHT. Otherwise, Load Balancing remains equal-weight. This
   * option is only supported in Network Load Balancing.
   */
  public const NAME_WEIGHTED_MAGLEV = 'WEIGHTED_MAGLEV';
  /**
   * Per-endpoint weighted round-robin Load Balancing using weights computed
   * from Backend reported Custom Metrics. If set, the Backend Service responses
   * are expected to contain non-standard HTTP response header field Endpoint-
   * Load-Metrics. The reported metrics to use for computing the weights are
   * specified via the customMetrics fields.
   */
  public const NAME_WEIGHTED_ROUND_ROBIN = 'WEIGHTED_ROUND_ROBIN';
  /**
   * The name of a locality load-balancing policy. Valid values include
   * ROUND_ROBIN and, for Java clients, LEAST_REQUEST. For information about
   * these values, see the description of localityLbPolicy.
   *
   * Do not specify the same policy more than once for a backend. If you do, the
   * configuration is rejected.
   *
   * @var string
   */
  public $name;

  /**
   * The name of a locality load-balancing policy. Valid values include
   * ROUND_ROBIN and, for Java clients, LEAST_REQUEST. For information about
   * these values, see the description of localityLbPolicy.
   *
   * Do not specify the same policy more than once for a backend. If you do, the
   * configuration is rejected.
   *
   * Accepted values: INVALID_LB_POLICY, LEAST_REQUEST, MAGLEV,
   * ORIGINAL_DESTINATION, RANDOM, RING_HASH, ROUND_ROBIN,
   * WEIGHTED_GCP_RENDEZVOUS, WEIGHTED_MAGLEV, WEIGHTED_ROUND_ROBIN
   *
   * @param self::NAME_* $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return self::NAME_*
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceLocalityLoadBalancingPolicyConfigPolicy::class, 'Google_Service_Compute_BackendServiceLocalityLoadBalancingPolicyConfigPolicy');
