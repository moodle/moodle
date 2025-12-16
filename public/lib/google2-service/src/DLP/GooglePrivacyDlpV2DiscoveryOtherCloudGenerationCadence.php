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

class GooglePrivacyDlpV2DiscoveryOtherCloudGenerationCadence extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const REFRESH_FREQUENCY_UPDATE_FREQUENCY_UNSPECIFIED = 'UPDATE_FREQUENCY_UNSPECIFIED';
  /**
   * After the data profile is created, it will never be updated.
   */
  public const REFRESH_FREQUENCY_UPDATE_FREQUENCY_NEVER = 'UPDATE_FREQUENCY_NEVER';
  /**
   * The data profile can be updated up to once every 24 hours.
   */
  public const REFRESH_FREQUENCY_UPDATE_FREQUENCY_DAILY = 'UPDATE_FREQUENCY_DAILY';
  /**
   * The data profile can be updated up to once every 30 days. Default.
   */
  public const REFRESH_FREQUENCY_UPDATE_FREQUENCY_MONTHLY = 'UPDATE_FREQUENCY_MONTHLY';
  protected $inspectTemplateModifiedCadenceType = GooglePrivacyDlpV2DiscoveryInspectTemplateModifiedCadence::class;
  protected $inspectTemplateModifiedCadenceDataType = '';
  /**
   * Optional. Frequency to update profiles regardless of whether the underlying
   * resource has changes. Defaults to never.
   *
   * @var string
   */
  public $refreshFrequency;

  /**
   * Optional. Governs when to update data profiles when the inspection rules
   * defined by the `InspectTemplate` change. If not set, changing the template
   * will not cause a data profile to update.
   *
   * @param GooglePrivacyDlpV2DiscoveryInspectTemplateModifiedCadence $inspectTemplateModifiedCadence
   */
  public function setInspectTemplateModifiedCadence(GooglePrivacyDlpV2DiscoveryInspectTemplateModifiedCadence $inspectTemplateModifiedCadence)
  {
    $this->inspectTemplateModifiedCadence = $inspectTemplateModifiedCadence;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryInspectTemplateModifiedCadence
   */
  public function getInspectTemplateModifiedCadence()
  {
    return $this->inspectTemplateModifiedCadence;
  }
  /**
   * Optional. Frequency to update profiles regardless of whether the underlying
   * resource has changes. Defaults to never.
   *
   * Accepted values: UPDATE_FREQUENCY_UNSPECIFIED, UPDATE_FREQUENCY_NEVER,
   * UPDATE_FREQUENCY_DAILY, UPDATE_FREQUENCY_MONTHLY
   *
   * @param self::REFRESH_FREQUENCY_* $refreshFrequency
   */
  public function setRefreshFrequency($refreshFrequency)
  {
    $this->refreshFrequency = $refreshFrequency;
  }
  /**
   * @return self::REFRESH_FREQUENCY_*
   */
  public function getRefreshFrequency()
  {
    return $this->refreshFrequency;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryOtherCloudGenerationCadence::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryOtherCloudGenerationCadence');
