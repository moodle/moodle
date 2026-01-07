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

class GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswer extends \Google\Model
{
  /**
   * @var string
   */
  public $answerText;
  protected $faqSourceType = GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerFaqSource::class;
  protected $faqSourceDataType = '';
  protected $generativeSourceType = GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerGenerativeSource::class;
  protected $generativeSourceDataType = '';
  protected $intentMatchingSourceType = GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerIntentMatchingSource::class;
  protected $intentMatchingSourceDataType = '';
  /**
   * @var float
   */
  public $matchConfidence;

  /**
   * @param string
   */
  public function setAnswerText($answerText)
  {
    $this->answerText = $answerText;
  }
  /**
   * @return string
   */
  public function getAnswerText()
  {
    return $this->answerText;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerFaqSource
   */
  public function setFaqSource(GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerFaqSource $faqSource)
  {
    $this->faqSource = $faqSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerFaqSource
   */
  public function getFaqSource()
  {
    return $this->faqSource;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerGenerativeSource
   */
  public function setGenerativeSource(GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerGenerativeSource $generativeSource)
  {
    $this->generativeSource = $generativeSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerGenerativeSource
   */
  public function getGenerativeSource()
  {
    return $this->generativeSource;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerIntentMatchingSource
   */
  public function setIntentMatchingSource(GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerIntentMatchingSource $intentMatchingSource)
  {
    $this->intentMatchingSource = $intentMatchingSource;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswerIntentMatchingSource
   */
  public function getIntentMatchingSource()
  {
    return $this->intentMatchingSource;
  }
  /**
   * @param float
   */
  public function setMatchConfidence($matchConfidence)
  {
    $this->matchConfidence = $matchConfidence;
  }
  /**
   * @return float
   */
  public function getMatchConfidence()
  {
    return $this->matchConfidence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswer::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1GetKnowledgeAssistResponseKnowledgeAnswer');
