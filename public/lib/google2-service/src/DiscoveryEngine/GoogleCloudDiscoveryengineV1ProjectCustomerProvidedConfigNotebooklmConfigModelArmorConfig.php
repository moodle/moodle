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

class GoogleCloudDiscoveryengineV1ProjectCustomerProvidedConfigNotebooklmConfigModelArmorConfig extends \Google\Model
{
  /**
   * Optional. The resource name of the Model Armor Template for sanitizing LLM
   * responses. Format:
   * projects/{project}/locations/{location}/templates/{template_id} If not
   * specified, no sanitization will be applied to the LLM response.
   *
   * @var string
   */
  public $responseTemplate;
  /**
   * Optional. The resource name of the Model Armor Template for sanitizing user
   * prompts. Format:
   * projects/{project}/locations/{location}/templates/{template_id} If not
   * specified, no sanitization will be applied to the user prompt.
   *
   * @var string
   */
  public $userPromptTemplate;

  /**
   * Optional. The resource name of the Model Armor Template for sanitizing LLM
   * responses. Format:
   * projects/{project}/locations/{location}/templates/{template_id} If not
   * specified, no sanitization will be applied to the LLM response.
   *
   * @param string $responseTemplate
   */
  public function setResponseTemplate($responseTemplate)
  {
    $this->responseTemplate = $responseTemplate;
  }
  /**
   * @return string
   */
  public function getResponseTemplate()
  {
    return $this->responseTemplate;
  }
  /**
   * Optional. The resource name of the Model Armor Template for sanitizing user
   * prompts. Format:
   * projects/{project}/locations/{location}/templates/{template_id} If not
   * specified, no sanitization will be applied to the user prompt.
   *
   * @param string $userPromptTemplate
   */
  public function setUserPromptTemplate($userPromptTemplate)
  {
    $this->userPromptTemplate = $userPromptTemplate;
  }
  /**
   * @return string
   */
  public function getUserPromptTemplate()
  {
    return $this->userPromptTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ProjectCustomerProvidedConfigNotebooklmConfigModelArmorConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ProjectCustomerProvidedConfigNotebooklmConfigModelArmorConfig');
