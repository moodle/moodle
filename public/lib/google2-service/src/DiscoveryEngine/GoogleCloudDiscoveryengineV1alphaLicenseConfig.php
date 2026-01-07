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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaLicenseConfig extends \Google\Model
{
  /**
   * Default value. The license config does not exist.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The license config is effective and being used.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The license config has expired.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  /**
   * The license config has not started yet, and its start date is in the
   * future.
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * Default value, do not use.
   */
  public const SUBSCRIPTION_TERM_SUBSCRIPTION_TERM_UNSPECIFIED = 'SUBSCRIPTION_TERM_UNSPECIFIED';
  /**
   * 1 month.
   */
  public const SUBSCRIPTION_TERM_SUBSCRIPTION_TERM_ONE_MONTH = 'SUBSCRIPTION_TERM_ONE_MONTH';
  /**
   * 1 year.
   */
  public const SUBSCRIPTION_TERM_SUBSCRIPTION_TERM_ONE_YEAR = 'SUBSCRIPTION_TERM_ONE_YEAR';
  /**
   * 3 years.
   */
  public const SUBSCRIPTION_TERM_SUBSCRIPTION_TERM_THREE_YEARS = 'SUBSCRIPTION_TERM_THREE_YEARS';
  /**
   * Default value.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_UNSPECIFIED = 'SUBSCRIPTION_TIER_UNSPECIFIED';
  /**
   * Search tier. Search tier can access VAIS search features and NotebookLM
   * features.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_SEARCH = 'SUBSCRIPTION_TIER_SEARCH';
  /**
   * Search + assistant tier. Search + assistant tier can access VAIS search
   * features, NotebookLM features and assistant features.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_SEARCH_AND_ASSISTANT = 'SUBSCRIPTION_TIER_SEARCH_AND_ASSISTANT';
  /**
   * NotebookLM tier. NotebookLM is a subscription tier can only access
   * NotebookLM features.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_NOTEBOOK_LM = 'SUBSCRIPTION_TIER_NOTEBOOK_LM';
  /**
   * Frontline worker tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_FRONTLINE_WORKER = 'SUBSCRIPTION_TIER_FRONTLINE_WORKER';
  /**
   * Agentspace Starter tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_AGENTSPACE_STARTER = 'SUBSCRIPTION_TIER_AGENTSPACE_STARTER';
  /**
   * Agentspace Business tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_AGENTSPACE_BUSINESS = 'SUBSCRIPTION_TIER_AGENTSPACE_BUSINESS';
  /**
   * Enterprise tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_ENTERPRISE = 'SUBSCRIPTION_TIER_ENTERPRISE';
  /**
   * EDU tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_EDU = 'SUBSCRIPTION_TIER_EDU';
  /**
   * EDU Pro tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_EDU_PRO = 'SUBSCRIPTION_TIER_EDU_PRO';
  /**
   * EDU emerging market tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_EDU_EMERGING = 'SUBSCRIPTION_TIER_EDU_EMERGING';
  /**
   * EDU Pro emerging market tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_EDU_PRO_EMERGING = 'SUBSCRIPTION_TIER_EDU_PRO_EMERGING';
  /**
   * Frontline starter tier.
   */
  public const SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_FRONTLINE_STARTER = 'SUBSCRIPTION_TIER_FRONTLINE_STARTER';
  protected $alertPolicyResourceConfigType = GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfig::class;
  protected $alertPolicyResourceConfigDataType = '';
  /**
   * Optional. Whether the license config should be auto renewed when it reaches
   * the end date.
   *
   * @var bool
   */
  public $autoRenew;
  protected $endDateType = GoogleTypeDate::class;
  protected $endDateDataType = '';
  /**
   * Optional. Whether the license config is for free trial.
   *
   * @var bool
   */
  public $freeTrial;
  /**
   * Output only. Whether the license config is for Gemini bundle.
   *
   * @var bool
   */
  public $geminiBundle;
  /**
   * Required. Number of licenses purchased.
   *
   * @var string
   */
  public $licenseCount;
  /**
   * Immutable. Identifier. The fully qualified resource name of the license
   * config. Format:
   * `projects/{project}/locations/{location}/licenseConfigs/{license_config}`
   *
   * @var string
   */
  public $name;
  protected $startDateType = GoogleTypeDate::class;
  protected $startDateDataType = '';
  /**
   * Output only. The state of the license config.
   *
   * @var string
   */
  public $state;
  /**
   * Required. Subscription term.
   *
   * @var string
   */
  public $subscriptionTerm;
  /**
   * Required. Subscription tier information for the license config.
   *
   * @var string
   */
  public $subscriptionTier;

  /**
   * Optional. The alert policy config for this license config.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfig $alertPolicyResourceConfig
   */
  public function setAlertPolicyResourceConfig(GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfig $alertPolicyResourceConfig)
  {
    $this->alertPolicyResourceConfig = $alertPolicyResourceConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAlertPolicyResourceConfig
   */
  public function getAlertPolicyResourceConfig()
  {
    return $this->alertPolicyResourceConfig;
  }
  /**
   * Optional. Whether the license config should be auto renewed when it reaches
   * the end date.
   *
   * @param bool $autoRenew
   */
  public function setAutoRenew($autoRenew)
  {
    $this->autoRenew = $autoRenew;
  }
  /**
   * @return bool
   */
  public function getAutoRenew()
  {
    return $this->autoRenew;
  }
  /**
   * Optional. The planed end date.
   *
   * @param GoogleTypeDate $endDate
   */
  public function setEndDate(GoogleTypeDate $endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Optional. Whether the license config is for free trial.
   *
   * @param bool $freeTrial
   */
  public function setFreeTrial($freeTrial)
  {
    $this->freeTrial = $freeTrial;
  }
  /**
   * @return bool
   */
  public function getFreeTrial()
  {
    return $this->freeTrial;
  }
  /**
   * Output only. Whether the license config is for Gemini bundle.
   *
   * @param bool $geminiBundle
   */
  public function setGeminiBundle($geminiBundle)
  {
    $this->geminiBundle = $geminiBundle;
  }
  /**
   * @return bool
   */
  public function getGeminiBundle()
  {
    return $this->geminiBundle;
  }
  /**
   * Required. Number of licenses purchased.
   *
   * @param string $licenseCount
   */
  public function setLicenseCount($licenseCount)
  {
    $this->licenseCount = $licenseCount;
  }
  /**
   * @return string
   */
  public function getLicenseCount()
  {
    return $this->licenseCount;
  }
  /**
   * Immutable. Identifier. The fully qualified resource name of the license
   * config. Format:
   * `projects/{project}/locations/{location}/licenseConfigs/{license_config}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The start date.
   *
   * @param GoogleTypeDate $startDate
   */
  public function setStartDate(GoogleTypeDate $startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Output only. The state of the license config.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, EXPIRED, NOT_STARTED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Required. Subscription term.
   *
   * Accepted values: SUBSCRIPTION_TERM_UNSPECIFIED,
   * SUBSCRIPTION_TERM_ONE_MONTH, SUBSCRIPTION_TERM_ONE_YEAR,
   * SUBSCRIPTION_TERM_THREE_YEARS
   *
   * @param self::SUBSCRIPTION_TERM_* $subscriptionTerm
   */
  public function setSubscriptionTerm($subscriptionTerm)
  {
    $this->subscriptionTerm = $subscriptionTerm;
  }
  /**
   * @return self::SUBSCRIPTION_TERM_*
   */
  public function getSubscriptionTerm()
  {
    return $this->subscriptionTerm;
  }
  /**
   * Required. Subscription tier information for the license config.
   *
   * Accepted values: SUBSCRIPTION_TIER_UNSPECIFIED, SUBSCRIPTION_TIER_SEARCH,
   * SUBSCRIPTION_TIER_SEARCH_AND_ASSISTANT, SUBSCRIPTION_TIER_NOTEBOOK_LM,
   * SUBSCRIPTION_TIER_FRONTLINE_WORKER, SUBSCRIPTION_TIER_AGENTSPACE_STARTER,
   * SUBSCRIPTION_TIER_AGENTSPACE_BUSINESS, SUBSCRIPTION_TIER_ENTERPRISE,
   * SUBSCRIPTION_TIER_EDU, SUBSCRIPTION_TIER_EDU_PRO,
   * SUBSCRIPTION_TIER_EDU_EMERGING, SUBSCRIPTION_TIER_EDU_PRO_EMERGING,
   * SUBSCRIPTION_TIER_FRONTLINE_STARTER
   *
   * @param self::SUBSCRIPTION_TIER_* $subscriptionTier
   */
  public function setSubscriptionTier($subscriptionTier)
  {
    $this->subscriptionTier = $subscriptionTier;
  }
  /**
   * @return self::SUBSCRIPTION_TIER_*
   */
  public function getSubscriptionTier()
  {
    return $this->subscriptionTier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaLicenseConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaLicenseConfig');
