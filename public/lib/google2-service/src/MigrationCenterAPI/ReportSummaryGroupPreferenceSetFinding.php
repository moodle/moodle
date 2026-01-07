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

class ReportSummaryGroupPreferenceSetFinding extends \Google\Model
{
  protected $computeEngineFindingType = ReportSummaryComputeEngineFinding::class;
  protected $computeEngineFindingDataType = '';
  /**
   * Description for the Preference Set.
   *
   * @var string
   */
  public $description;
  /**
   * Display Name of the Preference Set
   *
   * @var string
   */
  public $displayName;
  protected $machinePreferencesType = VirtualMachinePreferences::class;
  protected $machinePreferencesDataType = '';
  protected $monthlyCostComputeType = Money::class;
  protected $monthlyCostComputeDataType = '';
  protected $monthlyCostNetworkEgressType = Money::class;
  protected $monthlyCostNetworkEgressDataType = '';
  protected $monthlyCostOsLicenseType = Money::class;
  protected $monthlyCostOsLicenseDataType = '';
  protected $monthlyCostOtherType = Money::class;
  protected $monthlyCostOtherDataType = '';
  protected $monthlyCostStorageType = Money::class;
  protected $monthlyCostStorageDataType = '';
  protected $monthlyCostTotalType = Money::class;
  protected $monthlyCostTotalDataType = '';
  protected $soleTenantFindingType = ReportSummarySoleTenantFinding::class;
  protected $soleTenantFindingDataType = '';
  protected $vmwareEngineFindingType = ReportSummaryVmwareEngineFinding::class;
  protected $vmwareEngineFindingDataType = '';

  /**
   * A set of findings that applies to Compute Engine machines in the input.
   *
   * @param ReportSummaryComputeEngineFinding $computeEngineFinding
   */
  public function setComputeEngineFinding(ReportSummaryComputeEngineFinding $computeEngineFinding)
  {
    $this->computeEngineFinding = $computeEngineFinding;
  }
  /**
   * @return ReportSummaryComputeEngineFinding
   */
  public function getComputeEngineFinding()
  {
    return $this->computeEngineFinding;
  }
  /**
   * Description for the Preference Set.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Display Name of the Preference Set
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * A set of preferences that applies to all machines in the context.
   *
   * @param VirtualMachinePreferences $machinePreferences
   */
  public function setMachinePreferences(VirtualMachinePreferences $machinePreferences)
  {
    $this->machinePreferences = $machinePreferences;
  }
  /**
   * @return VirtualMachinePreferences
   */
  public function getMachinePreferences()
  {
    return $this->machinePreferences;
  }
  /**
   * Compute monthly cost for this preference set.
   *
   * @param Money $monthlyCostCompute
   */
  public function setMonthlyCostCompute(Money $monthlyCostCompute)
  {
    $this->monthlyCostCompute = $monthlyCostCompute;
  }
  /**
   * @return Money
   */
  public function getMonthlyCostCompute()
  {
    return $this->monthlyCostCompute;
  }
  /**
   * Network Egress monthly cost for this preference set.
   *
   * @param Money $monthlyCostNetworkEgress
   */
  public function setMonthlyCostNetworkEgress(Money $monthlyCostNetworkEgress)
  {
    $this->monthlyCostNetworkEgress = $monthlyCostNetworkEgress;
  }
  /**
   * @return Money
   */
  public function getMonthlyCostNetworkEgress()
  {
    return $this->monthlyCostNetworkEgress;
  }
  /**
   * Licensing monthly cost for this preference set.
   *
   * @param Money $monthlyCostOsLicense
   */
  public function setMonthlyCostOsLicense(Money $monthlyCostOsLicense)
  {
    $this->monthlyCostOsLicense = $monthlyCostOsLicense;
  }
  /**
   * @return Money
   */
  public function getMonthlyCostOsLicense()
  {
    return $this->monthlyCostOsLicense;
  }
  /**
   * Miscellaneous monthly cost for this preference set.
   *
   * @param Money $monthlyCostOther
   */
  public function setMonthlyCostOther(Money $monthlyCostOther)
  {
    $this->monthlyCostOther = $monthlyCostOther;
  }
  /**
   * @return Money
   */
  public function getMonthlyCostOther()
  {
    return $this->monthlyCostOther;
  }
  /**
   * Storage monthly cost for this preference set.
   *
   * @param Money $monthlyCostStorage
   */
  public function setMonthlyCostStorage(Money $monthlyCostStorage)
  {
    $this->monthlyCostStorage = $monthlyCostStorage;
  }
  /**
   * @return Money
   */
  public function getMonthlyCostStorage()
  {
    return $this->monthlyCostStorage;
  }
  /**
   * Total monthly cost for this preference set.
   *
   * @param Money $monthlyCostTotal
   */
  public function setMonthlyCostTotal(Money $monthlyCostTotal)
  {
    $this->monthlyCostTotal = $monthlyCostTotal;
  }
  /**
   * @return Money
   */
  public function getMonthlyCostTotal()
  {
    return $this->monthlyCostTotal;
  }
  /**
   * A set of findings that applies to Sole-Tenant machines in the input.
   *
   * @param ReportSummarySoleTenantFinding $soleTenantFinding
   */
  public function setSoleTenantFinding(ReportSummarySoleTenantFinding $soleTenantFinding)
  {
    $this->soleTenantFinding = $soleTenantFinding;
  }
  /**
   * @return ReportSummarySoleTenantFinding
   */
  public function getSoleTenantFinding()
  {
    return $this->soleTenantFinding;
  }
  /**
   * A set of findings that applies to VMWare machines in the input.
   *
   * @param ReportSummaryVmwareEngineFinding $vmwareEngineFinding
   */
  public function setVmwareEngineFinding(ReportSummaryVmwareEngineFinding $vmwareEngineFinding)
  {
    $this->vmwareEngineFinding = $vmwareEngineFinding;
  }
  /**
   * @return ReportSummaryVmwareEngineFinding
   */
  public function getVmwareEngineFinding()
  {
    return $this->vmwareEngineFinding;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSummaryGroupPreferenceSetFinding::class, 'Google_Service_MigrationCenterAPI_ReportSummaryGroupPreferenceSetFinding');
