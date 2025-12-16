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

namespace Google\Service\WorkloadManager;

class InstanceProperties extends \Google\Collection
{
  protected $collection_key = 'roles';
  /**
   * Optional. Instance number.
   *
   * @var string
   */
  public $instanceNumber;
  /**
   * Optional. Instance machine type.
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. Instance roles.
   *
   * @var string[]
   */
  public $roles;
  protected $sapInstancePropertiesType = SapInstanceProperties::class;
  protected $sapInstancePropertiesDataType = '';
  /**
   * Optional. Instance status.
   *
   * @var string
   */
  public $status;
  protected $upcomingMaintenanceEventType = UpcomingMaintenanceEvent::class;
  protected $upcomingMaintenanceEventDataType = '';

  /**
   * Optional. Instance number.
   *
   * @param string $instanceNumber
   */
  public function setInstanceNumber($instanceNumber)
  {
    $this->instanceNumber = $instanceNumber;
  }
  /**
   * @return string
   */
  public function getInstanceNumber()
  {
    return $this->instanceNumber;
  }
  /**
   * Optional. Instance machine type.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Optional. Instance roles.
   *
   * @param string[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return string[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
  /**
   * Optional. SAP Instance properties.
   *
   * @param SapInstanceProperties $sapInstanceProperties
   */
  public function setSapInstanceProperties(SapInstanceProperties $sapInstanceProperties)
  {
    $this->sapInstanceProperties = $sapInstanceProperties;
  }
  /**
   * @return SapInstanceProperties
   */
  public function getSapInstanceProperties()
  {
    return $this->sapInstanceProperties;
  }
  /**
   * Optional. Instance status.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Optional. the next maintenance event on VM
   *
   * @param UpcomingMaintenanceEvent $upcomingMaintenanceEvent
   */
  public function setUpcomingMaintenanceEvent(UpcomingMaintenanceEvent $upcomingMaintenanceEvent)
  {
    $this->upcomingMaintenanceEvent = $upcomingMaintenanceEvent;
  }
  /**
   * @return UpcomingMaintenanceEvent
   */
  public function getUpcomingMaintenanceEvent()
  {
    return $this->upcomingMaintenanceEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceProperties::class, 'Google_Service_WorkloadManager_InstanceProperties');
