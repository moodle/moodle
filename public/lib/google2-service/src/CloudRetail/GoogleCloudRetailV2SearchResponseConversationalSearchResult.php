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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2SearchResponseConversationalSearchResult extends \Google\Collection
{
  protected $collection_key = 'suggestedAnswers';
  protected $additionalFilterType = GoogleCloudRetailV2SearchResponseConversationalSearchResultAdditionalFilter::class;
  protected $additionalFilterDataType = '';
  protected $additionalFiltersType = GoogleCloudRetailV2SearchResponseConversationalSearchResultAdditionalFilter::class;
  protected $additionalFiltersDataType = 'array';
  /**
   * Conversation UUID. This field will be stored in client side storage to
   * maintain the conversation session with server and will be used for next
   * search request's SearchRequest.ConversationalSearchSpec.conversation_id to
   * restore conversation state in server.
   *
   * @var string
   */
  public $conversationId;
  /**
   * The follow-up question. e.g., `What is the color?`
   *
   * @var string
   */
  public $followupQuestion;
  /**
   * The current refined query for the conversational search. This field will be
   * used in customer UI that the query in the search bar should be replaced
   * with the refined query. For example, if SearchRequest.query is `dress` and
   * next SearchRequest.ConversationalSearchSpec.UserAnswer.text_answer is `red
   * color`, which does not match any product attribute value filters, the
   * refined query will be `dress, red color`.
   *
   * @var string
   */
  public $refinedQuery;
  protected $suggestedAnswersType = GoogleCloudRetailV2SearchResponseConversationalSearchResultSuggestedAnswer::class;
  protected $suggestedAnswersDataType = 'array';

  /**
   * This is the incremental additional filters implied from the current user
   * answer. User should add the suggested addition filters to the previous
   * SearchRequest.filter, and use the merged filter in the follow up search
   * request.
   *
   * @param GoogleCloudRetailV2SearchResponseConversationalSearchResultAdditionalFilter $additionalFilter
   */
  public function setAdditionalFilter(GoogleCloudRetailV2SearchResponseConversationalSearchResultAdditionalFilter $additionalFilter)
  {
    $this->additionalFilter = $additionalFilter;
  }
  /**
   * @return GoogleCloudRetailV2SearchResponseConversationalSearchResultAdditionalFilter
   */
  public function getAdditionalFilter()
  {
    return $this->additionalFilter;
  }
  /**
   * This field is deprecated but will be kept for backward compatibility. There
   * is expected to have only one additional filter and the value will be the
   * same to the same as field `additional_filter`.
   *
   * @deprecated
   * @param GoogleCloudRetailV2SearchResponseConversationalSearchResultAdditionalFilter[] $additionalFilters
   */
  public function setAdditionalFilters($additionalFilters)
  {
    $this->additionalFilters = $additionalFilters;
  }
  /**
   * @deprecated
   * @return GoogleCloudRetailV2SearchResponseConversationalSearchResultAdditionalFilter[]
   */
  public function getAdditionalFilters()
  {
    return $this->additionalFilters;
  }
  /**
   * Conversation UUID. This field will be stored in client side storage to
   * maintain the conversation session with server and will be used for next
   * search request's SearchRequest.ConversationalSearchSpec.conversation_id to
   * restore conversation state in server.
   *
   * @param string $conversationId
   */
  public function setConversationId($conversationId)
  {
    $this->conversationId = $conversationId;
  }
  /**
   * @return string
   */
  public function getConversationId()
  {
    return $this->conversationId;
  }
  /**
   * The follow-up question. e.g., `What is the color?`
   *
   * @param string $followupQuestion
   */
  public function setFollowupQuestion($followupQuestion)
  {
    $this->followupQuestion = $followupQuestion;
  }
  /**
   * @return string
   */
  public function getFollowupQuestion()
  {
    return $this->followupQuestion;
  }
  /**
   * The current refined query for the conversational search. This field will be
   * used in customer UI that the query in the search bar should be replaced
   * with the refined query. For example, if SearchRequest.query is `dress` and
   * next SearchRequest.ConversationalSearchSpec.UserAnswer.text_answer is `red
   * color`, which does not match any product attribute value filters, the
   * refined query will be `dress, red color`.
   *
   * @param string $refinedQuery
   */
  public function setRefinedQuery($refinedQuery)
  {
    $this->refinedQuery = $refinedQuery;
  }
  /**
   * @return string
   */
  public function getRefinedQuery()
  {
    return $this->refinedQuery;
  }
  /**
   * The answer options provided to client for the follow-up question.
   *
   * @param GoogleCloudRetailV2SearchResponseConversationalSearchResultSuggestedAnswer[] $suggestedAnswers
   */
  public function setSuggestedAnswers($suggestedAnswers)
  {
    $this->suggestedAnswers = $suggestedAnswers;
  }
  /**
   * @return GoogleCloudRetailV2SearchResponseConversationalSearchResultSuggestedAnswer[]
   */
  public function getSuggestedAnswers()
  {
    return $this->suggestedAnswers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchResponseConversationalSearchResult::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchResponseConversationalSearchResult');
