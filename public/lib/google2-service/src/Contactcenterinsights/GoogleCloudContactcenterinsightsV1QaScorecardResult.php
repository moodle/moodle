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

class GoogleCloudContactcenterinsightsV1QaScorecardResult extends \Google\Collection
{
  protected $collection_key = 'scoreSources';
  /**
   * ID of the agent that handled the conversation.
   *
   * @var string
   */
  public $agentId;
  /**
   * The conversation scored by this result.
   *
   * @var string
   */
  public $conversation;
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The name of the scorecard result. Format: projects/{project}/lo
   * cations/{location}/qaScorecardResults/{qa_scorecard_result}
   *
   * @var string
   */
  public $name;
  /**
   * The normalized score, which is the score divided by the potential score.
   * Any manual edits are included if they exist.
   *
   * @var 
   */
  public $normalizedScore;
  /**
   * The maximum potential overall score of the scorecard. Any questions
   * answered using `na_value` are excluded from this calculation.
   *
   * @var 
   */
  public $potentialScore;
  protected $qaAnswersType = GoogleCloudContactcenterinsightsV1QaAnswer::class;
  protected $qaAnswersDataType = 'array';
  /**
   * The QaScorecardRevision scored by this result.
   *
   * @var string
   */
  public $qaScorecardRevision;
  protected $qaTagResultsType = GoogleCloudContactcenterinsightsV1QaScorecardResultQaTagResult::class;
  protected $qaTagResultsDataType = 'array';
  /**
   * The overall numerical score of the result, incorporating any manual edits
   * if they exist.
   *
   * @var 
   */
  public $score;
  protected $scoreSourcesType = GoogleCloudContactcenterinsightsV1QaScorecardResultScoreSource::class;
  protected $scoreSourcesDataType = 'array';

  /**
   * ID of the agent that handled the conversation.
   *
   * @param string $agentId
   */
  public function setAgentId($agentId)
  {
    $this->agentId = $agentId;
  }
  /**
   * @return string
   */
  public function getAgentId()
  {
    return $this->agentId;
  }
  /**
   * The conversation scored by this result.
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
   * Output only. The timestamp that the revision was created.
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
   * Identifier. The name of the scorecard result. Format: projects/{project}/lo
   * cations/{location}/qaScorecardResults/{qa_scorecard_result}
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  public function setNormalizedScore($normalizedScore)
  {
    $this->normalizedScore = $normalizedScore;
  }
  public function getNormalizedScore()
  {
    return $this->normalizedScore;
  }
  public function setPotentialScore($potentialScore)
  {
    $this->potentialScore = $potentialScore;
  }
  public function getPotentialScore()
  {
    return $this->potentialScore;
  }
  /**
   * Set of QaAnswers represented in the result.
   *
   * @param GoogleCloudContactcenterinsightsV1QaAnswer[] $qaAnswers
   */
  public function setQaAnswers($qaAnswers)
  {
    $this->qaAnswers = $qaAnswers;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QaAnswer[]
   */
  public function getQaAnswers()
  {
    return $this->qaAnswers;
  }
  /**
   * The QaScorecardRevision scored by this result.
   *
   * @param string $qaScorecardRevision
   */
  public function setQaScorecardRevision($qaScorecardRevision)
  {
    $this->qaScorecardRevision = $qaScorecardRevision;
  }
  /**
   * @return string
   */
  public function getQaScorecardRevision()
  {
    return $this->qaScorecardRevision;
  }
  /**
   * Collection of tags and their scores.
   *
   * @param GoogleCloudContactcenterinsightsV1QaScorecardResultQaTagResult[] $qaTagResults
   */
  public function setQaTagResults($qaTagResults)
  {
    $this->qaTagResults = $qaTagResults;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QaScorecardResultQaTagResult[]
   */
  public function getQaTagResults()
  {
    return $this->qaTagResults;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * List of all individual score sets.
   *
   * @param GoogleCloudContactcenterinsightsV1QaScorecardResultScoreSource[] $scoreSources
   */
  public function setScoreSources($scoreSources)
  {
    $this->scoreSources = $scoreSources;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QaScorecardResultScoreSource[]
   */
  public function getScoreSources()
  {
    return $this->scoreSources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1QaScorecardResult::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QaScorecardResult');
