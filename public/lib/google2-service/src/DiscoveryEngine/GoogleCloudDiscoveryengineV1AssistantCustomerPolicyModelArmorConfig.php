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

class GoogleCloudDiscoveryengineV1AssistantCustomerPolicyModelArmorConfig extends \Google\Model
{
  /**
   * Unspecified failure mode, default behavior is `FAIL_CLOSED`.
   */
  public const FAILURE_MODE_FAILURE_MODE_UNSPECIFIED = 'FAILURE_MODE_UNSPECIFIED';
  /**
   * In case of a Model Armor processing failure, the request is allowed to
   * proceed without any changes.
   */
  public const FAILURE_MODE_FAIL_OPEN = 'FAIL_OPEN';
  /**
   * In case of a Model Armor processing failure, the request is rejected.
   */
  public const FAILURE_MODE_FAIL_CLOSED = 'FAIL_CLOSED';
  /**
   * Optional. Defines the failure mode for Model Armor sanitization.
   *
   * @var string
   */
  public $failureMode;
  /**
   * Optional. The resource name of the Model Armor template for sanitizing
   * assistant responses. Format:
   * `projects/{project}/locations/{location}/templates/{template_id}` If not
   * specified, no sanitization will be applied to the assistant response.
   *
   * @var string
   */
  public $responseTemplate;
  /**
   * Optional. The resource name of the Model Armor template for sanitizing user
   * prompts. Format:
   * `projects/{project}/locations/{location}/templates/{template_id}` If not
   * specified, no sanitization will be applied to the user prompt.
   *
   * @var string
   */
  public $userPromptTemplate;

  /**
   * Optional. Defines the failure mode for Model Armor sanitization.
   *
   * Accepted values: FAILURE_MODE_UNSPECIFIED, FAIL_OPEN, FAIL_CLOSED
   *
   * @param self::FAILURE_MODE_* $failureMode
   */
  public function setFailureMode($failureMode)
  {
    $this->failureMode = $failureMode;
  }
  /**
   * @return self::FAILURE_MODE_*
   */
  public function getFailureMode()
  {
    return $this->failureMode;
  }
  /**
   * Optional. The resource name of the Model Armor template for sanitizing
   * assistant responses. Format:
   * `projects/{project}/locations/{location}/templates/{template_id}` If not
   * specified, no sanitization will be applied to the assistant response.
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
   * Optional. The resource name of the Model Armor template for sanitizing user
   * prompts. Format:
   * `projects/{project}/locations/{location}/templates/{template_id}` If not
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
class_alias(GoogleCloudDiscoveryengineV1AssistantCustomerPolicyModelArmorConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantCustomerPolicyModelArmorConfig');
