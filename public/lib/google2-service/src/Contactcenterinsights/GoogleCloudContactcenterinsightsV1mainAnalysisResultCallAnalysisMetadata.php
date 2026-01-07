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

class GoogleCloudContactcenterinsightsV1mainAnalysisResultCallAnalysisMetadata extends \Google\Collection
{
  protected $collection_key = 'sentiments';
  protected $annotationsType = GoogleCloudContactcenterinsightsV1mainCallAnnotation::class;
  protected $annotationsDataType = 'array';
  protected $entitiesType = GoogleCloudContactcenterinsightsV1mainEntity::class;
  protected $entitiesDataType = 'map';
  protected $intentsType = GoogleCloudContactcenterinsightsV1mainIntent::class;
  protected $intentsDataType = 'map';
  protected $issueModelResultType = GoogleCloudContactcenterinsightsV1mainIssueModelResult::class;
  protected $issueModelResultDataType = '';
  protected $phraseMatchersType = GoogleCloudContactcenterinsightsV1mainPhraseMatchData::class;
  protected $phraseMatchersDataType = 'map';
  protected $qaScorecardResultsType = GoogleCloudContactcenterinsightsV1mainQaScorecardResult::class;
  protected $qaScorecardResultsDataType = 'array';
  protected $sentimentsType = GoogleCloudContactcenterinsightsV1mainConversationLevelSentiment::class;
  protected $sentimentsDataType = 'array';
  protected $silenceType = GoogleCloudContactcenterinsightsV1mainConversationLevelSilence::class;
  protected $silenceDataType = '';

  /**
   * A list of call annotations that apply to this call.
   *
   * @param GoogleCloudContactcenterinsightsV1mainCallAnnotation[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainCallAnnotation[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * All the entities in the call.
   *
   * @param GoogleCloudContactcenterinsightsV1mainEntity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainEntity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * All the matched intents in the call.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIntent[] $intents
   */
  public function setIntents($intents)
  {
    $this->intents = $intents;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIntent[]
   */
  public function getIntents()
  {
    return $this->intents;
  }
  /**
   * Overall conversation-level issue modeling result.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIssueModelResult $issueModelResult
   */
  public function setIssueModelResult(GoogleCloudContactcenterinsightsV1mainIssueModelResult $issueModelResult)
  {
    $this->issueModelResult = $issueModelResult;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIssueModelResult
   */
  public function getIssueModelResult()
  {
    return $this->issueModelResult;
  }
  /**
   * All the matched phrase matchers in the call.
   *
   * @param GoogleCloudContactcenterinsightsV1mainPhraseMatchData[] $phraseMatchers
   */
  public function setPhraseMatchers($phraseMatchers)
  {
    $this->phraseMatchers = $phraseMatchers;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainPhraseMatchData[]
   */
  public function getPhraseMatchers()
  {
    return $this->phraseMatchers;
  }
  /**
   * Results of scoring QaScorecards.
   *
   * @param GoogleCloudContactcenterinsightsV1mainQaScorecardResult[] $qaScorecardResults
   */
  public function setQaScorecardResults($qaScorecardResults)
  {
    $this->qaScorecardResults = $qaScorecardResults;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainQaScorecardResult[]
   */
  public function getQaScorecardResults()
  {
    return $this->qaScorecardResults;
  }
  /**
   * Overall conversation-level sentiment for each channel of the call.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationLevelSentiment[] $sentiments
   */
  public function setSentiments($sentiments)
  {
    $this->sentiments = $sentiments;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationLevelSentiment[]
   */
  public function getSentiments()
  {
    return $this->sentiments;
  }
  /**
   * Overall conversation-level silence during the call.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationLevelSilence $silence
   */
  public function setSilence(GoogleCloudContactcenterinsightsV1mainConversationLevelSilence $silence)
  {
    $this->silence = $silence;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationLevelSilence
   */
  public function getSilence()
  {
    return $this->silence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainAnalysisResultCallAnalysisMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainAnalysisResultCallAnalysisMetadata');
