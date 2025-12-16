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

class GoogleCloudDialogflowV2beta1SuggestionResult extends \Google\Model
{
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $generateSuggestionsResponseType = GoogleCloudDialogflowV2beta1GenerateSuggestionsResponse::class;
  protected $generateSuggestionsResponseDataType = '';
  protected $suggestArticlesResponseType = GoogleCloudDialogflowV2beta1SuggestArticlesResponse::class;
  protected $suggestArticlesResponseDataType = '';
  protected $suggestDialogflowAssistsResponseType = GoogleCloudDialogflowV2beta1SuggestDialogflowAssistsResponse::class;
  protected $suggestDialogflowAssistsResponseDataType = '';
  protected $suggestEntityExtractionResponseType = GoogleCloudDialogflowV2beta1SuggestDialogflowAssistsResponse::class;
  protected $suggestEntityExtractionResponseDataType = '';
  protected $suggestFaqAnswersResponseType = GoogleCloudDialogflowV2beta1SuggestFaqAnswersResponse::class;
  protected $suggestFaqAnswersResponseDataType = '';
  protected $suggestKnowledgeAssistResponseType = GoogleCloudDialogflowV2beta1SuggestKnowledgeAssistResponse::class;
  protected $suggestKnowledgeAssistResponseDataType = '';
  protected $suggestSmartRepliesResponseType = GoogleCloudDialogflowV2beta1SuggestSmartRepliesResponse::class;
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
   * @param GoogleCloudDialogflowV2beta1GenerateSuggestionsResponse $generateSuggestionsResponse
   */
  public function setGenerateSuggestionsResponse(GoogleCloudDialogflowV2beta1GenerateSuggestionsResponse $generateSuggestionsResponse)
  {
    $this->generateSuggestionsResponse = $generateSuggestionsResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1GenerateSuggestionsResponse
   */
  public function getGenerateSuggestionsResponse()
  {
    return $this->generateSuggestionsResponse;
  }
  /**
   * SuggestArticlesResponse if request is for ARTICLE_SUGGESTION.
   *
   * @param GoogleCloudDialogflowV2beta1SuggestArticlesResponse $suggestArticlesResponse
   */
  public function setSuggestArticlesResponse(GoogleCloudDialogflowV2beta1SuggestArticlesResponse $suggestArticlesResponse)
  {
    $this->suggestArticlesResponse = $suggestArticlesResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1SuggestArticlesResponse
   */
  public function getSuggestArticlesResponse()
  {
    return $this->suggestArticlesResponse;
  }
  /**
   * SuggestDialogflowAssistsResponse if request is for DIALOGFLOW_ASSIST.
   *
   * @param GoogleCloudDialogflowV2beta1SuggestDialogflowAssistsResponse $suggestDialogflowAssistsResponse
   */
  public function setSuggestDialogflowAssistsResponse(GoogleCloudDialogflowV2beta1SuggestDialogflowAssistsResponse $suggestDialogflowAssistsResponse)
  {
    $this->suggestDialogflowAssistsResponse = $suggestDialogflowAssistsResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1SuggestDialogflowAssistsResponse
   */
  public function getSuggestDialogflowAssistsResponse()
  {
    return $this->suggestDialogflowAssistsResponse;
  }
  /**
   * SuggestDialogflowAssistsResponse if request is for ENTITY_EXTRACTION.
   *
   * @param GoogleCloudDialogflowV2beta1SuggestDialogflowAssistsResponse $suggestEntityExtractionResponse
   */
  public function setSuggestEntityExtractionResponse(GoogleCloudDialogflowV2beta1SuggestDialogflowAssistsResponse $suggestEntityExtractionResponse)
  {
    $this->suggestEntityExtractionResponse = $suggestEntityExtractionResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1SuggestDialogflowAssistsResponse
   */
  public function getSuggestEntityExtractionResponse()
  {
    return $this->suggestEntityExtractionResponse;
  }
  /**
   * SuggestFaqAnswersResponse if request is for FAQ_ANSWER.
   *
   * @param GoogleCloudDialogflowV2beta1SuggestFaqAnswersResponse $suggestFaqAnswersResponse
   */
  public function setSuggestFaqAnswersResponse(GoogleCloudDialogflowV2beta1SuggestFaqAnswersResponse $suggestFaqAnswersResponse)
  {
    $this->suggestFaqAnswersResponse = $suggestFaqAnswersResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1SuggestFaqAnswersResponse
   */
  public function getSuggestFaqAnswersResponse()
  {
    return $this->suggestFaqAnswersResponse;
  }
  /**
   * SuggestKnowledgeAssistResponse if request is for KNOWLEDGE_ASSIST.
   *
   * @param GoogleCloudDialogflowV2beta1SuggestKnowledgeAssistResponse $suggestKnowledgeAssistResponse
   */
  public function setSuggestKnowledgeAssistResponse(GoogleCloudDialogflowV2beta1SuggestKnowledgeAssistResponse $suggestKnowledgeAssistResponse)
  {
    $this->suggestKnowledgeAssistResponse = $suggestKnowledgeAssistResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1SuggestKnowledgeAssistResponse
   */
  public function getSuggestKnowledgeAssistResponse()
  {
    return $this->suggestKnowledgeAssistResponse;
  }
  /**
   * SuggestSmartRepliesResponse if request is for SMART_REPLY.
   *
   * @param GoogleCloudDialogflowV2beta1SuggestSmartRepliesResponse $suggestSmartRepliesResponse
   */
  public function setSuggestSmartRepliesResponse(GoogleCloudDialogflowV2beta1SuggestSmartRepliesResponse $suggestSmartRepliesResponse)
  {
    $this->suggestSmartRepliesResponse = $suggestSmartRepliesResponse;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1SuggestSmartRepliesResponse
   */
  public function getSuggestSmartRepliesResponse()
  {
    return $this->suggestSmartRepliesResponse;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1SuggestionResult::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1SuggestionResult');
