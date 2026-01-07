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

class GoogleCloudRetailV2ConversationalSearchResponseFollowupQuestion extends \Google\Collection
{
  protected $collection_key = 'suggestedAnswers';
  /**
   * The conversational followup question generated for Intent refinement.
   *
   * @var string
   */
  public $followupQuestion;
  protected $suggestedAnswersType = GoogleCloudRetailV2ConversationalSearchResponseFollowupQuestionSuggestedAnswer::class;
  protected $suggestedAnswersDataType = 'array';

  /**
   * The conversational followup question generated for Intent refinement.
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
   * The answer options provided to client for the follow-up question.
   *
   * @param GoogleCloudRetailV2ConversationalSearchResponseFollowupQuestionSuggestedAnswer[] $suggestedAnswers
   */
  public function setSuggestedAnswers($suggestedAnswers)
  {
    $this->suggestedAnswers = $suggestedAnswers;
  }
  /**
   * @return GoogleCloudRetailV2ConversationalSearchResponseFollowupQuestionSuggestedAnswer[]
   */
  public function getSuggestedAnswers()
  {
    return $this->suggestedAnswers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ConversationalSearchResponseFollowupQuestion::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ConversationalSearchResponseFollowupQuestion');
