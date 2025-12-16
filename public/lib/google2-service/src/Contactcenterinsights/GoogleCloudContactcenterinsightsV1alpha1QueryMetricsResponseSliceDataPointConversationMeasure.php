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

class GoogleCloudContactcenterinsightsV1alpha1QueryMetricsResponseSliceDataPointConversationMeasure extends \Google\Collection
{
  protected $collection_key = 'qaTagScores';
  /**
   * The average agent's sentiment score.
   *
   * @var float
   */
  public $averageAgentSentimentScore;
  /**
   * The average client's sentiment score.
   *
   * @var float
   */
  public $averageClientSentimentScore;
  /**
   * The average customer satisfaction rating.
   *
   * @var 
   */
  public $averageCustomerSatisfactionRating;
  /**
   * The average duration.
   *
   * @var string
   */
  public $averageDuration;
  /**
   * The average normalized QA score for a scorecard. When computing the average
   * across a set of conversations, if a conversation has been evaluated with
   * multiple revisions of a scorecard, only the latest revision results will be
   * used. Will exclude 0's in average calculation. Will be only populated if
   * the request specifies a dimension of QA_SCORECARD_ID.
   *
   * @var 
   */
  public $averageQaNormalizedScore;
  /**
   * Average QA normalized score averaged for questions averaged across all
   * revisions of the parent scorecard. Will be only populated if the request
   * specifies a dimension of QA_QUESTION_ID.
   *
   * @var 
   */
  public $averageQaQuestionNormalizedScore;
  /**
   * The average silence percentage.
   *
   * @var float
   */
  public $averageSilencePercentage;
  /**
   * The average turn count.
   *
   * @var float
   */
  public $averageTurnCount;
  /**
   * The conversation count.
   *
   * @var int
   */
  public $conversationCount;
  protected $qaTagScoresType = GoogleCloudContactcenterinsightsV1alpha1QueryMetricsResponseSliceDataPointConversationMeasureQaTagScore::class;
  protected $qaTagScoresDataType = 'array';

  /**
   * The average agent's sentiment score.
   *
   * @param float $averageAgentSentimentScore
   */
  public function setAverageAgentSentimentScore($averageAgentSentimentScore)
  {
    $this->averageAgentSentimentScore = $averageAgentSentimentScore;
  }
  /**
   * @return float
   */
  public function getAverageAgentSentimentScore()
  {
    return $this->averageAgentSentimentScore;
  }
  /**
   * The average client's sentiment score.
   *
   * @param float $averageClientSentimentScore
   */
  public function setAverageClientSentimentScore($averageClientSentimentScore)
  {
    $this->averageClientSentimentScore = $averageClientSentimentScore;
  }
  /**
   * @return float
   */
  public function getAverageClientSentimentScore()
  {
    return $this->averageClientSentimentScore;
  }
  public function setAverageCustomerSatisfactionRating($averageCustomerSatisfactionRating)
  {
    $this->averageCustomerSatisfactionRating = $averageCustomerSatisfactionRating;
  }
  public function getAverageCustomerSatisfactionRating()
  {
    return $this->averageCustomerSatisfactionRating;
  }
  /**
   * The average duration.
   *
   * @param string $averageDuration
   */
  public function setAverageDuration($averageDuration)
  {
    $this->averageDuration = $averageDuration;
  }
  /**
   * @return string
   */
  public function getAverageDuration()
  {
    return $this->averageDuration;
  }
  public function setAverageQaNormalizedScore($averageQaNormalizedScore)
  {
    $this->averageQaNormalizedScore = $averageQaNormalizedScore;
  }
  public function getAverageQaNormalizedScore()
  {
    return $this->averageQaNormalizedScore;
  }
  public function setAverageQaQuestionNormalizedScore($averageQaQuestionNormalizedScore)
  {
    $this->averageQaQuestionNormalizedScore = $averageQaQuestionNormalizedScore;
  }
  public function getAverageQaQuestionNormalizedScore()
  {
    return $this->averageQaQuestionNormalizedScore;
  }
  /**
   * The average silence percentage.
   *
   * @param float $averageSilencePercentage
   */
  public function setAverageSilencePercentage($averageSilencePercentage)
  {
    $this->averageSilencePercentage = $averageSilencePercentage;
  }
  /**
   * @return float
   */
  public function getAverageSilencePercentage()
  {
    return $this->averageSilencePercentage;
  }
  /**
   * The average turn count.
   *
   * @param float $averageTurnCount
   */
  public function setAverageTurnCount($averageTurnCount)
  {
    $this->averageTurnCount = $averageTurnCount;
  }
  /**
   * @return float
   */
  public function getAverageTurnCount()
  {
    return $this->averageTurnCount;
  }
  /**
   * The conversation count.
   *
   * @param int $conversationCount
   */
  public function setConversationCount($conversationCount)
  {
    $this->conversationCount = $conversationCount;
  }
  /**
   * @return int
   */
  public function getConversationCount()
  {
    return $this->conversationCount;
  }
  /**
   * Average QA normalized score for all the tags.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1QueryMetricsResponseSliceDataPointConversationMeasureQaTagScore[] $qaTagScores
   */
  public function setQaTagScores($qaTagScores)
  {
    $this->qaTagScores = $qaTagScores;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1QueryMetricsResponseSliceDataPointConversationMeasureQaTagScore[]
   */
  public function getQaTagScores()
  {
    return $this->qaTagScores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1QueryMetricsResponseSliceDataPointConversationMeasure::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1QueryMetricsResponseSliceDataPointConversationMeasure');
