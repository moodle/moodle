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

class GoogleCloudDialogflowV2beta1GenerateSuggestionsResponse extends \Google\Collection
{
  protected $collection_key = 'generatorSuggestionAnswers';
  protected $generatorSuggestionAnswersType = GoogleCloudDialogflowV2beta1GenerateSuggestionsResponseGeneratorSuggestionAnswer::class;
  protected $generatorSuggestionAnswersDataType = 'array';
  /**
   * The name of the latest conversation message used as context for compiling
   * suggestion. Format: `projects//locations//conversations//messages/`.
   *
   * @var string
   */
  public $latestMessage;

  /**
   * The answers generated for the conversation based on context.
   *
   * @param GoogleCloudDialogflowV2beta1GenerateSuggestionsResponseGeneratorSuggestionAnswer[] $generatorSuggestionAnswers
   */
  public function setGeneratorSuggestionAnswers($generatorSuggestionAnswers)
  {
    $this->generatorSuggestionAnswers = $generatorSuggestionAnswers;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1GenerateSuggestionsResponseGeneratorSuggestionAnswer[]
   */
  public function getGeneratorSuggestionAnswers()
  {
    return $this->generatorSuggestionAnswers;
  }
  /**
   * The name of the latest conversation message used as context for compiling
   * suggestion. Format: `projects//locations//conversations//messages/`.
   *
   * @param string $latestMessage
   */
  public function setLatestMessage($latestMessage)
  {
    $this->latestMessage = $latestMessage;
  }
  /**
   * @return string
   */
  public function getLatestMessage()
  {
    return $this->latestMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1GenerateSuggestionsResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1GenerateSuggestionsResponse');
