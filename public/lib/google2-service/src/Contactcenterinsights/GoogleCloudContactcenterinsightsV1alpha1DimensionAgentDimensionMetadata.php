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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1DimensionAgentDimensionMetadata extends \Google\Model
{
  /**
   * Optional. The agent's deployment display name. Only applicable to automated
   * agents. This will be populated for AGENT_DEPLOYMENT_ID dimensions.
   *
   * @var string
   */
  public $agentDeploymentDisplayName;
  /**
   * Optional. The agent's deployment ID. Only applicable to automated agents.
   * This will be populated for AGENT and AGENT_DEPLOYMENT_ID dimensions.
   *
   * @var string
   */
  public $agentDeploymentId;
  /**
   * Optional. The agent's name This will be populated for AGENT, AGENT_TEAM,
   * AGENT_VERSION_ID, and AGENT_DEPLOYMENT_ID dimensions.
   *
   * @var string
   */
  public $agentDisplayName;
  /**
   * Optional. A user-specified string representing the agent. This will be
   * populated for AGENT, AGENT_TEAM, AGENT_VERSION_ID, and AGENT_DEPLOYMENT_ID
   * dimensions.
   *
   * @var string
   */
  public $agentId;
  /**
   * Optional. A user-specified string representing the agent's team.
   *
   * @var string
   */
  public $agentTeam;
  /**
   * Optional. The agent's version display name. Only applicable to automated
   * agents. This will be populated for AGENT_VERSION_ID, and
   * AGENT_DEPLOYMENT_ID dimensions.
   *
   * @var string
   */
  public $agentVersionDisplayName;
  /**
   * Optional. The agent's version ID. Only applicable to automated agents. This
   * will be populated for AGENT_VERSION_ID, and AGENT_DEPLOYMENT_ID dimensions.
   *
   * @var string
   */
  public $agentVersionId;

  /**
   * Optional. The agent's deployment display name. Only applicable to automated
   * agents. This will be populated for AGENT_DEPLOYMENT_ID dimensions.
   *
   * @param string $agentDeploymentDisplayName
   */
  public function setAgentDeploymentDisplayName($agentDeploymentDisplayName)
  {
    $this->agentDeploymentDisplayName = $agentDeploymentDisplayName;
  }
  /**
   * @return string
   */
  public function getAgentDeploymentDisplayName()
  {
    return $this->agentDeploymentDisplayName;
  }
  /**
   * Optional. The agent's deployment ID. Only applicable to automated agents.
   * This will be populated for AGENT and AGENT_DEPLOYMENT_ID dimensions.
   *
   * @param string $agentDeploymentId
   */
  public function setAgentDeploymentId($agentDeploymentId)
  {
    $this->agentDeploymentId = $agentDeploymentId;
  }
  /**
   * @return string
   */
  public function getAgentDeploymentId()
  {
    return $this->agentDeploymentId;
  }
  /**
   * Optional. The agent's name This will be populated for AGENT, AGENT_TEAM,
   * AGENT_VERSION_ID, and AGENT_DEPLOYMENT_ID dimensions.
   *
   * @param string $agentDisplayName
   */
  public function setAgentDisplayName($agentDisplayName)
  {
    $this->agentDisplayName = $agentDisplayName;
  }
  /**
   * @return string
   */
  public function getAgentDisplayName()
  {
    return $this->agentDisplayName;
  }
  /**
   * Optional. A user-specified string representing the agent. This will be
   * populated for AGENT, AGENT_TEAM, AGENT_VERSION_ID, and AGENT_DEPLOYMENT_ID
   * dimensions.
   *
   * @param string $agentId
   */
  public function setAgentId($agentId)
  {
    $this->agentId = $agentId;
  }
  /**
   * @return string
   */
  public function getAgentId()
  {
    return $this->agentId;
  }
  /**
   * Optional. A user-specified string representing the agent's team.
   *
   * @param string $agentTeam
   */
  public function setAgentTeam($agentTeam)
  {
    $this->agentTeam = $agentTeam;
  }
  /**
   * @return string
   */
  public function getAgentTeam()
  {
    return $this->agentTeam;
  }
  /**
   * Optional. The agent's version display name. Only applicable to automated
   * agents. This will be populated for AGENT_VERSION_ID, and
   * AGENT_DEPLOYMENT_ID dimensions.
   *
   * @param string $agentVersionDisplayName
   */
  public function setAgentVersionDisplayName($agentVersionDisplayName)
  {
    $this->agentVersionDisplayName = $agentVersionDisplayName;
  }
  /**
   * @return string
   */
  public function getAgentVersionDisplayName()
  {
    return $this->agentVersionDisplayName;
  }
  /**
   * Optional. The agent's version ID. Only applicable to automated agents. This
   * will be populated for AGENT_VERSION_ID, and AGENT_DEPLOYMENT_ID dimensions.
   *
   * @param string $agentVersionId
   */
  public function setAgentVersionId($agentVersionId)
  {
    $this->agentVersionId = $agentVersionId;
  }
  /**
   * @return string
   */
  public function getAgentVersionId()
  {
    return $this->agentVersionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1DimensionAgentDimensionMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1DimensionAgentDimensionMetadata');
