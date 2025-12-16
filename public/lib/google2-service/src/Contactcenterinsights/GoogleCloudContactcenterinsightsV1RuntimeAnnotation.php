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

class GoogleCloudContactcenterinsightsV1RuntimeAnnotation extends \Google\Model
{
  /**
   * The unique identifier of the annotation. Format: projects/{project}/locatio
   * ns/{location}/conversationDatasets/{dataset}/conversationDataItems/{data_it
   * em}/conversationAnnotations/{annotation}
   *
   * @var string
   */
  public $annotationId;
  protected $answerFeedbackType = GoogleCloudContactcenterinsightsV1AnswerFeedback::class;
  protected $answerFeedbackDataType = '';
  protected $articleSuggestionType = GoogleCloudContactcenterinsightsV1ArticleSuggestionData::class;
  protected $articleSuggestionDataType = '';
  protected $conversationSummarizationSuggestionType = GoogleCloudContactcenterinsightsV1ConversationSummarizationSuggestionData::class;
  protected $conversationSummarizationSuggestionDataType = '';
  /**
   * The time at which this annotation was created.
   *
   * @var string
   */
  public $createTime;
  protected $dialogflowInteractionType = GoogleCloudContactcenterinsightsV1DialogflowInteractionData::class;
  protected $dialogflowInteractionDataType = '';
  protected $endBoundaryType = GoogleCloudContactcenterinsightsV1AnnotationBoundary::class;
  protected $endBoundaryDataType = '';
  protected $faqAnswerType = GoogleCloudContactcenterinsightsV1FaqAnswerData::class;
  protected $faqAnswerDataType = '';
  protected $smartComposeSuggestionType = GoogleCloudContactcenterinsightsV1SmartComposeSuggestionData::class;
  protected $smartComposeSuggestionDataType = '';
  protected $smartReplyType = GoogleCloudContactcenterinsightsV1SmartReplyData::class;
  protected $smartReplyDataType = '';
  protected $startBoundaryType = GoogleCloudContactcenterinsightsV1AnnotationBoundary::class;
  protected $startBoundaryDataType = '';
  protected $userInputType = GoogleCloudContactcenterinsightsV1RuntimeAnnotationUserInput::class;
  protected $userInputDataType = '';

  /**
   * The unique identifier of the annotation. Format: projects/{project}/locatio
   * ns/{location}/conversationDatasets/{dataset}/conversationDataItems/{data_it
   * em}/conversationAnnotations/{annotation}
   *
   * @param string $annotationId
   */
  public function setAnnotationId($annotationId)
  {
    $this->annotationId = $annotationId;
  }
  /**
   * @return string
   */
  public function getAnnotationId()
  {
    return $this->annotationId;
  }
  /**
   * The feedback that the customer has about the answer in `data`.
   *
   * @param GoogleCloudContactcenterinsightsV1AnswerFeedback $answerFeedback
   */
  public function setAnswerFeedback(GoogleCloudContactcenterinsightsV1AnswerFeedback $answerFeedback)
  {
    $this->answerFeedback = $answerFeedback;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnswerFeedback
   */
  public function getAnswerFeedback()
  {
    return $this->answerFeedback;
  }
  /**
   * Agent Assist Article Suggestion data.
   *
   * @param GoogleCloudContactcenterinsightsV1ArticleSuggestionData $articleSuggestion
   */
  public function setArticleSuggestion(GoogleCloudContactcenterinsightsV1ArticleSuggestionData $articleSuggestion)
  {
    $this->articleSuggestion = $articleSuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1ArticleSuggestionData
   */
  public function getArticleSuggestion()
  {
    return $this->articleSuggestion;
  }
  /**
   * Conversation summarization suggestion data.
   *
   * @param GoogleCloudContactcenterinsightsV1ConversationSummarizationSuggestionData $conversationSummarizationSuggestion
   */
  public function setConversationSummarizationSuggestion(GoogleCloudContactcenterinsightsV1ConversationSummarizationSuggestionData $conversationSummarizationSuggestion)
  {
    $this->conversationSummarizationSuggestion = $conversationSummarizationSuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1ConversationSummarizationSuggestionData
   */
  public function getConversationSummarizationSuggestion()
  {
    return $this->conversationSummarizationSuggestion;
  }
  /**
   * The time at which this annotation was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Dialogflow interaction data.
   *
   * @param GoogleCloudContactcenterinsightsV1DialogflowInteractionData $dialogflowInteraction
   */
  public function setDialogflowInteraction(GoogleCloudContactcenterinsightsV1DialogflowInteractionData $dialogflowInteraction)
  {
    $this->dialogflowInteraction = $dialogflowInteraction;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1DialogflowInteractionData
   */
  public function getDialogflowInteraction()
  {
    return $this->dialogflowInteraction;
  }
  /**
   * The boundary in the conversation where the annotation ends, inclusive.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotationBoundary $endBoundary
   */
  public function setEndBoundary(GoogleCloudContactcenterinsightsV1AnnotationBoundary $endBoundary)
  {
    $this->endBoundary = $endBoundary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotationBoundary
   */
  public function getEndBoundary()
  {
    return $this->endBoundary;
  }
  /**
   * Agent Assist FAQ answer data.
   *
   * @param GoogleCloudContactcenterinsightsV1FaqAnswerData $faqAnswer
   */
  public function setFaqAnswer(GoogleCloudContactcenterinsightsV1FaqAnswerData $faqAnswer)
  {
    $this->faqAnswer = $faqAnswer;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1FaqAnswerData
   */
  public function getFaqAnswer()
  {
    return $this->faqAnswer;
  }
  /**
   * Agent Assist Smart Compose suggestion data.
   *
   * @param GoogleCloudContactcenterinsightsV1SmartComposeSuggestionData $smartComposeSuggestion
   */
  public function setSmartComposeSuggestion(GoogleCloudContactcenterinsightsV1SmartComposeSuggestionData $smartComposeSuggestion)
  {
    $this->smartComposeSuggestion = $smartComposeSuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SmartComposeSuggestionData
   */
  public function getSmartComposeSuggestion()
  {
    return $this->smartComposeSuggestion;
  }
  /**
   * Agent Assist Smart Reply data.
   *
   * @param GoogleCloudContactcenterinsightsV1SmartReplyData $smartReply
   */
  public function setSmartReply(GoogleCloudContactcenterinsightsV1SmartReplyData $smartReply)
  {
    $this->smartReply = $smartReply;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SmartReplyData
   */
  public function getSmartReply()
  {
    return $this->smartReply;
  }
  /**
   * The boundary in the conversation where the annotation starts, inclusive.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotationBoundary $startBoundary
   */
  public function setStartBoundary(GoogleCloudContactcenterinsightsV1AnnotationBoundary $startBoundary)
  {
    $this->startBoundary = $startBoundary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotationBoundary
   */
  public function getStartBoundary()
  {
    return $this->startBoundary;
  }
  /**
   * Explicit input used for generating the answer
   *
   * @param GoogleCloudContactcenterinsightsV1RuntimeAnnotationUserInput $userInput
   */
  public function setUserInput(GoogleCloudContactcenterinsightsV1RuntimeAnnotationUserInput $userInput)
  {
    $this->userInput = $userInput;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1RuntimeAnnotationUserInput
   */
  public function getUserInput()
  {
    return $this->userInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1RuntimeAnnotation::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1RuntimeAnnotation');
