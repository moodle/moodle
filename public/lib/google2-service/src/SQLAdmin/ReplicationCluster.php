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

class ReplicationCluster extends \Google\Model
{
  /**
   * Output only. Read-only field that indicates whether the replica is a DR
   * replica. This field is not set if the instance is a primary instance.
   *
   * @var bool
   */
  public $drReplica;
  /**
   * Optional. If the instance is a primary instance, then this field identifies
   * the disaster recovery (DR) replica. A DR replica is an optional
   * configuration for Enterprise Plus edition instances. If the instance is a
   * read replica, then the field is not set. Set this field to a replica name
   * to designate a DR replica for a primary instance. Remove the replica name
   * to remove the DR replica designation.
   *
   * @var string
   */
  public $failoverDrReplicaName;
  /**
   * Output only. If set, this field indicates this instance has a private
   * service access (PSA) DNS endpoint that is pointing to the primary instance
   * of the cluster. If this instance is the primary, then the DNS endpoint
   * points to this instance. After a switchover or replica failover operation,
   * this DNS endpoint points to the promoted instance. This is a read-only
   * field, returned to the user as information. This field can exist even if a
   * standalone instance doesn't have a DR replica yet or the DR replica is
   * deleted.
   *
   * @var string
   */
  public $psaWriteEndpoint;

  /**
   * Output only. Read-only field that indicates whether the replica is a DR
   * replica. This field is not set if the instance is a primary instance.
   *
   * @param bool $drReplica
   */
  public function setDrReplica($drReplica)
  {
    $this->drReplica = $drReplica;
  }
  /**
   * @return bool
   */
  public function getDrReplica()
  {
    return $this->drReplica;
  }
  /**
   * Optional. If the instance is a primary instance, then this field identifies
   * the disaster recovery (DR) replica. A DR replica is an optional
   * configuration for Enterprise Plus edition instances. If the instance is a
   * read replica, then the field is not set. Set this field to a replica name
   * to designate a DR replica for a primary instance. Remove the replica name
   * to remove the DR replica designation.
   *
   * @param string $failoverDrReplicaName
   */
  public function setFailoverDrReplicaName($failoverDrReplicaName)
  {
    $this->failoverDrReplicaName = $failoverDrReplicaName;
  }
  /**
   * @return string
   */
  public function getFailoverDrReplicaName()
  {
    return $this->failoverDrReplicaName;
  }
  /**
   * Output only. If set, this field indicates this instance has a private
   * service access (PSA) DNS endpoint that is pointing to the primary instance
   * of the cluster. If this instance is the primary, then the DNS endpoint
   * points to this instance. After a switchover or replica failover operation,
   * this DNS endpoint points to the promoted instance. This is a read-only
   * field, returned to the user as information. This field can exist even if a
   * standalone instance doesn't have a DR replica yet or the DR replica is
   * deleted.
   *
   * @param string $psaWriteEndpoint
   */
  public function setPsaWriteEndpoint($psaWriteEndpoint)
  {
    $this->psaWriteEndpoint = $psaWriteEndpoint;
  }
  /**
   * @return string
   */
  public function getPsaWriteEndpoint()
  {
    return $this->psaWriteEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicationCluster::class, 'Google_Service_SQLAdmin_ReplicationCluster');
