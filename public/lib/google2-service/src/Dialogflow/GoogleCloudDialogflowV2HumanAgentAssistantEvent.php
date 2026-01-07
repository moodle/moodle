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

class GoogleCloudDialogflowV2HumanAgentAssistantEvent extends \Google\Collection
{
  protected $collection_key = 'suggestionResults';
  /**
   * The conversation this notification refers to. Format:
   * `projects//conversations/`.
   *
   * @var string
   */
  public $conversation;
  /**
   * The participant that the suggestion is compiled for. Format:
   * `projects//conversations//participants/`. It will not be set in legacy
   * workflow.
   *
   * @var string
   */
  public $participant;
  protected $suggestionResultsType = GoogleCloudDialogflowV2SuggestionResult::class;
  protected $suggestionResultsDataType = 'array';

  /**
   * The conversation this notification refers to. Format:
   * `projects//conversations/`.
   *
   * @param string $conversation
   */
  public function setConversation($conversation)
  {
    $this->conversation = $conversation;
  }
  /**
   * @return string
   */
  public function getConversation()
  {
    return $this->conversation;
  }
  /**
   * The participant that the suggestion is compiled for. Format:
   * `projects//conversations//participants/`. It will not be set in legacy
   * workflow.
   *
   * @param string $participant
   */
  public function setParticipant($participant)
  {
    $this->participant = $participant;
  }
  /**
   * @return string
   */
  public function getParticipant()
  {
    return $this->participant;
  }
  /**
   * The suggestion results payload that this notification refers to.
   *
   * @param GoogleCloudDialogflowV2SuggestionResult[] $suggestionResults
   */
  public function setSuggestionResults($suggestionResults)
  {
    $this->suggestionResults = $suggestionResults;
  }
  /**
   * @return GoogleCloudDialogflowV2SuggestionResult[]
   */
  public function getSuggestionResults()
  {
    return $this->suggestionResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2HumanAgentAssistantEvent::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2HumanAgentAssistantEvent');
