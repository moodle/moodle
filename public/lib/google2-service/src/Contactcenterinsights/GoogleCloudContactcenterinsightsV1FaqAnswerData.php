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

class GoogleCloudContactcenterinsightsV1FaqAnswerData extends \Google\Model
{
  /**
   * The piece of text from the `source` knowledge base document.
   *
   * @var string
   */
  public $answer;
  /**
   * The system's confidence score that this answer is a good match for this
   * conversation, ranging from 0.0 (completely uncertain) to 1.0 (completely
   * certain).
   *
   * @var float
   */
  public $confidenceScore;
  /**
   * Map that contains metadata about the FAQ answer and the document that it
   * originates from.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * The name of the answer record. Format:
   * projects/{project}/locations/{location}/answerRecords/{answer_record}
   *
   * @var string
   */
  public $queryRecord;
  /**
   * The corresponding FAQ question.
   *
   * @var string
   */
  public $question;
  /**
   * The knowledge document that this answer was extracted from. Format:
   * projects/{project}/knowledgeBases/{knowledge_base}/documents/{document}.
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
   * The system's confidence score that this answer is a good match for this
   * conversation, ranging from 0.0 (completely uncertain) to 1.0 (completely
   * certain).
   *
   * @param float $confidenceScore
   */
  public function setConfidenceScore($confidenceScore)
  {
    $this->confidenceScore = $confidenceScore;
  }
  /**
   * @return float
   */
  public function getConfidenceScore()
  {
    return $this->confidenceScore;
  }
  /**
   * Map that contains metadata about the FAQ answer and the document that it
   * originates from.
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
   * The name of the answer record. Format:
   * projects/{project}/locations/{location}/answerRecords/{answer_record}
   *
   * @param string $queryRecord
   */
  public function setQueryRecord($queryRecord)
  {
    $this->queryRecord = $queryRecord;
  }
  /**
   * @return string
   */
  public function getQueryRecord()
  {
    return $this->queryRecord;
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
   * The knowledge document that this answer was extracted from. Format:
   * projects/{project}/knowledgeBases/{knowledge_base}/documents/{document}.
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
class_alias(GoogleCloudContactcenterinsightsV1FaqAnswerData::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1FaqAnswerData');
