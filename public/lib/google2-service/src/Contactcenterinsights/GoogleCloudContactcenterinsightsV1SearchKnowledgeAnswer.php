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

class GoogleCloudContactcenterinsightsV1SearchKnowledgeAnswer extends \Google\Collection
{
  protected $collection_key = 'answerSources';
  /**
   * @var string
   */
  public $answer;
  /**
   * @var string
   */
  public $answerRecord;
  protected $answerSourcesType = GoogleCloudContactcenterinsightsV1SearchKnowledgeAnswerAnswerSource::class;
  protected $answerSourcesDataType = 'array';
  /**
   * @var string
   */
  public $answerType;
  /**
   * @var float
   */
  public $confidenceScore;

  /**
   * @param string
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
   * @param string
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
   * @param GoogleCloudContactcenterinsightsV1SearchKnowledgeAnswerAnswerSource[]
   */
  public function setAnswerSources($answerSources)
  {
    $this->answerSources = $answerSources;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SearchKnowledgeAnswerAnswerSource[]
   */
  public function getAnswerSources()
  {
    return $this->answerSources;
  }
  /**
   * @param string
   */
  public function setAnswerType($answerType)
  {
    $this->answerType = $answerType;
  }
  /**
   * @return string
   */
  public function getAnswerType()
  {
    return $this->answerType;
  }
  /**
   * @param float
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1SearchKnowledgeAnswer::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1SearchKnowledgeAnswer');
