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

class GoogleCloudDialogflowV2SuggestionResult extends \Google\Model
{
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $generateSuggestionsResponseType = GoogleCloudDialogflowV2GenerateSuggestionsResponse::class;
  protected $generateSuggestionsResponseDataType = '';
  protected $suggestArticlesResponseType = GoogleCloudDialogflowV2SuggestArticlesResponse::class;
  protected $suggestArticlesResponseDataType = '';
  protected $suggestFaqAnswersResponseType = GoogleCloudDialogflowV2SuggestFaqAnswersResponse::class;
  protected $suggestFaqAnswersResponseDataType = '';
  protected $suggestKnowledgeAssistResponseType = GoogleCloudDialogflowV2SuggestKnowledgeAssistResponse::class;
  protected $suggestKnowledgeAssistResponseDataType = '';
  protected $suggestSmartRepliesResponseType = GoogleCloudDialogflowV2SuggestSmartRepliesResponse::class;
  protected $suggestSmartRepliesResponseDataType = '';

  /**
   * Error status if the request failed.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Suggestions generated using generators triggered by customer or agent
   * messages.
   *
   * @param GoogleCloudDialogflowV2GenerateSuggestionsResponse $generateSuggestionsResponse
   */
  public function setGenerateSuggestionsResponse(GoogleCloudDialogflowV2GenerateSuggestionsResponse $generateSuggestionsResponse)
  {
    $this->generateSuggestionsResponse = $generateSuggestionsResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2GenerateSuggestionsResponse
   */
  public function getGenerateSuggestionsResponse()
  {
    return $this->generateSuggestionsResponse;
  }
  /**
   * SuggestArticlesResponse if request is for ARTICLE_SUGGESTION.
   *
   * @param GoogleCloudDialogflowV2SuggestArticlesResponse $suggestArticlesResponse
   */
  public function setSuggestArticlesResponse(GoogleCloudDialogflowV2SuggestArticlesResponse $suggestArticlesResponse)
  {
    $this->suggestArticlesResponse = $suggestArticlesResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2SuggestArticlesResponse
   */
  public function getSuggestArticlesResponse()
  {
    return $this->suggestArticlesResponse;
  }
  /**
   * SuggestFaqAnswersResponse if request is for FAQ_ANSWER.
   *
   * @param GoogleCloudDialogflowV2SuggestFaqAnswersResponse $suggestFaqAnswersResponse
   */
  public function setSuggestFaqAnswersResponse(GoogleCloudDialogflowV2SuggestFaqAnswersResponse $suggestFaqAnswersResponse)
  {
    $this->suggestFaqAnswersResponse = $suggestFaqAnswersResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2SuggestFaqAnswersResponse
   */
  public function getSuggestFaqAnswersResponse()
  {
    return $this->suggestFaqAnswersResponse;
  }
  /**
   * SuggestKnowledgeAssistResponse if request is for KNOWLEDGE_ASSIST.
   *
   * @param GoogleCloudDialogflowV2SuggestKnowledgeAssistResponse $suggestKnowledgeAssistResponse
   */
  public function setSuggestKnowledgeAssistResponse(GoogleCloudDialogflowV2SuggestKnowledgeAssistResponse $suggestKnowledgeAssistResponse)
  {
    $this->suggestKnowledgeAssistResponse = $suggestKnowledgeAssistResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2SuggestKnowledgeAssistResponse
   */
  public function getSuggestKnowledgeAssistResponse()
  {
    return $this->suggestKnowledgeAssistResponse;
  }
  /**
   * SuggestSmartRepliesResponse if request is for SMART_REPLY.
   *
   * @param GoogleCloudDialogflowV2SuggestSmartRepliesResponse $suggestSmartRepliesResponse
   */
  public function setSuggestSmartRepliesResponse(GoogleCloudDialogflowV2SuggestSmartRepliesResponse $suggestSmartRepliesResponse)
  {
    $this->suggestSmartRepliesResponse = $suggestSmartRepliesResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2SuggestSmartRepliesResponse
   */
  public function getSuggestSmartRepliesResponse()
  {
    return $this->suggestSmartRepliesResponse;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2SuggestionResult::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2SuggestionResult');
