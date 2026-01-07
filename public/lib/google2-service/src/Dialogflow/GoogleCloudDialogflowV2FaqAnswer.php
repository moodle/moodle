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

class GoogleCloudDialogflowV2FaqAnswer extends \Google\Model
{
  /**
   * The piece of text from the `source` knowledge base document.
   *
   * @var string
   */
  public $answer;
  /**
   * The name of answer record, in the format of
   * "projects//locations//answerRecords/"
   *
   * @var string
   */
  public $answerRecord;
  /**
   * The system's confidence score that this Knowledge answer is a good match
   * for this conversational query, range from 0.0 (completely uncertain) to 1.0
   * (completely certain).
   *
   * @var float
   */
  public $confidence;
  /**
   * A map that contains metadata about the answer and the document from which
   * it originates.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * The corresponding FAQ question.
   *
   * @var string
   */
  public $question;
  /**
   * Indicates which Knowledge Document this answer was extracted from. Format:
   * `projects//locations//agent/knowledgeBases//documents/`.
   *
   * @var string
   */
  public $source;

  /**
   * The piece of text from the `source` knowledge base document.
   *
   * @param string $answer
   */
  public function setAnswer($answer)
  {
    $this->answer = $answer;
  }
  /**
   * @return string
   */
  public function getAnswer()
  {
    return $this->answer;
  }
  /**
   * The name of answer record, in the format of
   * "projects//locations//answerRecords/"
   *
   * @param string $answerRecord
   */
  public function setAnswerRecord($answerRecord)
  {
    $this->answerRecord = $answerRecord;
  }
  /**
   * @return string
   */
  public function getAnswerRecord()
  {
    return $this->answerRecord;
  }
  /**
   * The system's confidence score that this Knowledge answer is a good match
   * for this conversational query, range from 0.0 (completely uncertain) to 1.0
   * (completely certain).
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * A map that contains metadata about the answer and the document from which
   * it originates.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
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
  /**
   * Indicates which Knowledge Document this answer was extracted from. Format:
   * `projects//locations//agent/knowledgeBases//documents/`.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2FaqAnswer::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2FaqAnswer');
