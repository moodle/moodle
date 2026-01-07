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

namespace Google\Service\Spanner;

class DirectedReadOptions extends \Google\Model
{
  protected $excludeReplicasType = ExcludeReplicas::class;
  protected $excludeReplicasDataType = '';
  protected $includeReplicasType = IncludeReplicas::class;
  protected $includeReplicasDataType = '';

  /**
   * `Exclude_replicas` indicates that specified replicas should be excluded
   * from serving requests. Spanner doesn't route requests to the replicas in
   * this list.
   *
   * @param ExcludeReplicas $excludeReplicas
   */
  public function setExcludeReplicas(ExcludeReplicas $excludeReplicas)
  {
    $this->excludeReplicas = $excludeReplicas;
  }
  /**
   * @return ExcludeReplicas
   */
  public function getExcludeReplicas()
  {
    return $this->excludeReplicas;
  }
  /**
   * `Include_replicas` indicates the order of replicas (as they appear in this
   * list) to process the request. If `auto_failover_disabled` is set to `true`
   * and all replicas are exhausted without finding a healthy replica, Spanner
   * waits for a replica in the list to become available, requests might fail
   * due to `DEADLINE_EXCEEDED` errors.
   *
   * @param IncludeReplicas $includeReplicas
   */
  public function setIncludeReplicas(IncludeReplicas $includeReplicas)
  {
    $this->includeReplicas = $includeReplicas;
  }
  /**
   * @return IncludeReplicas
   */
  public function getIncludeReplicas()
  {
    return $this->includeReplicas;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DirectedReadOptions::class, 'Google_Service_Spanner_DirectedReadOptions');
