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

class GooglePrivacyDlpV2DiscoveryInspectTemplateModifiedCadence extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const FREQUENCY_UPDATE_FREQUENCY_UNSPECIFIED = 'UPDATE_FREQUENCY_UNSPECIFIED';
  /**
   * After the data profile is created, it will never be updated.
   */
  public const FREQUENCY_UPDATE_FREQUENCY_NEVER = 'UPDATE_FREQUENCY_NEVER';
  /**
   * The data profile can be updated up to once every 24 hours.
   */
  public const FREQUENCY_UPDATE_FREQUENCY_DAILY = 'UPDATE_FREQUENCY_DAILY';
  /**
   * The data profile can be updated up to once every 30 days. Default.
   */
  public const FREQUENCY_UPDATE_FREQUENCY_MONTHLY = 'UPDATE_FREQUENCY_MONTHLY';
  /**
   * How frequently data profiles can be updated when the template is modified.
   * Defaults to never.
   *
   * @var string
   */
  public $frequency;

  /**
   * How frequently data profiles can be updated when the template is modified.
   * Defaults to never.
   *
   * Accepted values: UPDATE_FREQUENCY_UNSPECIFIED, UPDATE_FREQUENCY_NEVER,
   * UPDATE_FREQUENCY_DAILY, UPDATE_FREQUENCY_MONTHLY
   *
   * @param self::FREQUENCY_* $frequency
   */
  public function setFrequency($frequency)
  {
    $this->frequency = $frequency;
  }
  /**
   * @return self::FREQUENCY_*
   */
  public function getFrequency()
  {
    return $this->frequency;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryInspectTemplateModifiedCadence::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryInspectTemplateModifiedCadence');
