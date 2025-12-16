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

class GoogleCloudDiscoveryengineV1EngineSearchEngineConfig extends \Google\Collection
{
  /**
   * Default value.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_UNSPECIFIED = 'SUBSCRIPTION_TIER_UNSPECIFIED';
  /**
   * Search tier. Search tier can access VAIS search features and NotebookLM
   * features.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_SEARCH = 'SUBSCRIPTION_TIER_SEARCH';
  /**
   * Search + assistant tier. Search + assistant tier can access VAIS search
   * features, NotebookLM features and assistant features.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_SEARCH_AND_ASSISTANT = 'SUBSCRIPTION_TIER_SEARCH_AND_ASSISTANT';
  /**
   * NotebookLM tier. NotebookLM is a subscription tier can only access
   * NotebookLM features.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_NOTEBOOK_LM = 'SUBSCRIPTION_TIER_NOTEBOOK_LM';
  /**
   * Frontline worker tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_FRONTLINE_WORKER = 'SUBSCRIPTION_TIER_FRONTLINE_WORKER';
  /**
   * Agentspace Starter tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_AGENTSPACE_STARTER = 'SUBSCRIPTION_TIER_AGENTSPACE_STARTER';
  /**
   * Agentspace Business tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_AGENTSPACE_BUSINESS = 'SUBSCRIPTION_TIER_AGENTSPACE_BUSINESS';
  /**
   * Enterprise tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_ENTERPRISE = 'SUBSCRIPTION_TIER_ENTERPRISE';
  /**
   * EDU tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_EDU = 'SUBSCRIPTION_TIER_EDU';
  /**
   * EDU Pro tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_EDU_PRO = 'SUBSCRIPTION_TIER_EDU_PRO';
  /**
   * EDU emerging market tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_EDU_EMERGING = 'SUBSCRIPTION_TIER_EDU_EMERGING';
  /**
   * EDU Pro emerging market tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_EDU_PRO_EMERGING = 'SUBSCRIPTION_TIER_EDU_PRO_EMERGING';
  /**
   * Frontline starter tier.
   */
  public const REQUIRED_SUBSCRIPTION_TIER_SUBSCRIPTION_TIER_FRONTLINE_STARTER = 'SUBSCRIPTION_TIER_FRONTLINE_STARTER';
  /**
   * Default value when the enum is unspecified. This is invalid to use.
   */
  public const SEARCH_TIER_SEARCH_TIER_UNSPECIFIED = 'SEARCH_TIER_UNSPECIFIED';
  /**
   * Standard tier.
   */
  public const SEARCH_TIER_SEARCH_TIER_STANDARD = 'SEARCH_TIER_STANDARD';
  /**
   * Enterprise tier.
   */
  public const SEARCH_TIER_SEARCH_TIER_ENTERPRISE = 'SEARCH_TIER_ENTERPRISE';
  protected $collection_key = 'searchAddOns';
  /**
   * Optional. The required subscription tier of this engine. They cannot be
   * modified after engine creation. If the required subscription tier is
   * search, user with higher license tier like assist can still access the
   * standalone app associated with this engine.
   *
   * @var string
   */
  public $requiredSubscriptionTier;
  /**
   * The add-on that this search engine enables.
   *
   * @var string[]
   */
  public $searchAddOns;
  /**
   * The search feature tier of this engine. Different tiers might have
   * different pricing. To learn more, check the pricing documentation. Defaults
   * to SearchTier.SEARCH_TIER_STANDARD if not specified.
   *
   * @var string
   */
  public $searchTier;

  /**
   * Optional. The required subscription tier of this engine. They cannot be
   * modified after engine creation. If the required subscription tier is
   * search, user with higher license tier like assist can still access the
   * standalone app associated with this engine.
   *
   * Accepted values: SUBSCRIPTION_TIER_UNSPECIFIED, SUBSCRIPTION_TIER_SEARCH,
   * SUBSCRIPTION_TIER_SEARCH_AND_ASSISTANT, SUBSCRIPTION_TIER_NOTEBOOK_LM,
   * SUBSCRIPTION_TIER_FRONTLINE_WORKER, SUBSCRIPTION_TIER_AGENTSPACE_STARTER,
   * SUBSCRIPTION_TIER_AGENTSPACE_BUSINESS, SUBSCRIPTION_TIER_ENTERPRISE,
   * SUBSCRIPTION_TIER_EDU, SUBSCRIPTION_TIER_EDU_PRO,
   * SUBSCRIPTION_TIER_EDU_EMERGING, SUBSCRIPTION_TIER_EDU_PRO_EMERGING,
   * SUBSCRIPTION_TIER_FRONTLINE_STARTER
   *
   * @param self::REQUIRED_SUBSCRIPTION_TIER_* $requiredSubscriptionTier
   */
  public function setRequiredSubscriptionTier($requiredSubscriptionTier)
  {
    $this->requiredSubscriptionTier = $requiredSubscriptionTier;
  }
  /**
   * @return self::REQUIRED_SUBSCRIPTION_TIER_*
   */
  public function getRequiredSubscriptionTier()
  {
    return $this->requiredSubscriptionTier;
  }
  /**
   * The add-on that this search engine enables.
   *
   * @param string[] $searchAddOns
   */
  public function setSearchAddOns($searchAddOns)
  {
    $this->searchAddOns = $searchAddOns;
  }
  /**
   * @return string[]
   */
  public function getSearchAddOns()
  {
    return $this->searchAddOns;
  }
  /**
   * The search feature tier of this engine. Different tiers might have
   * different pricing. To learn more, check the pricing documentation. Defaults
   * to SearchTier.SEARCH_TIER_STANDARD if not specified.
   *
   * Accepted values: SEARCH_TIER_UNSPECIFIED, SEARCH_TIER_STANDARD,
   * SEARCH_TIER_ENTERPRISE
   *
   * @param self::SEARCH_TIER_* $searchTier
   */
  public function setSearchTier($searchTier)
  {
    $this->searchTier = $searchTier;
  }
  /**
   * @return self::SEARCH_TIER_*
   */
  public function getSearchTier()
  {
    return $this->searchTier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1EngineSearchEngineConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1EngineSearchEngineConfig');
