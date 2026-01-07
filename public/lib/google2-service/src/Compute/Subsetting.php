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

class Subsetting extends \Google\Model
{
  /**
   * Subsetting based on consistent hashing.
   *
   * For Traffic Director, the number of backends per backend group (the subset
   * size) is based on the `subset_size` parameter.
   *
   * For Internal HTTP(S) load balancing, the number of backends per backend
   * group (the subset size) is dynamically adjusted in two cases: - As the
   * number of proxy instances participating in Internal HTTP(S) load
   * balancing increases, the subset size decreases. - When the total number of
   * backends in a network exceeds the capacity of   a single proxy instance,
   * subset sizes are reduced automatically for   each service that has backend
   * subsetting enabled.
   */
  public const POLICY_CONSISTENT_HASH_SUBSETTING = 'CONSISTENT_HASH_SUBSETTING';
  /**
   * No Subsetting.
   *
   * Clients may open connections and send traffic to all backends of this
   * backend service. This can lead to performance issues if there is
   * substantial imbalance in the count of clients and backends.
   */
  public const POLICY_NONE = 'NONE';
  /**
   * @var string
   */
  public $policy;

  /**
   * @param self::POLICY_* $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return self::POLICY_*
   */
  public function getPolicy()
  {
    return $this->policy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subsetting::class, 'Google_Service_Compute_Subsetting');
