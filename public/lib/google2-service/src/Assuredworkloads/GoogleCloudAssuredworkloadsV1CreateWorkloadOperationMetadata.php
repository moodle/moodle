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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1CreateWorkloadOperationMetadata extends \Google\Model
{
  /**
   * Unknown compliance regime.
   */
  public const COMPLIANCE_REGIME_COMPLIANCE_REGIME_UNSPECIFIED = 'COMPLIANCE_REGIME_UNSPECIFIED';
  /**
   * Information protection as per DoD IL4 requirements.
   */
  public const COMPLIANCE_REGIME_IL4 = 'IL4';
  /**
   * Criminal Justice Information Services (CJIS) Security policies.
   */
  public const COMPLIANCE_REGIME_CJIS = 'CJIS';
  /**
   * FedRAMP High data protection controls
   */
  public const COMPLIANCE_REGIME_FEDRAMP_HIGH = 'FEDRAMP_HIGH';
  /**
   * FedRAMP Moderate data protection controls
   */
  public const COMPLIANCE_REGIME_FEDRAMP_MODERATE = 'FEDRAMP_MODERATE';
  /**
   * Assured Workloads For US Regions data protection controls
   */
  public const COMPLIANCE_REGIME_US_REGIONAL_ACCESS = 'US_REGIONAL_ACCESS';
  /**
   * [DEPRECATED] Health Insurance Portability and Accountability Act controls
   *
   * @deprecated
   */
  public const COMPLIANCE_REGIME_HIPAA = 'HIPAA';
  /**
   * [DEPRECATED] Health Information Trust Alliance controls
   *
   * @deprecated
   */
  public const COMPLIANCE_REGIME_HITRUST = 'HITRUST';
  /**
   * Assured Workloads For EU Regions and Support controls
   */
  public const COMPLIANCE_REGIME_EU_REGIONS_AND_SUPPORT = 'EU_REGIONS_AND_SUPPORT';
  /**
   * Assured Workloads For Canada Regions and Support controls
   */
  public const COMPLIANCE_REGIME_CA_REGIONS_AND_SUPPORT = 'CA_REGIONS_AND_SUPPORT';
  /**
   * International Traffic in Arms Regulations
   */
  public const COMPLIANCE_REGIME_ITAR = 'ITAR';
  /**
   * Assured Workloads for Australia Regions and Support controls
   */
  public const COMPLIANCE_REGIME_AU_REGIONS_AND_US_SUPPORT = 'AU_REGIONS_AND_US_SUPPORT';
  /**
   * Assured Workloads for Partners;
   */
  public const COMPLIANCE_REGIME_ASSURED_WORKLOADS_FOR_PARTNERS = 'ASSURED_WORKLOADS_FOR_PARTNERS';
  /**
   * Assured Workloads for Israel Regions
   */
  public const COMPLIANCE_REGIME_ISR_REGIONS = 'ISR_REGIONS';
  /**
   * Assured Workloads for Israel Regions
   */
  public const COMPLIANCE_REGIME_ISR_REGIONS_AND_SUPPORT = 'ISR_REGIONS_AND_SUPPORT';
  /**
   * Assured Workloads for Canada Protected B regime
   */
  public const COMPLIANCE_REGIME_CA_PROTECTED_B = 'CA_PROTECTED_B';
  /**
   * Information protection as per DoD IL5 requirements.
   */
  public const COMPLIANCE_REGIME_IL5 = 'IL5';
  /**
   * Information protection as per DoD IL2 requirements.
   */
  public const COMPLIANCE_REGIME_IL2 = 'IL2';
  /**
   * Assured Workloads for Japan Regions
   */
  public const COMPLIANCE_REGIME_JP_REGIONS_AND_SUPPORT = 'JP_REGIONS_AND_SUPPORT';
  /**
   * Assured Workloads Sovereign Controls KSA
   */
  public const COMPLIANCE_REGIME_KSA_REGIONS_AND_SUPPORT_WITH_SOVEREIGNTY_CONTROLS = 'KSA_REGIONS_AND_SUPPORT_WITH_SOVEREIGNTY_CONTROLS';
  /**
   * Assured Workloads for Regional Controls
   */
  public const COMPLIANCE_REGIME_REGIONAL_CONTROLS = 'REGIONAL_CONTROLS';
  /**
   * Healthcare and Life Science Controls
   */
  public const COMPLIANCE_REGIME_HEALTHCARE_AND_LIFE_SCIENCES_CONTROLS = 'HEALTHCARE_AND_LIFE_SCIENCES_CONTROLS';
  /**
   * Healthcare and Life Science Controls with US Support
   */
  public const COMPLIANCE_REGIME_HEALTHCARE_AND_LIFE_SCIENCES_CONTROLS_US_SUPPORT = 'HEALTHCARE_AND_LIFE_SCIENCES_CONTROLS_US_SUPPORT';
  /**
   * Internal Revenue Service 1075 controls
   */
  public const COMPLIANCE_REGIME_IRS_1075 = 'IRS_1075';
  /**
   * Canada Controlled Goods
   */
  public const COMPLIANCE_REGIME_CANADA_CONTROLLED_GOODS = 'CANADA_CONTROLLED_GOODS';
  /**
   * Australia Data Boundary and Support
   */
  public const COMPLIANCE_REGIME_AUSTRALIA_DATA_BOUNDARY_AND_SUPPORT = 'AUSTRALIA_DATA_BOUNDARY_AND_SUPPORT';
  /**
   * Canada Data Boundary and Support
   */
  public const COMPLIANCE_REGIME_CANADA_DATA_BOUNDARY_AND_SUPPORT = 'CANADA_DATA_BOUNDARY_AND_SUPPORT';
  /**
   * Data Boundary for Canada Controlled Goods
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_CANADA_CONTROLLED_GOODS = 'DATA_BOUNDARY_FOR_CANADA_CONTROLLED_GOODS';
  /**
   * Data Boundary for Canada Protected B
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_CANADA_PROTECTED_B = 'DATA_BOUNDARY_FOR_CANADA_PROTECTED_B';
  /**
   * Data Boundary for CJIS
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_CJIS = 'DATA_BOUNDARY_FOR_CJIS';
  /**
   * Data Boundary for FedRAMP High
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_FEDRAMP_HIGH = 'DATA_BOUNDARY_FOR_FEDRAMP_HIGH';
  /**
   * Data Boundary for FedRAMP Moderate
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_FEDRAMP_MODERATE = 'DATA_BOUNDARY_FOR_FEDRAMP_MODERATE';
  /**
   * Data Boundary for IL2
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_IL2 = 'DATA_BOUNDARY_FOR_IL2';
  /**
   * Data Boundary for IL4
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_IL4 = 'DATA_BOUNDARY_FOR_IL4';
  /**
   * Data Boundary for IL5
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_IL5 = 'DATA_BOUNDARY_FOR_IL5';
  /**
   * Data Boundary for IRS Publication 1075
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_IRS_PUBLICATION_1075 = 'DATA_BOUNDARY_FOR_IRS_PUBLICATION_1075';
  /**
   * Data Boundary for ITAR
   */
  public const COMPLIANCE_REGIME_DATA_BOUNDARY_FOR_ITAR = 'DATA_BOUNDARY_FOR_ITAR';
  /**
   * Data Boundary for EU Regions and Support
   */
  public const COMPLIANCE_REGIME_EU_DATA_BOUNDARY_AND_SUPPORT = 'EU_DATA_BOUNDARY_AND_SUPPORT';
  /**
   * Data Boundary for Israel Regions
   */
  public const COMPLIANCE_REGIME_ISRAEL_DATA_BOUNDARY_AND_SUPPORT = 'ISRAEL_DATA_BOUNDARY_AND_SUPPORT';
  /**
   * Data Boundary for US Regions and Support
   */
  public const COMPLIANCE_REGIME_US_DATA_BOUNDARY_AND_SUPPORT = 'US_DATA_BOUNDARY_AND_SUPPORT';
  /**
   * Data Boundary for US Healthcare and Life Sciences
   */
  public const COMPLIANCE_REGIME_US_DATA_BOUNDARY_FOR_HEALTHCARE_AND_LIFE_SCIENCES = 'US_DATA_BOUNDARY_FOR_HEALTHCARE_AND_LIFE_SCIENCES';
  /**
   * Data Boundary for US Healthcare and Life Sciences with Support
   */
  public const COMPLIANCE_REGIME_US_DATA_BOUNDARY_FOR_HEALTHCARE_AND_LIFE_SCIENCES_WITH_SUPPORT = 'US_DATA_BOUNDARY_FOR_HEALTHCARE_AND_LIFE_SCIENCES_WITH_SUPPORT';
  /**
   * KSA Data Boundary with Access Justifications
   */
  public const COMPLIANCE_REGIME_KSA_DATA_BOUNDARY_WITH_ACCESS_JUSTIFICATIONS = 'KSA_DATA_BOUNDARY_WITH_ACCESS_JUSTIFICATIONS';
  /**
   * Regional Data Boundary
   */
  public const COMPLIANCE_REGIME_REGIONAL_DATA_BOUNDARY = 'REGIONAL_DATA_BOUNDARY';
  /**
   * JAPAN Data Boundary
   */
  public const COMPLIANCE_REGIME_JAPAN_DATA_BOUNDARY = 'JAPAN_DATA_BOUNDARY';
  /**
   * Optional. Compliance controls that should be applied to the resources
   * managed by the workload.
   *
   * @var string
   */
  public $complianceRegime;
  /**
   * Optional. Time when the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The display name of the workload.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. The parent of the workload.
   *
   * @var string
   */
  public $parent;

  /**
   * Optional. Compliance controls that should be applied to the resources
   * managed by the workload.
   *
   * Accepted values: COMPLIANCE_REGIME_UNSPECIFIED, IL4, CJIS, FEDRAMP_HIGH,
   * FEDRAMP_MODERATE, US_REGIONAL_ACCESS, HIPAA, HITRUST,
   * EU_REGIONS_AND_SUPPORT, CA_REGIONS_AND_SUPPORT, ITAR,
   * AU_REGIONS_AND_US_SUPPORT, ASSURED_WORKLOADS_FOR_PARTNERS, ISR_REGIONS,
   * ISR_REGIONS_AND_SUPPORT, CA_PROTECTED_B, IL5, IL2, JP_REGIONS_AND_SUPPORT,
   * KSA_REGIONS_AND_SUPPORT_WITH_SOVEREIGNTY_CONTROLS, REGIONAL_CONTROLS,
   * HEALTHCARE_AND_LIFE_SCIENCES_CONTROLS,
   * HEALTHCARE_AND_LIFE_SCIENCES_CONTROLS_US_SUPPORT, IRS_1075,
   * CANADA_CONTROLLED_GOODS, AUSTRALIA_DATA_BOUNDARY_AND_SUPPORT,
   * CANADA_DATA_BOUNDARY_AND_SUPPORT,
   * DATA_BOUNDARY_FOR_CANADA_CONTROLLED_GOODS,
   * DATA_BOUNDARY_FOR_CANADA_PROTECTED_B, DATA_BOUNDARY_FOR_CJIS,
   * DATA_BOUNDARY_FOR_FEDRAMP_HIGH, DATA_BOUNDARY_FOR_FEDRAMP_MODERATE,
   * DATA_BOUNDARY_FOR_IL2, DATA_BOUNDARY_FOR_IL4, DATA_BOUNDARY_FOR_IL5,
   * DATA_BOUNDARY_FOR_IRS_PUBLICATION_1075, DATA_BOUNDARY_FOR_ITAR,
   * EU_DATA_BOUNDARY_AND_SUPPORT, ISRAEL_DATA_BOUNDARY_AND_SUPPORT,
   * US_DATA_BOUNDARY_AND_SUPPORT,
   * US_DATA_BOUNDARY_FOR_HEALTHCARE_AND_LIFE_SCIENCES,
   * US_DATA_BOUNDARY_FOR_HEALTHCARE_AND_LIFE_SCIENCES_WITH_SUPPORT,
   * KSA_DATA_BOUNDARY_WITH_ACCESS_JUSTIFICATIONS, REGIONAL_DATA_BOUNDARY,
   * JAPAN_DATA_BOUNDARY
   *
   * @param self::COMPLIANCE_REGIME_* $complianceRegime
   */
  public function setComplianceRegime($complianceRegime)
  {
    $this->complianceRegime = $complianceRegime;
  }
  /**
   * @return self::COMPLIANCE_REGIME_*
   */
  public function getComplianceRegime()
  {
    return $this->complianceRegime;
  }
  /**
   * Optional. Time when the operation was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The display name of the workload.
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
   * Optional. The parent of the workload.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1CreateWorkloadOperationMetadata::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1CreateWorkloadOperationMetadata');
