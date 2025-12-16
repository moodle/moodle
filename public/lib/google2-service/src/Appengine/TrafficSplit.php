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

namespace Google\Service\Appengine;

class TrafficSplit extends \Google\Model
{
  /**
   * Diversion method unspecified.
   */
  public const SHARD_BY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Diversion based on a specially named cookie, "GOOGAPPUID." The cookie must
   * be set by the application itself or no diversion will occur.
   */
  public const SHARD_BY_COOKIE = 'COOKIE';
  /**
   * Diversion based on applying the modulus operation to a fingerprint of the
   * IP address.
   */
  public const SHARD_BY_IP = 'IP';
  /**
   * Diversion based on weighted random assignment. An incoming request is
   * randomly routed to a version in the traffic split, with probability
   * proportional to the version's traffic share.
   */
  public const SHARD_BY_RANDOM = 'RANDOM';
  /**
   * Mapping from version IDs within the service to fractional (0.000, 1]
   * allocations of traffic for that version. Each version can be specified only
   * once, but some versions in the service may not have any traffic allocation.
   * Services that have traffic allocated cannot be deleted until either the
   * service is deleted or their traffic allocation is removed. Allocations must
   * sum to 1. Up to two decimal place precision is supported for IP-based
   * splits and up to three decimal places is supported for cookie-based splits.
   *
   * @var []
   */
  public $allocations;
  /**
   * Mechanism used to determine which version a request is sent to. The traffic
   * selection algorithm will be stable for either type until allocations are
   * changed.
   *
   * @var string
   */
  public $shardBy;

  public function setAllocations($allocations)
  {
    $this->allocations = $allocations;
  }
  public function getAllocations()
  {
    return $this->allocations;
  }
  /**
   * Mechanism used to determine which version a request is sent to. The traffic
   * selection algorithm will be stable for either type until allocations are
   * changed.
   *
   * Accepted values: UNSPECIFIED, COOKIE, IP, RANDOM
   *
   * @param self::SHARD_BY_* $shardBy
   */
  public function setShardBy($shardBy)
  {
    $this->shardBy = $shardBy;
  }
  /**
   * @return self::SHARD_BY_*
   */
  public function getShardBy()
  {
    return $this->shardBy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrafficSplit::class, 'Google_Service_Appengine_TrafficSplit');
