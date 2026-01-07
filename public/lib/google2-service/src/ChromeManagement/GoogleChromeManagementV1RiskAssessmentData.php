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

class GoogleChromeManagementV1RiskAssessmentData extends \Google\Collection
{
  /**
   * Risk level not specified.
   */
  public const OVERALL_RISK_LEVEL_RISK_LEVEL_UNSPECIFIED = 'RISK_LEVEL_UNSPECIFIED';
  /**
   * Extension that represents a low risk.
   */
  public const OVERALL_RISK_LEVEL_RISK_LEVEL_LOW = 'RISK_LEVEL_LOW';
  /**
   * Extension that represents a medium risk.
   */
  public const OVERALL_RISK_LEVEL_RISK_LEVEL_MEDIUM = 'RISK_LEVEL_MEDIUM';
  /**
   * Extension that represents a high risk.
   */
  public const OVERALL_RISK_LEVEL_RISK_LEVEL_HIGH = 'RISK_LEVEL_HIGH';
  protected $collection_key = 'entries';
  protected $entriesType = GoogleChromeManagementV1RiskAssessmentEntry::class;
  protected $entriesDataType = 'array';
  /**
   * Overall assessed risk level across all entries. This will be the highest
   * risk level from all entries.
   *
   * @var string
   */
  public $overallRiskLevel;

  /**
   * Individual risk assessments.
   *
   * @param GoogleChromeManagementV1RiskAssessmentEntry[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return GoogleChromeManagementV1RiskAssessmentEntry[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
  /**
   * Overall assessed risk level across all entries. This will be the highest
   * risk level from all entries.
   *
   * Accepted values: RISK_LEVEL_UNSPECIFIED, RISK_LEVEL_LOW, RISK_LEVEL_MEDIUM,
   * RISK_LEVEL_HIGH
   *
   * @param self::OVERALL_RISK_LEVEL_* $overallRiskLevel
   */
  public function setOverallRiskLevel($overallRiskLevel)
  {
    $this->overallRiskLevel = $overallRiskLevel;
  }
  /**
   * @return self::OVERALL_RISK_LEVEL_*
   */
  public function getOverallRiskLevel()
  {
    return $this->overallRiskLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1RiskAssessmentData::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1RiskAssessmentData');
