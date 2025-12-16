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

class GoogleCloudDialogflowCxV3ExportAgentResponse extends \Google\Model
{
  /**
   * Uncompressed raw byte content for agent. This field is populated if none of
   * `agent_uri` and `git_destination` are specified in ExportAgentRequest.
   *
   * @var string
   */
  public $agentContent;
  /**
   * The URI to a file containing the exported agent. This field is populated if
   * `agent_uri` is specified in ExportAgentRequest.
   *
   * @var string
   */
  public $agentUri;
  /**
   * Commit SHA of the git push. This field is populated if `git_destination` is
   * specified in ExportAgentRequest.
   *
   * @var string
   */
  public $commitSha;

  /**
   * Uncompressed raw byte content for agent. This field is populated if none of
   * `agent_uri` and `git_destination` are specified in ExportAgentRequest.
   *
   * @param string $agentContent
   */
  public function setAgentContent($agentContent)
  {
    $this->agentContent = $agentContent;
  }
  /**
   * @return string
   */
  public function getAgentContent()
  {
    return $this->agentContent;
  }
  /**
   * The URI to a file containing the exported agent. This field is populated if
   * `agent_uri` is specified in ExportAgentRequest.
   *
   * @param string $agentUri
   */
  public function setAgentUri($agentUri)
  {
    $this->agentUri = $agentUri;
  }
  /**
   * @return string
   */
  public function getAgentUri()
  {
    return $this->agentUri;
  }
  /**
   * Commit SHA of the git push. This field is populated if `git_destination` is
   * specified in ExportAgentRequest.
   *
   * @param string $commitSha
   */
  public function setCommitSha($commitSha)
  {
    $this->commitSha = $commitSha;
  }
  /**
   * @return string
   */
  public function getCommitSha()
  {
    return $this->commitSha;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportAgentResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportAgentResponse');
