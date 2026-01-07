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

class GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswer extends \Google\Model
{
  /**
   * The piece of text from the `source` that answers this suggested query.
   *
   * @var string
   */
  public $answerText;
  protected $faqSourceType = GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerFaqSource::class;
  protected $faqSourceDataType = '';
  protected $generativeSourceType = GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerGenerativeSource::class;
  protected $generativeSourceDataType = '';

  /**
   * The piece of text from the `source` that answers this suggested query.
   *
   * @param string $answerText
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
   * Populated if the prediction came from FAQ.
   *
   * @param GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerFaqSource $faqSource
   */
  public function setFaqSource(GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerFaqSource $faqSource)
  {
    $this->faqSource = $faqSource;
  }
  /**
   * @return GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerFaqSource
   */
  public function getFaqSource()
  {
    return $this->faqSource;
  }
  /**
   * Populated if the prediction was Generative.
   *
   * @param GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerGenerativeSource $generativeSource
   */
  public function setGenerativeSource(GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerGenerativeSource $generativeSource)
  {
    $this->generativeSource = $generativeSource;
  }
  /**
   * @return GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerGenerativeSource
   */
  public function getGenerativeSource()
  {
    return $this->generativeSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswer::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswer');
