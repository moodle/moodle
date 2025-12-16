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

class GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultPolicyEnforcementResult extends \Google\Model
{
  protected $bannedPhraseEnforcementResultType = GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultBannedPhraseEnforcementResult::class;
  protected $bannedPhraseEnforcementResultDataType = '';
  protected $modelArmorEnforcementResultType = GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultModelArmorEnforcementResult::class;
  protected $modelArmorEnforcementResultDataType = '';

  /**
   * The policy enforcement result for the banned phrase policy.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultBannedPhraseEnforcementResult $bannedPhraseEnforcementResult
   */
  public function setBannedPhraseEnforcementResult(GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultBannedPhraseEnforcementResult $bannedPhraseEnforcementResult)
  {
    $this->bannedPhraseEnforcementResult = $bannedPhraseEnforcementResult;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultBannedPhraseEnforcementResult
   */
  public function getBannedPhraseEnforcementResult()
  {
    return $this->bannedPhraseEnforcementResult;
  }
  /**
   * The policy enforcement result for the Model Armor policy.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultModelArmorEnforcementResult $modelArmorEnforcementResult
   */
  public function setModelArmorEnforcementResult(GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultModelArmorEnforcementResult $modelArmorEnforcementResult)
  {
    $this->modelArmorEnforcementResult = $modelArmorEnforcementResult;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultModelArmorEnforcementResult
   */
  public function getModelArmorEnforcementResult()
  {
    return $this->modelArmorEnforcementResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultPolicyEnforcementResult::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultPolicyEnforcementResult');
