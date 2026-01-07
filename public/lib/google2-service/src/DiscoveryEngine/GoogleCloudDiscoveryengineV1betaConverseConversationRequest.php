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

class GoogleCloudDiscoveryengineV1betaConverseConversationRequest extends \Google\Model
{
  protected $boostSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  protected $conversationType = GoogleCloudDiscoveryengineV1betaConversation::class;
  protected $conversationDataType = '';
  /**
   * @var string
   */
  public $filter;
  protected $queryType = GoogleCloudDiscoveryengineV1betaTextInput::class;
  protected $queryDataType = '';
  /**
   * @var bool
   */
  public $safeSearch;
  /**
   * @var string
   */
  public $servingConfig;
  protected $summarySpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec::class;
  protected $summarySpecDataType = '';
  /**
   * @var string[]
   */
  public $userLabels;

  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec
   */
  public function setBoostSpec(GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaConversation
   */
  public function setConversation(GoogleCloudDiscoveryengineV1betaConversation $conversation)
  {
    $this->conversation = $conversation;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaConversation
   */
  public function getConversation()
  {
    return $this->conversation;
  }
  /**
   * @param string
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaTextInput
   */
  public function setQuery(GoogleCloudDiscoveryengineV1betaTextInput $query)
  {
    $this->query = $query;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaTextInput
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * @param bool
   */
  public function setSafeSearch($safeSearch)
  {
    $this->safeSearch = $safeSearch;
  }
  /**
   * @return bool
   */
  public function getSafeSearch()
  {
    return $this->safeSearch;
  }
  /**
   * @param string
   */
  public function setServingConfig($servingConfig)
  {
    $this->servingConfig = $servingConfig;
  }
  /**
   * @return string
   */
  public function getServingConfig()
  {
    return $this->servingConfig;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec
   */
  public function setSummarySpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec $summarySpec)
  {
    $this->summarySpec = $summarySpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpec
   */
  public function getSummarySpec()
  {
    return $this->summarySpec;
  }
  /**
   * @param string[]
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaConverseConversationRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaConverseConversationRequest');
