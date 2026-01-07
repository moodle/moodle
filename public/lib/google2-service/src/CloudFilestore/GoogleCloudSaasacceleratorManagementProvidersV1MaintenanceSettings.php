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

class GoogleCloudSaasacceleratorManagementProvidersV1MaintenanceSettings extends \Google\Model
{
  /**
   * Optional. Exclude instance from maintenance. When true, rollout service
   * will not attempt maintenance on the instance. Rollout service will include
   * the instance in reported rollout progress as not attempted.
   *
   * @var bool
   */
  public $exclude;
  /**
   * Optional. If the update call is triggered from rollback, set the value as
   * true.
   *
   * @var bool
   */
  public $isRollback;
  protected $maintenancePoliciesType = MaintenancePolicy::class;
  protected $maintenancePoliciesDataType = 'map';

  /**
   * Optional. Exclude instance from maintenance. When true, rollout service
   * will not attempt maintenance on the instance. Rollout service will include
   * the instance in reported rollout progress as not attempted.
   *
   * @param bool $exclude
   */
  public function setExclude($exclude)
  {
    $this->exclude = $exclude;
  }
  /**
   * @return bool
   */
  public function getExclude()
  {
    return $this->exclude;
  }
  /**
   * Optional. If the update call is triggered from rollback, set the value as
   * true.
   *
   * @param bool $isRollback
   */
  public function setIsRollback($isRollback)
  {
    $this->isRollback = $isRollback;
  }
  /**
   * @return bool
   */
  public function getIsRollback()
  {
    return $this->isRollback;
  }
  /**
   * Optional. The MaintenancePolicies that have been attached to the instance.
   * The key must be of the type name of the oneof policy name defined in
   * MaintenancePolicy, and the embedded policy must define the same policy
   * type. For details, please refer to go/mr-user-guide. Should not be set if
   * maintenance_policy_names is set. If only the name is needed, then only
   * populate MaintenancePolicy.name.
   *
   * @param MaintenancePolicy[] $maintenancePolicies
   */
  public function setMaintenancePolicies($maintenancePolicies)
  {
    $this->maintenancePolicies = $maintenancePolicies;
  }
  /**
   * @return MaintenancePolicy[]
   */
  public function getMaintenancePolicies()
  {
    return $this->maintenancePolicies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSaasacceleratorManagementProvidersV1MaintenanceSettings::class, 'Google_Service_CloudFilestore_GoogleCloudSaasacceleratorManagementProvidersV1MaintenanceSettings');
