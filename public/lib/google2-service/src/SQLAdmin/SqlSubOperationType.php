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

class SqlSubOperationType extends \Google\Model
{
  /**
   * Maintenance type is unspecified.
   */
  public const MAINTENANCE_TYPE_SQL_MAINTENANCE_TYPE_UNSPECIFIED = 'SQL_MAINTENANCE_TYPE_UNSPECIFIED';
  /**
   * Indicates that a standalone instance is undergoing maintenance. The
   * instance can be either a primary instance or a replica.
   */
  public const MAINTENANCE_TYPE_INSTANCE_MAINTENANCE = 'INSTANCE_MAINTENANCE';
  /**
   * Indicates that the primary instance and all of its replicas, including
   * cascading replicas, are undergoing maintenance. Maintenance is performed on
   * groups of replicas first, followed by the primary instance.
   */
  public const MAINTENANCE_TYPE_REPLICA_INCLUDED_MAINTENANCE = 'REPLICA_INCLUDED_MAINTENANCE';
  /**
   * Indicates that the standalone instance is undergoing maintenance, initiated
   * by self-service. The instance can be either a primary instance or a
   * replica.
   */
  public const MAINTENANCE_TYPE_INSTANCE_SELF_SERVICE_MAINTENANCE = 'INSTANCE_SELF_SERVICE_MAINTENANCE';
  /**
   * Indicates that the primary instance and all of its replicas are undergoing
   * maintenance, initiated by self-service. Maintenance is performed on groups
   * of replicas first, followed by the primary instance.
   */
  public const MAINTENANCE_TYPE_REPLICA_INCLUDED_SELF_SERVICE_MAINTENANCE = 'REPLICA_INCLUDED_SELF_SERVICE_MAINTENANCE';
  /**
   * The type of maintenance to be performed on the instance.
   *
   * @var string
   */
  public $maintenanceType;

  /**
   * The type of maintenance to be performed on the instance.
   *
   * Accepted values: SQL_MAINTENANCE_TYPE_UNSPECIFIED, INSTANCE_MAINTENANCE,
   * REPLICA_INCLUDED_MAINTENANCE, INSTANCE_SELF_SERVICE_MAINTENANCE,
   * REPLICA_INCLUDED_SELF_SERVICE_MAINTENANCE
   *
   * @param self::MAINTENANCE_TYPE_* $maintenanceType
   */
  public function setMaintenanceType($maintenanceType)
  {
    $this->maintenanceType = $maintenanceType;
  }
  /**
   * @return self::MAINTENANCE_TYPE_*
   */
  public function getMaintenanceType()
  {
    return $this->maintenanceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlSubOperationType::class, 'Google_Service_SQLAdmin_SqlSubOperationType');
