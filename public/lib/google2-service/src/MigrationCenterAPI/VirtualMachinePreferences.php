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

class VirtualMachinePreferences extends \Google\Model
{
  /**
   * Unspecified commitment plan.
   */
  public const COMMITMENT_PLAN_COMMITMENT_PLAN_UNSPECIFIED = 'COMMITMENT_PLAN_UNSPECIFIED';
  /**
   * No commitment plan.
   */
  public const COMMITMENT_PLAN_COMMITMENT_PLAN_NONE = 'COMMITMENT_PLAN_NONE';
  /**
   * 1 year commitment.
   */
  public const COMMITMENT_PLAN_COMMITMENT_PLAN_ONE_YEAR = 'COMMITMENT_PLAN_ONE_YEAR';
  /**
   * 3 years commitment.
   */
  public const COMMITMENT_PLAN_COMMITMENT_PLAN_THREE_YEARS = 'COMMITMENT_PLAN_THREE_YEARS';
  /**
   * Unspecified (default value).
   */
  public const SIZING_OPTIMIZATION_STRATEGY_SIZING_OPTIMIZATION_STRATEGY_UNSPECIFIED = 'SIZING_OPTIMIZATION_STRATEGY_UNSPECIFIED';
  /**
   * No optimization applied. Virtual machine sizing matches as closely as
   * possible the machine shape on the source site, not considering any actual
   * performance data.
   */
  public const SIZING_OPTIMIZATION_STRATEGY_SIZING_OPTIMIZATION_STRATEGY_SAME_AS_SOURCE = 'SIZING_OPTIMIZATION_STRATEGY_SAME_AS_SOURCE';
  /**
   * Virtual machine sizing will match the reported usage and shape, with some
   * slack. This a good value to start with.
   */
  public const SIZING_OPTIMIZATION_STRATEGY_SIZING_OPTIMIZATION_STRATEGY_MODERATE = 'SIZING_OPTIMIZATION_STRATEGY_MODERATE';
  /**
   * Virtual machine sizing will match the reported usage, with little slack.
   * Using this option can help reduce costs.
   */
  public const SIZING_OPTIMIZATION_STRATEGY_SIZING_OPTIMIZATION_STRATEGY_AGGRESSIVE = 'SIZING_OPTIMIZATION_STRATEGY_AGGRESSIVE';
  /**
   * Unspecified (default value).
   */
  public const TARGET_PRODUCT_COMPUTE_MIGRATION_TARGET_PRODUCT_UNSPECIFIED = 'COMPUTE_MIGRATION_TARGET_PRODUCT_UNSPECIFIED';
  /**
   * Prefer to migrate to Google Cloud Compute Engine.
   */
  public const TARGET_PRODUCT_COMPUTE_MIGRATION_TARGET_PRODUCT_COMPUTE_ENGINE = 'COMPUTE_MIGRATION_TARGET_PRODUCT_COMPUTE_ENGINE';
  /**
   * Prefer to migrate to Google Cloud VMware Engine.6278
   */
  public const TARGET_PRODUCT_COMPUTE_MIGRATION_TARGET_PRODUCT_VMWARE_ENGINE = 'COMPUTE_MIGRATION_TARGET_PRODUCT_VMWARE_ENGINE';
  /**
   * Prefer to migrate to Google Cloud Sole Tenant Nodes.
   */
  public const TARGET_PRODUCT_COMPUTE_MIGRATION_TARGET_PRODUCT_SOLE_TENANCY = 'COMPUTE_MIGRATION_TARGET_PRODUCT_SOLE_TENANCY';
  /**
   * Commitment plan to consider when calculating costs for virtual machine
   * insights and recommendations. If you are unsure which value to set, a 3
   * year commitment plan is often a good value to start with.
   *
   * @var string
   */
  public $commitmentPlan;
  protected $computeEnginePreferencesType = ComputeEnginePreferences::class;
  protected $computeEnginePreferencesDataType = '';
  protected $regionPreferencesType = RegionPreferences::class;
  protected $regionPreferencesDataType = '';
  /**
   * Sizing optimization strategy specifies the preferred strategy used when
   * extrapolating usage data to calculate insights and recommendations for a
   * virtual machine. If you are unsure which value to set, a moderate sizing
   * optimization strategy is often a good value to start with.
   *
   * @var string
   */
  public $sizingOptimizationStrategy;
  protected $soleTenancyPreferencesType = SoleTenancyPreferences::class;
  protected $soleTenancyPreferencesDataType = '';
  /**
   * Target product for assets using this preference set. Specify either target
   * product or business goal, but not both.
   *
   * @var string
   */
  public $targetProduct;
  protected $vmwareEnginePreferencesType = VmwareEnginePreferences::class;
  protected $vmwareEnginePreferencesDataType = '';

  /**
   * Commitment plan to consider when calculating costs for virtual machine
   * insights and recommendations. If you are unsure which value to set, a 3
   * year commitment plan is often a good value to start with.
   *
   * Accepted values: COMMITMENT_PLAN_UNSPECIFIED, COMMITMENT_PLAN_NONE,
   * COMMITMENT_PLAN_ONE_YEAR, COMMITMENT_PLAN_THREE_YEARS
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
  /**
   * Compute Engine preferences concern insights and recommendations for Compute
   * Engine target.
   *
   * @param ComputeEnginePreferences $computeEnginePreferences
   */
  public function setComputeEnginePreferences(ComputeEnginePreferences $computeEnginePreferences)
  {
    $this->computeEnginePreferences = $computeEnginePreferences;
  }
  /**
   * @return ComputeEnginePreferences
   */
  public function getComputeEnginePreferences()
  {
    return $this->computeEnginePreferences;
  }
  /**
   * Region preferences for assets using this preference set. If you are unsure
   * which value to set, the migration service API region is often a good value
   * to start with.
   *
   * @param RegionPreferences $regionPreferences
   */
  public function setRegionPreferences(RegionPreferences $regionPreferences)
  {
    $this->regionPreferences = $regionPreferences;
  }
  /**
   * @return RegionPreferences
   */
  public function getRegionPreferences()
  {
    return $this->regionPreferences;
  }
  /**
   * Sizing optimization strategy specifies the preferred strategy used when
   * extrapolating usage data to calculate insights and recommendations for a
   * virtual machine. If you are unsure which value to set, a moderate sizing
   * optimization strategy is often a good value to start with.
   *
   * Accepted values: SIZING_OPTIMIZATION_STRATEGY_UNSPECIFIED,
   * SIZING_OPTIMIZATION_STRATEGY_SAME_AS_SOURCE,
   * SIZING_OPTIMIZATION_STRATEGY_MODERATE,
   * SIZING_OPTIMIZATION_STRATEGY_AGGRESSIVE
   *
   * @param self::SIZING_OPTIMIZATION_STRATEGY_* $sizingOptimizationStrategy
   */
  public function setSizingOptimizationStrategy($sizingOptimizationStrategy)
  {
    $this->sizingOptimizationStrategy = $sizingOptimizationStrategy;
  }
  /**
   * @return self::SIZING_OPTIMIZATION_STRATEGY_*
   */
  public function getSizingOptimizationStrategy()
  {
    return $this->sizingOptimizationStrategy;
  }
  /**
   * Preferences concerning Sole Tenant nodes and virtual machines.
   *
   * @param SoleTenancyPreferences $soleTenancyPreferences
   */
  public function setSoleTenancyPreferences(SoleTenancyPreferences $soleTenancyPreferences)
  {
    $this->soleTenancyPreferences = $soleTenancyPreferences;
  }
  /**
   * @return SoleTenancyPreferences
   */
  public function getSoleTenancyPreferences()
  {
    return $this->soleTenancyPreferences;
  }
  /**
   * Target product for assets using this preference set. Specify either target
   * product or business goal, but not both.
   *
   * Accepted values: COMPUTE_MIGRATION_TARGET_PRODUCT_UNSPECIFIED,
   * COMPUTE_MIGRATION_TARGET_PRODUCT_COMPUTE_ENGINE,
   * COMPUTE_MIGRATION_TARGET_PRODUCT_VMWARE_ENGINE,
   * COMPUTE_MIGRATION_TARGET_PRODUCT_SOLE_TENANCY
   *
   * @param self::TARGET_PRODUCT_* $targetProduct
   */
  public function setTargetProduct($targetProduct)
  {
    $this->targetProduct = $targetProduct;
  }
  /**
   * @return self::TARGET_PRODUCT_*
   */
  public function getTargetProduct()
  {
    return $this->targetProduct;
  }
  /**
   * Preferences concerning insights and recommendations for Google Cloud VMware
   * Engine.
   *
   * @param VmwareEnginePreferences $vmwareEnginePreferences
   */
  public function setVmwareEnginePreferences(VmwareEnginePreferences $vmwareEnginePreferences)
  {
    $this->vmwareEnginePreferences = $vmwareEnginePreferences;
  }
  /**
   * @return VmwareEnginePreferences
   */
  public function getVmwareEnginePreferences()
  {
    return $this->vmwareEnginePreferences;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VirtualMachinePreferences::class, 'Google_Service_MigrationCenterAPI_VirtualMachinePreferences');
