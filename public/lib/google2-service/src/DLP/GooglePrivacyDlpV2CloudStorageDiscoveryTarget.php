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

class GooglePrivacyDlpV2CloudStorageDiscoveryTarget extends \Google\Model
{
  protected $conditionsType = GooglePrivacyDlpV2DiscoveryFileStoreConditions::class;
  protected $conditionsDataType = '';
  protected $disabledType = GooglePrivacyDlpV2Disabled::class;
  protected $disabledDataType = '';
  protected $filterType = GooglePrivacyDlpV2DiscoveryCloudStorageFilter::class;
  protected $filterDataType = '';
  protected $generationCadenceType = GooglePrivacyDlpV2DiscoveryCloudStorageGenerationCadence::class;
  protected $generationCadenceDataType = '';

  /**
   * Optional. In addition to matching the filter, these conditions must be true
   * before a profile is generated.
   *
   * @param GooglePrivacyDlpV2DiscoveryFileStoreConditions $conditions
   */
  public function setConditions(GooglePrivacyDlpV2DiscoveryFileStoreConditions $conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryFileStoreConditions
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Optional. Disable profiling for buckets that match this filter.
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
   * Required. The buckets the generation_cadence applies to. The first target
   * with a matching filter will be the one to apply to a bucket.
   *
   * @param GooglePrivacyDlpV2DiscoveryCloudStorageFilter $filter
   */
  public function setFilter(GooglePrivacyDlpV2DiscoveryCloudStorageFilter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryCloudStorageFilter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. How often and when to update profiles. New buckets that match
   * both the filter and conditions are scanned as quickly as possible depending
   * on system capacity.
   *
   * @param GooglePrivacyDlpV2DiscoveryCloudStorageGenerationCadence $generationCadence
   */
  public function setGenerationCadence(GooglePrivacyDlpV2DiscoveryCloudStorageGenerationCadence $generationCadence)
  {
    $this->generationCadence = $generationCadence;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryCloudStorageGenerationCadence
   */
  public function getGenerationCadence()
  {
    return $this->generationCadence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CloudStorageDiscoveryTarget::class, 'Google_Service_DLP_GooglePrivacyDlpV2CloudStorageDiscoveryTarget');
