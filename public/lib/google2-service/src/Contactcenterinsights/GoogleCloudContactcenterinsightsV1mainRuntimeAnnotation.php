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

class GoogleCloudContactcenterinsightsV1mainRuntimeAnnotation extends \Google\Model
{
  /**
   * The unique identifier of the annotation. Format: projects/{project}/locatio
   * ns/{location}/conversationDatasets/{dataset}/conversationDataItems/{data_it
   * em}/conversationAnnotations/{annotation}
   *
   * @var string
   */
  public $annotationId;
  protected $answerFeedbackType = GoogleCloudContactcenterinsightsV1mainAnswerFeedback::class;
  protected $answerFeedbackDataType = '';
  protected $articleSuggestionType = GoogleCloudContactcenterinsightsV1mainArticleSuggestionData::class;
  protected $articleSuggestionDataType = '';
  protected $conversationSummarizationSuggestionType = GoogleCloudContactcenterinsightsV1mainConversationSummarizationSuggestionData::class;
  protected $conversationSummarizationSuggestionDataType = '';
  /**
   * The time at which this annotation was created.
   *
   * @var string
   */
  public $createTime;
  protected $dialogflowInteractionType = GoogleCloudContactcenterinsightsV1mainDialogflowInteractionData::class;
  protected $dialogflowInteractionDataType = '';
  protected $endBoundaryType = GoogleCloudContactcenterinsightsV1mainAnnotationBoundary::class;
  protected $endBoundaryDataType = '';
  protected $faqAnswerType = GoogleCloudContactcenterinsightsV1mainFaqAnswerData::class;
  protected $faqAnswerDataType = '';
  protected $smartComposeSuggestionType = GoogleCloudContactcenterinsightsV1mainSmartComposeSuggestionData::class;
  protected $smartComposeSuggestionDataType = '';
  protected $smartReplyType = GoogleCloudContactcenterinsightsV1mainSmartReplyData::class;
  protected $smartReplyDataType = '';
  protected $startBoundaryType = GoogleCloudContactcenterinsightsV1mainAnnotationBoundary::class;
  protected $startBoundaryDataType = '';
  protected $userInputType = GoogleCloudContactcenterinsightsV1mainRuntimeAnnotationUserInput::class;
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
   * @param GoogleCloudContactcenterinsightsV1mainAnswerFeedback $answerFeedback
   */
  public function setAnswerFeedback(GoogleCloudContactcenterinsightsV1mainAnswerFeedback $answerFeedback)
  {
    $this->answerFeedback = $answerFeedback;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainAnswerFeedback
   */
  public function getAnswerFeedback()
  {
    return $this->answerFeedback;
  }
  /**
   * Agent Assist Article Suggestion data.
   *
   * @param GoogleCloudContactcenterinsightsV1mainArticleSuggestionData $articleSuggestion
   */
  public function setArticleSuggestion(GoogleCloudContactcenterinsightsV1mainArticleSuggestionData $articleSuggestion)
  {
    $this->articleSuggestion = $articleSuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainArticleSuggestionData
   */
  public function getArticleSuggestion()
  {
    return $this->articleSuggestion;
  }
  /**
   * Conversation summarization suggestion data.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationSummarizationSuggestionData $conversationSummarizationSuggestion
   */
  public function setConversationSummarizationSuggestion(GoogleCloudContactcenterinsightsV1mainConversationSummarizationSuggestionData $conversationSummarizationSuggestion)
  {
    $this->conversationSummarizationSuggestion = $conversationSummarizationSuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationSummarizationSuggestionData
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
   * @param GoogleCloudContactcenterinsightsV1mainDialogflowInteractionData $dialogflowInteraction
   */
  public function setDialogflowInteraction(GoogleCloudContactcenterinsightsV1mainDialogflowInteractionData $dialogflowInteraction)
  {
    $this->dialogflowInteraction = $dialogflowInteraction;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainDialogflowInteractionData
   */
  public function getDialogflowInteraction()
  {
    return $this->dialogflowInteraction;
  }
  /**
   * The boundary in the conversation where the annotation ends, inclusive.
   *
   * @param GoogleCloudContactcenterinsightsV1mainAnnotationBoundary $endBoundary
   */
  public function setEndBoundary(GoogleCloudContactcenterinsightsV1mainAnnotationBoundary $endBoundary)
  {
    $this->endBoundary = $endBoundary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainAnnotationBoundary
   */
  public function getEndBoundary()
  {
    return $this->endBoundary;
  }
  /**
   * Agent Assist FAQ answer data.
   *
   * @param GoogleCloudContactcenterinsightsV1mainFaqAnswerData $faqAnswer
   */
  public function setFaqAnswer(GoogleCloudContactcenterinsightsV1mainFaqAnswerData $faqAnswer)
  {
    $this->faqAnswer = $faqAnswer;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainFaqAnswerData
   */
  public function getFaqAnswer()
  {
    return $this->faqAnswer;
  }
  /**
   * Agent Assist Smart Compose suggestion data.
   *
   * @param GoogleCloudContactcenterinsightsV1mainSmartComposeSuggestionData $smartComposeSuggestion
   */
  public function setSmartComposeSuggestion(GoogleCloudContactcenterinsightsV1mainSmartComposeSuggestionData $smartComposeSuggestion)
  {
    $this->smartComposeSuggestion = $smartComposeSuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainSmartComposeSuggestionData
   */
  public function getSmartComposeSuggestion()
  {
    return $this->smartComposeSuggestion;
  }
  /**
   * Agent Assist Smart Reply data.
   *
   * @param GoogleCloudContactcenterinsightsV1mainSmartReplyData $smartReply
   */
  public function setSmartReply(GoogleCloudContactcenterinsightsV1mainSmartReplyData $smartReply)
  {
    $this->smartReply = $smartReply;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainSmartReplyData
   */
  public function getSmartReply()
  {
    return $this->smartReply;
  }
  /**
   * The boundary in the conversation where the annotation starts, inclusive.
   *
   * @param GoogleCloudContactcenterinsightsV1mainAnnotationBoundary $startBoundary
   */
  public function setStartBoundary(GoogleCloudContactcenterinsightsV1mainAnnotationBoundary $startBoundary)
  {
    $this->startBoundary = $startBoundary;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainAnnotationBoundary
   */
  public function getStartBoundary()
  {
    return $this->startBoundary;
  }
  /**
   * Explicit input used for generating the answer
   *
   * @param GoogleCloudContactcenterinsightsV1mainRuntimeAnnotationUserInput $userInput
   */
  public function setUserInput(GoogleCloudContactcenterinsightsV1mainRuntimeAnnotationUserInput $userInput)
  {
    $this->userInput = $userInput;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainRuntimeAnnotationUserInput
   */
  public function getUserInput()
  {
    return $this->userInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainRuntimeAnnotation::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainRuntimeAnnotation');
