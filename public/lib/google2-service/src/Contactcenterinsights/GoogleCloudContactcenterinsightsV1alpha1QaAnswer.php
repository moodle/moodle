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

class GoogleCloudContactcenterinsightsV1alpha1QaAnswer extends \Google\Collection
{
  protected $collection_key = 'tags';
  protected $answerSourcesType = GoogleCloudContactcenterinsightsV1alpha1QaAnswerAnswerSource::class;
  protected $answerSourcesDataType = 'array';
  protected $answerValueType = GoogleCloudContactcenterinsightsV1alpha1QaAnswerAnswerValue::class;
  protected $answerValueDataType = '';
  /**
   * The conversation the answer applies to.
   *
   * @var string
   */
  public $conversation;
  /**
   * The QaQuestion answered by this answer.
   *
   * @var string
   */
  public $qaQuestion;
  /**
   * Question text. E.g., "Did the agent greet the customer?"
   *
   * @var string
   */
  public $questionBody;
  /**
   * User-defined list of arbitrary tags. Matches the value from
   * QaScorecard.ScorecardQuestion.tags. Used for grouping/organization and for
   * weighting the score of each answer.
   *
   * @var string[]
   */
  public $tags;

  /**
   * List of all individual answers given to the question.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1QaAnswerAnswerSource[] $answerSources
   */
  public function setAnswerSources($answerSources)
  {
    $this->answerSources = $answerSources;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1QaAnswerAnswerSource[]
   */
  public function getAnswerSources()
  {
    return $this->answerSources;
  }
  /**
   * The main answer value, incorporating any manual edits if they exist.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1QaAnswerAnswerValue $answerValue
   */
  public function setAnswerValue(GoogleCloudContactcenterinsightsV1alpha1QaAnswerAnswerValue $answerValue)
  {
    $this->answerValue = $answerValue;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1QaAnswerAnswerValue
   */
  public function getAnswerValue()
  {
    return $this->answerValue;
  }
  /**
   * The conversation the answer applies to.
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
   * The QaQuestion answered by this answer.
   *
   * @param string $qaQuestion
   */
  public function setQaQuestion($qaQuestion)
  {
    $this->qaQuestion = $qaQuestion;
  }
  /**
   * @return string
   */
  public function getQaQuestion()
  {
    return $this->qaQuestion;
  }
  /**
   * Question text. E.g., "Did the agent greet the customer?"
   *
   * @param string $questionBody
   */
  public function setQuestionBody($questionBody)
  {
    $this->questionBody = $questionBody;
  }
  /**
   * @return string
   */
  public function getQuestionBody()
  {
    return $this->questionBody;
  }
  /**
   * User-defined list of arbitrary tags. Matches the value from
   * QaScorecard.ScorecardQuestion.tags. Used for grouping/organization and for
   * weighting the score of each answer.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1QaAnswer::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1QaAnswer');
