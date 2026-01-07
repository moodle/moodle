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

class ReservationsBlocksPerformMaintenanceRequest extends \Google\Model
{
  /**
   * Trigger maintenance for all hosts belonging to this reservation
   * irrespective of whether VMs are running on them or not.
   */
  public const MAINTENANCE_SCOPE_ALL = 'ALL';
  /**
   * Internal only
   */
  public const MAINTENANCE_SCOPE_MAINTENANCE_SCOPE_UNSPECIFIED = 'MAINTENANCE_SCOPE_UNSPECIFIED';
  /**
   * Trigger maintenance only on the hosts belonging to this reservation which
   * have VMs running on them.
   */
  public const MAINTENANCE_SCOPE_RUNNING_VMS = 'RUNNING_VMS';
  /**
   * Trigger maintenance only on the hosts belonging to this reservation which
   * do not have any VMs running on them. This is not allowed for Standard ExR
   */
  public const MAINTENANCE_SCOPE_UNUSED_CAPACITY = 'UNUSED_CAPACITY';
  /**
   * Specifies if all, running or unused hosts are in scope for this request.
   *
   * @var string
   */
  public $maintenanceScope;

  /**
   * Specifies if all, running or unused hosts are in scope for this request.
   *
   * Accepted values: ALL, MAINTENANCE_SCOPE_UNSPECIFIED, RUNNING_VMS,
   * UNUSED_CAPACITY
   *
   * @param self::MAINTENANCE_SCOPE_* $maintenanceScope
   */
  public function setMaintenanceScope($maintenanceScope)
  {
    $this->maintenanceScope = $maintenanceScope;
  }
  /**
   * @return self::MAINTENANCE_SCOPE_*
   */
  public function getMaintenanceScope()
  {
    return $this->maintenanceScope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationsBlocksPerformMaintenanceRequest::class, 'Google_Service_Compute_ReservationsBlocksPerformMaintenanceRequest');
