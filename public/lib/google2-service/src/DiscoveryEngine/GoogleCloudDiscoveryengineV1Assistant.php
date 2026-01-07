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

class GoogleCloudDiscoveryengineV1Assistant extends \Google\Model
{
  /**
   * Default, unspecified setting. This is the same as disabled.
   */
  public const WEB_GROUNDING_TYPE_WEB_GROUNDING_TYPE_UNSPECIFIED = 'WEB_GROUNDING_TYPE_UNSPECIFIED';
  /**
   * Web grounding is disabled.
   */
  public const WEB_GROUNDING_TYPE_WEB_GROUNDING_TYPE_DISABLED = 'WEB_GROUNDING_TYPE_DISABLED';
  /**
   * Grounding with Google Search is enabled.
   */
  public const WEB_GROUNDING_TYPE_WEB_GROUNDING_TYPE_GOOGLE_SEARCH = 'WEB_GROUNDING_TYPE_GOOGLE_SEARCH';
  /**
   * Grounding with Enterprise Web Search is enabled.
   */
  public const WEB_GROUNDING_TYPE_WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH = 'WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH';
  protected $customerPolicyType = GoogleCloudDiscoveryengineV1AssistantCustomerPolicy::class;
  protected $customerPolicyDataType = '';
  /**
   * Optional. Description for additional information. Expected to be shown on
   * the configuration UI, not to the users of the assistant.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The assistant display name. It must be a UTF-8 encoded string
   * with a length limit of 128 characters.
   *
   * @var string
   */
  public $displayName;
  protected $enabledToolsType = GoogleCloudDiscoveryengineV1AssistantToolList::class;
  protected $enabledToolsDataType = 'map';
  protected $generationConfigType = GoogleCloudDiscoveryengineV1AssistantGenerationConfig::class;
  protected $generationConfigDataType = '';
  /**
   * Immutable. Resource name of the assistant. Format: `projects/{project}/loca
   * tions/{location}/collections/{collection}/engines/{engine}/assistants/{assi
   * stant}` It must be a UTF-8 encoded string with a length limit of 1024
   * characters.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The type of web grounding to use.
   *
   * @var string
   */
  public $webGroundingType;

  /**
   * Optional. Customer policy for the assistant.
   *
   * @param GoogleCloudDiscoveryengineV1AssistantCustomerPolicy $customerPolicy
   */
  public function setCustomerPolicy(GoogleCloudDiscoveryengineV1AssistantCustomerPolicy $customerPolicy)
  {
    $this->customerPolicy = $customerPolicy;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistantCustomerPolicy
   */
  public function getCustomerPolicy()
  {
    return $this->customerPolicy;
  }
  /**
   * Optional. Description for additional information. Expected to be shown on
   * the configuration UI, not to the users of the assistant.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The assistant display name. It must be a UTF-8 encoded string
   * with a length limit of 128 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Note: not implemented yet. Use enabled_actions instead. The
   * enabled tools on this assistant. The keys are connector name, for example "
   * projects/{projectId}/locations/{locationId}/collections/{collectionId}/data
   * connector The values consist of admin enabled tools towards the connector
   * instance. Admin can selectively enable multiple tools on any of the
   * connector instances that they created in the project. For example
   * {"jira1ConnectorName": [(toolId1, "createTicket"), (toolId2,
   * "transferTicket")], "gmail1ConnectorName": [(toolId3, "sendEmail"),..] }
   *
   * @param GoogleCloudDiscoveryengineV1AssistantToolList[] $enabledTools
   */
  public function setEnabledTools($enabledTools)
  {
    $this->enabledTools = $enabledTools;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistantToolList[]
   */
  public function getEnabledTools()
  {
    return $this->enabledTools;
  }
  /**
   * Optional. Configuration for the generation of the assistant response.
   *
   * @param GoogleCloudDiscoveryengineV1AssistantGenerationConfig $generationConfig
   */
  public function setGenerationConfig(GoogleCloudDiscoveryengineV1AssistantGenerationConfig $generationConfig)
  {
    $this->generationConfig = $generationConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistantGenerationConfig
   */
  public function getGenerationConfig()
  {
    return $this->generationConfig;
  }
  /**
   * Immutable. Resource name of the assistant. Format: `projects/{project}/loca
   * tions/{location}/collections/{collection}/engines/{engine}/assistants/{assi
   * stant}` It must be a UTF-8 encoded string with a length limit of 1024
   * characters.
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
   * Optional. The type of web grounding to use.
   *
   * Accepted values: WEB_GROUNDING_TYPE_UNSPECIFIED,
   * WEB_GROUNDING_TYPE_DISABLED, WEB_GROUNDING_TYPE_GOOGLE_SEARCH,
   * WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH
   *
   * @param self::WEB_GROUNDING_TYPE_* $webGroundingType
   */
  public function setWebGroundingType($webGroundingType)
  {
    $this->webGroundingType = $webGroundingType;
  }
  /**
   * @return self::WEB_GROUNDING_TYPE_*
   */
  public function getWebGroundingType()
  {
    return $this->webGroundingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1Assistant::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Assistant');
