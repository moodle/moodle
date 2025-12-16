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

class GoogleCloudDiscoveryengineV1EngineChatEngineConfig extends \Google\Model
{
  protected $agentCreationConfigType = GoogleCloudDiscoveryengineV1EngineChatEngineConfigAgentCreationConfig::class;
  protected $agentCreationConfigDataType = '';
  /**
   * Optional. If the flag set to true, we allow the agent and engine are in
   * different locations, otherwise the agent and engine are required to be in
   * the same location. The flag is set to false by default. Note that the
   * `allow_cross_region` are one-time consumed by and passed to
   * EngineService.CreateEngine. It means they cannot be retrieved using
   * EngineService.GetEngine or EngineService.ListEngines API after engine
   * creation.
   *
   * @var bool
   */
  public $allowCrossRegion;
  /**
   * The resource name of an exist Dialogflow agent to link to this Chat Engine.
   * Customers can either provide `agent_creation_config` to create agent or
   * provide an agent name that links the agent with the Chat engine. Format:
   * `projects//locations//agents/`. Note that the `dialogflow_agent_to_link`
   * are one-time consumed by and passed to Dialogflow service. It means they
   * cannot be retrieved using EngineService.GetEngine or
   * EngineService.ListEngines API after engine creation. Use
   * ChatEngineMetadata.dialogflow_agent for actual agent association after
   * Engine is created.
   *
   * @var string
   */
  public $dialogflowAgentToLink;

  /**
   * The configurationt generate the Dialogflow agent that is associated to this
   * Engine. Note that these configurations are one-time consumed by and passed
   * to Dialogflow service. It means they cannot be retrieved using
   * EngineService.GetEngine or EngineService.ListEngines API after engine
   * creation.
   *
   * @param GoogleCloudDiscoveryengineV1EngineChatEngineConfigAgentCreationConfig $agentCreationConfig
   */
  public function setAgentCreationConfig(GoogleCloudDiscoveryengineV1EngineChatEngineConfigAgentCreationConfig $agentCreationConfig)
  {
    $this->agentCreationConfig = $agentCreationConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1EngineChatEngineConfigAgentCreationConfig
   */
  public function getAgentCreationConfig()
  {
    return $this->agentCreationConfig;
  }
  /**
   * Optional. If the flag set to true, we allow the agent and engine are in
   * different locations, otherwise the agent and engine are required to be in
   * the same location. The flag is set to false by default. Note that the
   * `allow_cross_region` are one-time consumed by and passed to
   * EngineService.CreateEngine. It means they cannot be retrieved using
   * EngineService.GetEngine or EngineService.ListEngines API after engine
   * creation.
   *
   * @param bool $allowCrossRegion
   */
  public function setAllowCrossRegion($allowCrossRegion)
  {
    $this->allowCrossRegion = $allowCrossRegion;
  }
  /**
   * @return bool
   */
  public function getAllowCrossRegion()
  {
    return $this->allowCrossRegion;
  }
  /**
   * The resource name of an exist Dialogflow agent to link to this Chat Engine.
   * Customers can either provide `agent_creation_config` to create agent or
   * provide an agent name that links the agent with the Chat engine. Format:
   * `projects//locations//agents/`. Note that the `dialogflow_agent_to_link`
   * are one-time consumed by and passed to Dialogflow service. It means they
   * cannot be retrieved using EngineService.GetEngine or
   * EngineService.ListEngines API after engine creation. Use
   * ChatEngineMetadata.dialogflow_agent for actual agent association after
   * Engine is created.
   *
   * @param string $dialogflowAgentToLink
   */
  public function setDialogflowAgentToLink($dialogflowAgentToLink)
  {
    $this->dialogflowAgentToLink = $dialogflowAgentToLink;
  }
  /**
   * @return string
   */
  public function getDialogflowAgentToLink()
  {
    return $this->dialogflowAgentToLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1EngineChatEngineConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1EngineChatEngineConfig');
