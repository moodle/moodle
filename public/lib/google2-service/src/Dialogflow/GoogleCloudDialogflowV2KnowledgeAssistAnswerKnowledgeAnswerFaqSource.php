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

class GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerFaqSource extends \Google\Model
{
  /**
   * The corresponding FAQ question.
   *
   * @var string
   */
  public $question;

  /**
   * The corresponding FAQ question.
   *
   * @param string $question
   */
  public function setQuestion($question)
  {
    $this->question = $question;
  }
  /**
   * @return string
   */
  public function getQuestion()
  {
    return $this->question;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerFaqSource::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2KnowledgeAssistAnswerKnowledgeAnswerFaqSource');
