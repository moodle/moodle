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

class GoogleCloudContactcenterinsightsV1AgentCoachingSuggestion extends \Google\Collection
{
  protected $collection_key = 'sampleResponses';
  protected $agentActionSuggestionsType = GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentActionSuggestion::class;
  protected $agentActionSuggestionsDataType = 'array';
  protected $applicableInstructionsType = GoogleCloudContactcenterinsightsV1AgentCoachingInstruction::class;
  protected $applicableInstructionsDataType = 'array';
  protected $sampleResponsesType = GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionSampleResponse::class;
  protected $sampleResponsesDataType = 'array';
  protected $suggestionEvalType = GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentCoachingSuggestionEval::class;
  protected $suggestionEvalDataType = '';
  protected $suggestionReasoningType = GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentCoachingSuggestionReasoning::class;
  protected $suggestionReasoningDataType = '';

  /**
   * @param GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentActionSuggestion[]
   */
  public function setAgentActionSuggestions($agentActionSuggestions)
  {
    $this->agentActionSuggestions = $agentActionSuggestions;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentActionSuggestion[]
   */
  public function getAgentActionSuggestions()
  {
    return $this->agentActionSuggestions;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1AgentCoachingInstruction[]
   */
  public function setApplicableInstructions($applicableInstructions)
  {
    $this->applicableInstructions = $applicableInstructions;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AgentCoachingInstruction[]
   */
  public function getApplicableInstructions()
  {
    return $this->applicableInstructions;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionSampleResponse[]
   */
  public function setSampleResponses($sampleResponses)
  {
    $this->sampleResponses = $sampleResponses;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionSampleResponse[]
   */
  public function getSampleResponses()
  {
    return $this->sampleResponses;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentCoachingSuggestionEval
   */
  public function setSuggestionEval(GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentCoachingSuggestionEval $suggestionEval)
  {
    $this->suggestionEval = $suggestionEval;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentCoachingSuggestionEval
   */
  public function getSuggestionEval()
  {
    return $this->suggestionEval;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentCoachingSuggestionReasoning
   */
  public function setSuggestionReasoning(GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentCoachingSuggestionReasoning $suggestionReasoning)
  {
    $this->suggestionReasoning = $suggestionReasoning;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AgentCoachingSuggestionAgentCoachingSuggestionReasoning
   */
  public function getSuggestionReasoning()
  {
    return $this->suggestionReasoning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1AgentCoachingSuggestion::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1AgentCoachingSuggestion');
