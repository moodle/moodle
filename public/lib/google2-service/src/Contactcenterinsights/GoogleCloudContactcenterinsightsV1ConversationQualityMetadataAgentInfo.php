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

class GoogleCloudContactcenterinsightsV1ConversationQualityMetadataAgentInfo extends \Google\Collection
{
  /**
   * Participant's role is not set.
   */
  public const AGENT_TYPE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * Participant is a human agent.
   */
  public const AGENT_TYPE_HUMAN_AGENT = 'HUMAN_AGENT';
  /**
   * Participant is an automated agent.
   */
  public const AGENT_TYPE_AUTOMATED_AGENT = 'AUTOMATED_AGENT';
  /**
   * Participant is an end user who conversed with the contact center.
   */
  public const AGENT_TYPE_END_USER = 'END_USER';
  /**
   * Participant is either a human or automated agent.
   */
  public const AGENT_TYPE_ANY_AGENT = 'ANY_AGENT';
  protected $collection_key = 'teams';
  /**
   * A user-specified string representing the agent.
   *
   * @var string
   */
  public $agentId;
  /**
   * The agent type, e.g. HUMAN_AGENT.
   *
   * @var string
   */
  public $agentType;
  /**
   * The agent's deployment display name. Only applicable to automated agents.
   *
   * @var string
   */
  public $deploymentDisplayName;
  /**
   * The agent's deployment ID. Only applicable to automated agents.
   *
   * @var string
   */
  public $deploymentId;
  /**
   * The agent's name.
   *
   * @var string
   */
  public $displayName;
  /**
   * A user-provided string indicating the outcome of the agent's segment of the
   * call.
   *
   * @var string
   */
  public $dispositionCode;
  /**
   * The agent's location.
   *
   * @var string
   */
  public $location;
  /**
   * A user-specified string representing the agent's team. Deprecated in favor
   * of the `teams` field.
   *
   * @deprecated
   * @var string
   */
  public $team;
  /**
   * User-specified strings representing the agent's teams.
   *
   * @var string[]
   */
  public $teams;
  /**
   * The agent's version display name. Only applicable to automated agents.
   *
   * @var string
   */
  public $versionDisplayName;
  /**
   * The agent's version ID. Only applicable to automated agents.
   *
   * @var string
   */
  public $versionId;

  /**
   * A user-specified string representing the agent.
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
   * The agent type, e.g. HUMAN_AGENT.
   *
   * Accepted values: ROLE_UNSPECIFIED, HUMAN_AGENT, AUTOMATED_AGENT, END_USER,
   * ANY_AGENT
   *
   * @param self::AGENT_TYPE_* $agentType
   */
  public function setAgentType($agentType)
  {
    $this->agentType = $agentType;
  }
  /**
   * @return self::AGENT_TYPE_*
   */
  public function getAgentType()
  {
    return $this->agentType;
  }
  /**
   * The agent's deployment display name. Only applicable to automated agents.
   *
   * @param string $deploymentDisplayName
   */
  public function setDeploymentDisplayName($deploymentDisplayName)
  {
    $this->deploymentDisplayName = $deploymentDisplayName;
  }
  /**
   * @return string
   */
  public function getDeploymentDisplayName()
  {
    return $this->deploymentDisplayName;
  }
  /**
   * The agent's deployment ID. Only applicable to automated agents.
   *
   * @param string $deploymentId
   */
  public function setDeploymentId($deploymentId)
  {
    $this->deploymentId = $deploymentId;
  }
  /**
   * @return string
   */
  public function getDeploymentId()
  {
    return $this->deploymentId;
  }
  /**
   * The agent's name.
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
   * A user-provided string indicating the outcome of the agent's segment of the
   * call.
   *
   * @param string $dispositionCode
   */
  public function setDispositionCode($dispositionCode)
  {
    $this->dispositionCode = $dispositionCode;
  }
  /**
   * @return string
   */
  public function getDispositionCode()
  {
    return $this->dispositionCode;
  }
  /**
   * The agent's location.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * A user-specified string representing the agent's team. Deprecated in favor
   * of the `teams` field.
   *
   * @deprecated
   * @param string $team
   */
  public function setTeam($team)
  {
    $this->team = $team;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTeam()
  {
    return $this->team;
  }
  /**
   * User-specified strings representing the agent's teams.
   *
   * @param string[] $teams
   */
  public function setTeams($teams)
  {
    $this->teams = $teams;
  }
  /**
   * @return string[]
   */
  public function getTeams()
  {
    return $this->teams;
  }
  /**
   * The agent's version display name. Only applicable to automated agents.
   *
   * @param string $versionDisplayName
   */
  public function setVersionDisplayName($versionDisplayName)
  {
    $this->versionDisplayName = $versionDisplayName;
  }
  /**
   * @return string
   */
  public function getVersionDisplayName()
  {
    return $this->versionDisplayName;
  }
  /**
   * The agent's version ID. Only applicable to automated agents.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1ConversationQualityMetadataAgentInfo::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1ConversationQualityMetadataAgentInfo');
