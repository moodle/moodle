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

class GooglePrivacyDlpV2VertexDatasetDiscoveryTarget extends \Google\Model
{
  protected $conditionsType = GooglePrivacyDlpV2DiscoveryVertexDatasetConditions::class;
  protected $conditionsDataType = '';
  protected $disabledType = GooglePrivacyDlpV2Disabled::class;
  protected $disabledDataType = '';
  protected $filterType = GooglePrivacyDlpV2DiscoveryVertexDatasetFilter::class;
  protected $filterDataType = '';
  protected $generationCadenceType = GooglePrivacyDlpV2DiscoveryVertexDatasetGenerationCadence::class;
  protected $generationCadenceDataType = '';

  /**
   * In addition to matching the filter, these conditions must be true before a
   * profile is generated.
   *
   * @param GooglePrivacyDlpV2DiscoveryVertexDatasetConditions $conditions
   */
  public function setConditions(GooglePrivacyDlpV2DiscoveryVertexDatasetConditions $conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryVertexDatasetConditions
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Disable profiling for datasets that match this filter.
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
   * Required. The datasets the discovery cadence applies to. The first target
   * with a matching filter will be the one to apply to a dataset.
   *
   * @param GooglePrivacyDlpV2DiscoveryVertexDatasetFilter $filter
   */
  public function setFilter(GooglePrivacyDlpV2DiscoveryVertexDatasetFilter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryVertexDatasetFilter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * How often and when to update profiles. New datasets that match both the
   * filter and conditions are scanned as quickly as possible depending on
   * system capacity.
   *
   * @param GooglePrivacyDlpV2DiscoveryVertexDatasetGenerationCadence $generationCadence
   */
  public function setGenerationCadence(GooglePrivacyDlpV2DiscoveryVertexDatasetGenerationCadence $generationCadence)
  {
    $this->generationCadence = $generationCadence;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryVertexDatasetGenerationCadence
   */
  public function getGenerationCadence()
  {
    return $this->generationCadence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2VertexDatasetDiscoveryTarget::class, 'Google_Service_DLP_GooglePrivacyDlpV2VertexDatasetDiscoveryTarget');
