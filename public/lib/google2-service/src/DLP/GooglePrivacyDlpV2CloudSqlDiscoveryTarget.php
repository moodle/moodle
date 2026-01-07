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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2CloudSqlDiscoveryTarget extends \Google\Model
{
  protected $conditionsType = GooglePrivacyDlpV2DiscoveryCloudSqlConditions::class;
  protected $conditionsDataType = '';
  protected $disabledType = GooglePrivacyDlpV2Disabled::class;
  protected $disabledDataType = '';
  protected $filterType = GooglePrivacyDlpV2DiscoveryCloudSqlFilter::class;
  protected $filterDataType = '';
  protected $generationCadenceType = GooglePrivacyDlpV2DiscoveryCloudSqlGenerationCadence::class;
  protected $generationCadenceDataType = '';

  /**
   * In addition to matching the filter, these conditions must be true before a
   * profile is generated.
   *
   * @param GooglePrivacyDlpV2DiscoveryCloudSqlConditions $conditions
   */
  public function setConditions(GooglePrivacyDlpV2DiscoveryCloudSqlConditions $conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryCloudSqlConditions
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Disable profiling for database resources that match this filter.
   *
   * @param GooglePrivacyDlpV2Disabled $disabled
   */
  public function setDisabled(GooglePrivacyDlpV2Disabled $disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return GooglePrivacyDlpV2Disabled
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Required. The tables the discovery cadence applies to. The first target
   * with a matching filter will be the one to apply to a table.
   *
   * @param GooglePrivacyDlpV2DiscoveryCloudSqlFilter $filter
   */
  public function setFilter(GooglePrivacyDlpV2DiscoveryCloudSqlFilter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryCloudSqlFilter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * How often and when to update profiles. New tables that match both the
   * filter and conditions are scanned as quickly as possible depending on
   * system capacity.
   *
   * @param GooglePrivacyDlpV2DiscoveryCloudSqlGenerationCadence $generationCadence
   */
  public function setGenerationCadence(GooglePrivacyDlpV2DiscoveryCloudSqlGenerationCadence $generationCadence)
  {
    $this->generationCadence = $generationCadence;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryCloudSqlGenerationCadence
   */
  public function getGenerationCadence()
  {
    return $this->generationCadence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CloudSqlDiscoveryTarget::class, 'Google_Service_DLP_GooglePrivacyDlpV2CloudSqlDiscoveryTarget');
