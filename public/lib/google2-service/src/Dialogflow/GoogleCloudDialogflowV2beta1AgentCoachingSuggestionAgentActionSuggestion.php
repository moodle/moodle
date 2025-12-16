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

class GoogleCloudDialogflowV2beta1AgentCoachingSuggestionAgentActionSuggestion extends \Google\Model
{
  /**
   * Optional. The suggested action for the agent.
   *
   * @var string
   */
  public $agentAction;
  protected $duplicateCheckResultType = GoogleCloudDialogflowV2beta1AgentCoachingSuggestionDuplicateCheckResult::class;
  protected $duplicateCheckResultDataType = '';
  protected $sourcesType = GoogleCloudDialogflowV2beta1AgentCoachingSuggestionSources::class;
  protected $sourcesDataType = '';

  /**
   * Optional. The suggested action for the agent.
   *
   * @param string $agentAction
   */
  public function setAgentAction($agentAction)
  {
    $this->agentAction = $agentAction;
  }
  /**
   * @return string
   */
  public function getAgentAction()
  {
    return $this->agentAction;
  }
  /**
   * Output only. Duplicate check result for the agent action suggestion.
   *
   * @param GoogleCloudDialogflowV2beta1AgentCoachingSuggestionDuplicateCheckResult $duplicateCheckResult
   */
  public function setDuplicateCheckResult(GoogleCloudDialogflowV2beta1AgentCoachingSuggestionDuplicateCheckResult $duplicateCheckResult)
  {
    $this->duplicateCheckResult = $duplicateCheckResult;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1AgentCoachingSuggestionDuplicateCheckResult
   */
  public function getDuplicateCheckResult()
  {
    return $this->duplicateCheckResult;
  }
  /**
   * Output only. Sources for the agent action suggestion.
   *
   * @param GoogleCloudDialogflowV2beta1AgentCoachingSuggestionSources $sources
   */
  public function setSources(GoogleCloudDialogflowV2beta1AgentCoachingSuggestionSources $sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1AgentCoachingSuggestionSources
   */
  public function getSources()
  {
    return $this->sources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1AgentCoachingSuggestionAgentActionSuggestion::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1AgentCoachingSuggestionAgentActionSuggestion');
