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

namespace Google\Service\SQLAdmin;

class ReplicaConfiguration extends \Google\Model
{
  /**
   * Optional. Specifies if a SQL Server replica is a cascadable replica. A
   * cascadable replica is a SQL Server cross region replica that supports
   * replica(s) under it.
   *
   * @var bool
   */
  public $cascadableReplica;
  /**
   * Specifies if the replica is the failover target. If the field is set to
   * `true`, the replica will be designated as a failover replica. In case the
   * primary instance fails, the replica instance will be promoted as the new
   * primary instance. Only one replica can be specified as failover target, and
   * the replica has to be in different zone with the primary instance.
   *
   * @var bool
   */
  public $failoverTarget;
  /**
   * This is always `sql#replicaConfiguration`.
   *
   * @var string
   */
  public $kind;
  protected $mysqlReplicaConfigurationType = MySqlReplicaConfiguration::class;
  protected $mysqlReplicaConfigurationDataType = '';

  /**
   * Optional. Specifies if a SQL Server replica is a cascadable replica. A
   * cascadable replica is a SQL Server cross region replica that supports
   * replica(s) under it.
   *
   * @param bool $cascadableReplica
   */
  public function setCascadableReplica($cascadableReplica)
  {
    $this->cascadableReplica = $cascadableReplica;
  }
  /**
   * @return bool
   */
  public function getCascadableReplica()
  {
    return $this->cascadableReplica;
  }
  /**
   * Specifies if the replica is the failover target. If the field is set to
   * `true`, the replica will be designated as a failover replica. In case the
   * primary instance fails, the replica instance will be promoted as the new
   * primary instance. Only one replica can be specified as failover target, and
   * the replica has to be in different zone with the primary instance.
   *
   * @param bool $failoverTarget
   */
  public function setFailoverTarget($failoverTarget)
  {
    $this->failoverTarget = $failoverTarget;
  }
  /**
   * @return bool
   */
  public function getFailoverTarget()
  {
    return $this->failoverTarget;
  }
  /**
   * This is always `sql#replicaConfiguration`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * MySQL specific configuration when replicating from a MySQL on-premises
   * primary instance. Replication configuration information such as the
   * username, password, certificates, and keys are not stored in the instance
   * metadata. The configuration information is used only to set up the
   * replication connection and is stored by MySQL in a file named `master.info`
   * in the data directory.
   *
   * @param MySqlReplicaConfiguration $mysqlReplicaConfiguration
   */
  public function setMysqlReplicaConfiguration(MySqlReplicaConfiguration $mysqlReplicaConfiguration)
  {
    $this->mysqlReplicaConfiguration = $mysqlReplicaConfiguration;
  }
  /**
   * @return MySqlReplicaConfiguration
   */
  public function getMysqlReplicaConfiguration()
  {
    return $this->mysqlReplicaConfiguration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicaConfiguration::class, 'Google_Service_SQLAdmin_ReplicaConfiguration');
