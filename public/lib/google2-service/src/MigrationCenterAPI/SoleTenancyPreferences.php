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

namespace Google\Service\MigrationCenterAPI;

class SoleTenancyPreferences extends \Google\Collection
{
  /**
   * Unspecified commitment plan.
   */
  public const COMMITMENT_PLAN_COMMITMENT_PLAN_UNSPECIFIED = 'COMMITMENT_PLAN_UNSPECIFIED';
  /**
   * No commitment plan (on-demand usage).
   */
  public const COMMITMENT_PLAN_ON_DEMAND = 'ON_DEMAND';
  /**
   * 1 year commitment.
   */
  public const COMMITMENT_PLAN_COMMITMENT_1_YEAR = 'COMMITMENT_1_YEAR';
  /**
   * 3 years commitment.
   */
  public const COMMITMENT_PLAN_COMMITMENT_3_YEAR = 'COMMITMENT_3_YEAR';
  /**
   * Unspecified host maintenance policy.
   */
  public const HOST_MAINTENANCE_POLICY_HOST_MAINTENANCE_POLICY_UNSPECIFIED = 'HOST_MAINTENANCE_POLICY_UNSPECIFIED';
  /**
   * Default host maintenance policy.
   */
  public const HOST_MAINTENANCE_POLICY_HOST_MAINTENANCE_POLICY_DEFAULT = 'HOST_MAINTENANCE_POLICY_DEFAULT';
  /**
   * Restart in place host maintenance policy.
   */
  public const HOST_MAINTENANCE_POLICY_HOST_MAINTENANCE_POLICY_RESTART_IN_PLACE = 'HOST_MAINTENANCE_POLICY_RESTART_IN_PLACE';
  /**
   * Migrate within node group host maintenance policy.
   */
  public const HOST_MAINTENANCE_POLICY_HOST_MAINTENANCE_POLICY_MIGRATE_WITHIN_NODE_GROUP = 'HOST_MAINTENANCE_POLICY_MIGRATE_WITHIN_NODE_GROUP';
  protected $collection_key = 'nodeTypes';
  /**
   * Commitment plan to consider when calculating costs for virtual machine
   * insights and recommendations. If you are unsure which value to set, a 3
   * year commitment plan is often a good value to start with.
   *
   * @var string
   */
  public $commitmentPlan;
  /**
   * CPU overcommit ratio. Acceptable values are between 1.0 and 2.0 inclusive.
   *
   * @var 
   */
  public $cpuOvercommitRatio;
  /**
   * Sole Tenancy nodes maintenance policy.
   *
   * @var string
   */
  public $hostMaintenancePolicy;
  protected $nodeTypesType = SoleTenantNodeType::class;
  protected $nodeTypesDataType = 'array';

  /**
   * Commitment plan to consider when calculating costs for virtual machine
   * insights and recommendations. If you are unsure which value to set, a 3
   * year commitment plan is often a good value to start with.
   *
   * Accepted values: COMMITMENT_PLAN_UNSPECIFIED, ON_DEMAND, COMMITMENT_1_YEAR,
   * COMMITMENT_3_YEAR
   *
   * @param self::COMMITMENT_PLAN_* $commitmentPlan
   */
  public function setCommitmentPlan($commitmentPlan)
  {
    $this->commitmentPlan = $commitmentPlan;
  }
  /**
   * @return self::COMMITMENT_PLAN_*
   */
  public function getCommitmentPlan()
  {
    return $this->commitmentPlan;
  }
  public function setCpuOvercommitRatio($cpuOvercommitRatio)
  {
    $this->cpuOvercommitRatio = $cpuOvercommitRatio;
  }
  public function getCpuOvercommitRatio()
  {
    return $this->cpuOvercommitRatio;
  }
  /**
   * Sole Tenancy nodes maintenance policy.
   *
   * Accepted values: HOST_MAINTENANCE_POLICY_UNSPECIFIED,
   * HOST_MAINTENANCE_POLICY_DEFAULT, HOST_MAINTENANCE_POLICY_RESTART_IN_PLACE,
   * HOST_MAINTENANCE_POLICY_MIGRATE_WITHIN_NODE_GROUP
   *
   * @param self::HOST_MAINTENANCE_POLICY_* $hostMaintenancePolicy
   */
  public function setHostMaintenancePolicy($hostMaintenancePolicy)
  {
    $this->hostMaintenancePolicy = $hostMaintenancePolicy;
  }
  /**
   * @return self::HOST_MAINTENANCE_POLICY_*
   */
  public function getHostMaintenancePolicy()
  {
    return $this->hostMaintenancePolicy;
  }
  /**
   * A list of sole tenant node types. An empty list means that all possible
   * node types will be considered.
   *
   * @param SoleTenantNodeType[] $nodeTypes
   */
  public function setNodeTypes($nodeTypes)
  {
    $this->nodeTypes = $nodeTypes;
  }
  /**
   * @return SoleTenantNodeType[]
   */
  public function getNodeTypes()
  {
    return $this->nodeTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SoleTenancyPreferences::class, 'Google_Service_MigrationCenterAPI_SoleTenancyPreferences');
