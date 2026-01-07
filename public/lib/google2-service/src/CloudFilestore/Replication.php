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

namespace Google\Service\CloudFilestore;

class Replication extends \Google\Collection
{
  /**
   * Role not set.
   */
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * The instance is the `ACTIVE` replication member, functions as the
   * replication source instance.
   */
  public const ROLE_ACTIVE = 'ACTIVE';
  /**
   * The instance is the `STANDBY` replication member, functions as the
   * replication destination instance.
   */
  public const ROLE_STANDBY = 'STANDBY';
  protected $collection_key = 'replicas';
  protected $replicasType = ReplicaConfig::class;
  protected $replicasDataType = 'array';
  /**
   * Optional. The replication role. When creating a new replica, this field
   * must be set to `STANDBY`.
   *
   * @var string
   */
  public $role;

  /**
   * Optional. Replication configuration for the replica instance associated
   * with this instance. Only a single replica is supported.
   *
   * @param ReplicaConfig[] $replicas
   */
  public function setReplicas($replicas)
  {
    $this->replicas = $replicas;
  }
  /**
   * @return ReplicaConfig[]
   */
  public function getReplicas()
  {
    return $this->replicas;
  }
  /**
   * Optional. The replication role. When creating a new replica, this field
   * must be set to `STANDBY`.
   *
   * Accepted values: ROLE_UNSPECIFIED, ACTIVE, STANDBY
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Replication::class, 'Google_Service_CloudFilestore_Replication');
