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

class GoogleCloudDiscoveryengineV1AssistantCustomerPolicy extends \Google\Collection
{
  protected $collection_key = 'bannedPhrases';
  protected $bannedPhrasesType = GoogleCloudDiscoveryengineV1AssistantCustomerPolicyBannedPhrase::class;
  protected $bannedPhrasesDataType = 'array';
  protected $modelArmorConfigType = GoogleCloudDiscoveryengineV1AssistantCustomerPolicyModelArmorConfig::class;
  protected $modelArmorConfigDataType = '';

  /**
   * Optional. List of banned phrases.
   *
   * @param GoogleCloudDiscoveryengineV1AssistantCustomerPolicyBannedPhrase[] $bannedPhrases
   */
  public function setBannedPhrases($bannedPhrases)
  {
    $this->bannedPhrases = $bannedPhrases;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistantCustomerPolicyBannedPhrase[]
   */
  public function getBannedPhrases()
  {
    return $this->bannedPhrases;
  }
  /**
   * Optional. Model Armor configuration to be used for sanitizing user prompts
   * and assistant responses.
   *
   * @param GoogleCloudDiscoveryengineV1AssistantCustomerPolicyModelArmorConfig $modelArmorConfig
   */
  public function setModelArmorConfig(GoogleCloudDiscoveryengineV1AssistantCustomerPolicyModelArmorConfig $modelArmorConfig)
  {
    $this->modelArmorConfig = $modelArmorConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistantCustomerPolicyModelArmorConfig
   */
  public function getModelArmorConfig()
  {
    return $this->modelArmorConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AssistantCustomerPolicy::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantCustomerPolicy');
