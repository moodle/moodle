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

class GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswer extends \Google\Model
{
  protected $selectedAnswerType = GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswerSelectedAnswer::class;
  protected $selectedAnswerDataType = '';
  /**
   * This field specifies the incremental input text from the user during the
   * conversational search.
   *
   * @var string
   */
  public $textAnswer;

  /**
   * This field specifies the selected attributes during the conversational
   * search. This should be a subset of
   * ConversationalSearchResult.suggested_answers.
   *
   * @param GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswerSelectedAnswer $selectedAnswer
   */
  public function setSelectedAnswer(GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswerSelectedAnswer $selectedAnswer)
  {
    $this->selectedAnswer = $selectedAnswer;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswerSelectedAnswer
   */
  public function getSelectedAnswer()
  {
    return $this->selectedAnswer;
  }
  /**
   * This field specifies the incremental input text from the user during the
   * conversational search.
   *
   * @param string $textAnswer
   */
  public function setTextAnswer($textAnswer)
  {
    $this->textAnswer = $textAnswer;
  }
  /**
   * @return string
   */
  public function getTextAnswer()
  {
    return $this->textAnswer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswer::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswer');
