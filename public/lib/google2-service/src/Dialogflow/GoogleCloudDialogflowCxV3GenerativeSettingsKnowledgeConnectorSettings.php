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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3GenerativeSettingsKnowledgeConnectorSettings extends \Google\Model
{
  /**
   * Name of the virtual agent. Used for LLM prompt. Can be left empty.
   *
   * @var string
   */
  public $agent;
  /**
   * Identity of the agent, e.g. "virtual agent", "AI assistant".
   *
   * @var string
   */
  public $agentIdentity;
  /**
   * Agent scope, e.g. "Example company website", "internal Example company
   * website for employees", "manual of car owner".
   *
   * @var string
   */
  public $agentScope;
  /**
   * Name of the company, organization or other entity that the agent
   * represents. Used for knowledge connector LLM prompt and for knowledge
   * search.
   *
   * @var string
   */
  public $business;
  /**
   * Company description, used for LLM prompt, e.g. "a family company selling
   * freshly roasted coffee beans".
   *
   * @var string
   */
  public $businessDescription;
  /**
   * Whether to disable fallback to Data Store search results (in case the LLM
   * couldn't pick a proper answer). Per default the feature is enabled.
   *
   * @var bool
   */
  public $disableDataStoreFallback;

  /**
   * Name of the virtual agent. Used for LLM prompt. Can be left empty.
   *
   * @param string $agent
   */
  public function setAgent($agent)
  {
    $this->agent = $agent;
  }
  /**
   * @return string
   */
  public function getAgent()
  {
    return $this->agent;
  }
  /**
   * Identity of the agent, e.g. "virtual agent", "AI assistant".
   *
   * @param string $agentIdentity
   */
  public function setAgentIdentity($agentIdentity)
  {
    $this->agentIdentity = $agentIdentity;
  }
  /**
   * @return string
   */
  public function getAgentIdentity()
  {
    return $this->agentIdentity;
  }
  /**
   * Agent scope, e.g. "Example company website", "internal Example company
   * website for employees", "manual of car owner".
   *
   * @param string $agentScope
   */
  public function setAgentScope($agentScope)
  {
    $this->agentScope = $agentScope;
  }
  /**
   * @return string
   */
  public function getAgentScope()
  {
    return $this->agentScope;
  }
  /**
   * Name of the company, organization or other entity that the agent
   * represents. Used for knowledge connector LLM prompt and for knowledge
   * search.
   *
   * @param string $business
   */
  public function setBusiness($business)
  {
    $this->business = $business;
  }
  /**
   * @return string
   */
  public function getBusiness()
  {
    return $this->business;
  }
  /**
   * Company description, used for LLM prompt, e.g. "a family company selling
   * freshly roasted coffee beans".
   *
   * @param string $businessDescription
   */
  public function setBusinessDescription($businessDescription)
  {
    $this->businessDescription = $businessDescription;
  }
  /**
   * @return string
   */
  public function getBusinessDescription()
  {
    return $this->businessDescription;
  }
  /**
   * Whether to disable fallback to Data Store search results (in case the LLM
   * couldn't pick a proper answer). Per default the feature is enabled.
   *
   * @param bool $disableDataStoreFallback
   */
  public function setDisableDataStoreFallback($disableDataStoreFallback)
  {
    $this->disableDataStoreFallback = $disableDataStoreFallback;
  }
  /**
   * @return bool
   */
  public function getDisableDataStoreFallback()
  {
    return $this->disableDataStoreFallback;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3GenerativeSettingsKnowledgeConnectorSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3GenerativeSettingsKnowledgeConnectorSettings');
