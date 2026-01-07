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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1RiskAssessmentEntry extends \Google\Model
{
  /**
   * Default value when no provider is specified.
   */
  public const PROVIDER_RISK_ASSESSMENT_PROVIDER_UNSPECIFIED = 'RISK_ASSESSMENT_PROVIDER_UNSPECIFIED';
  /**
   * CRXcavator.
   */
  public const PROVIDER_RISK_ASSESSMENT_PROVIDER_CRXCAVATOR = 'RISK_ASSESSMENT_PROVIDER_CRXCAVATOR';
  /**
   * Spin.Ai.
   */
  public const PROVIDER_RISK_ASSESSMENT_PROVIDER_SPIN_AI = 'RISK_ASSESSMENT_PROVIDER_SPIN_AI';
  /**
   * LayerX Security.
   */
  public const PROVIDER_RISK_ASSESSMENT_PROVIDER_LAYERX = 'RISK_ASSESSMENT_PROVIDER_LAYERX';
  /**
   * Risk level not specified.
   */
  public const RISK_LEVEL_RISK_LEVEL_UNSPECIFIED = 'RISK_LEVEL_UNSPECIFIED';
  /**
   * Extension that represents a low risk.
   */
  public const RISK_LEVEL_RISK_LEVEL_LOW = 'RISK_LEVEL_LOW';
  /**
   * Extension that represents a medium risk.
   */
  public const RISK_LEVEL_RISK_LEVEL_MEDIUM = 'RISK_LEVEL_MEDIUM';
  /**
   * Extension that represents a high risk.
   */
  public const RISK_LEVEL_RISK_LEVEL_HIGH = 'RISK_LEVEL_HIGH';
  /**
   * Output only. The risk assessment provider from which this entry comes from.
   *
   * @var string
   */
  public $provider;
  protected $riskAssessmentType = GoogleChromeManagementV1RiskAssessment::class;
  protected $riskAssessmentDataType = '';
  /**
   * Output only. The bucketed risk level for the risk assessment.
   *
   * @var string
   */
  public $riskLevel;

  /**
   * Output only. The risk assessment provider from which this entry comes from.
   *
   * Accepted values: RISK_ASSESSMENT_PROVIDER_UNSPECIFIED,
   * RISK_ASSESSMENT_PROVIDER_CRXCAVATOR, RISK_ASSESSMENT_PROVIDER_SPIN_AI,
   * RISK_ASSESSMENT_PROVIDER_LAYERX
   *
   * @param self::PROVIDER_* $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return self::PROVIDER_*
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Output only. The details of the provider's risk assessment.
   *
   * @param GoogleChromeManagementV1RiskAssessment $riskAssessment
   */
  public function setRiskAssessment(GoogleChromeManagementV1RiskAssessment $riskAssessment)
  {
    $this->riskAssessment = $riskAssessment;
  }
  /**
   * @return GoogleChromeManagementV1RiskAssessment
   */
  public function getRiskAssessment()
  {
    return $this->riskAssessment;
  }
  /**
   * Output only. The bucketed risk level for the risk assessment.
   *
   * Accepted values: RISK_LEVEL_UNSPECIFIED, RISK_LEVEL_LOW, RISK_LEVEL_MEDIUM,
   * RISK_LEVEL_HIGH
   *
   * @param self::RISK_LEVEL_* $riskLevel
   */
  public function setRiskLevel($riskLevel)
  {
    $this->riskLevel = $riskLevel;
  }
  /**
   * @return self::RISK_LEVEL_*
   */
  public function getRiskLevel()
  {
    return $this->riskLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1RiskAssessmentEntry::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1RiskAssessmentEntry');
