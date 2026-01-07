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

class GoogleCloudContactcenterinsightsV1CalculateStatsResponse extends \Google\Model
{
  /**
   * The average duration of all conversations. The average is calculated using
   * only conversations that have a time duration.
   *
   * @var string
   */
  public $averageDuration;
  /**
   * The average number of turns per conversation.
   *
   * @var int
   */
  public $averageTurnCount;
  /**
   * The total number of conversations.
   *
   * @var int
   */
  public $conversationCount;
  protected $conversationCountTimeSeriesType = GoogleCloudContactcenterinsightsV1CalculateStatsResponseTimeSeries::class;
  protected $conversationCountTimeSeriesDataType = '';
  /**
   * A map associating each custom highlighter resource name with its respective
   * number of matches in the set of conversations.
   *
   * @var int[]
   */
  public $customHighlighterMatches;
  /**
   * A map associating each issue resource name with its respective number of
   * matches in the set of conversations. Key has the format:
   * `projects//locations//issueModels//issues/` Deprecated, use
   * `issue_matches_stats` field instead.
   *
   * @deprecated
   * @var int[]
   */
  public $issueMatches;
  protected $issueMatchesStatsType = GoogleCloudContactcenterinsightsV1IssueModelLabelStatsIssueStats::class;
  protected $issueMatchesStatsDataType = 'map';
  /**
   * A map associating each smart highlighter display name with its respective
   * number of matches in the set of conversations.
   *
   * @var int[]
   */
  public $smartHighlighterMatches;

  /**
   * The average duration of all conversations. The average is calculated using
   * only conversations that have a time duration.
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
  /**
   * The average number of turns per conversation.
   *
   * @param int $averageTurnCount
   */
  public function setAverageTurnCount($averageTurnCount)
  {
    $this->averageTurnCount = $averageTurnCount;
  }
  /**
   * @return int
   */
  public function getAverageTurnCount()
  {
    return $this->averageTurnCount;
  }
  /**
   * The total number of conversations.
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
   * A time series representing the count of conversations created over time
   * that match that requested filter criteria.
   *
   * @param GoogleCloudContactcenterinsightsV1CalculateStatsResponseTimeSeries $conversationCountTimeSeries
   */
  public function setConversationCountTimeSeries(GoogleCloudContactcenterinsightsV1CalculateStatsResponseTimeSeries $conversationCountTimeSeries)
  {
    $this->conversationCountTimeSeries = $conversationCountTimeSeries;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1CalculateStatsResponseTimeSeries
   */
  public function getConversationCountTimeSeries()
  {
    return $this->conversationCountTimeSeries;
  }
  /**
   * A map associating each custom highlighter resource name with its respective
   * number of matches in the set of conversations.
   *
   * @param int[] $customHighlighterMatches
   */
  public function setCustomHighlighterMatches($customHighlighterMatches)
  {
    $this->customHighlighterMatches = $customHighlighterMatches;
  }
  /**
   * @return int[]
   */
  public function getCustomHighlighterMatches()
  {
    return $this->customHighlighterMatches;
  }
  /**
   * A map associating each issue resource name with its respective number of
   * matches in the set of conversations. Key has the format:
   * `projects//locations//issueModels//issues/` Deprecated, use
   * `issue_matches_stats` field instead.
   *
   * @deprecated
   * @param int[] $issueMatches
   */
  public function setIssueMatches($issueMatches)
  {
    $this->issueMatches = $issueMatches;
  }
  /**
   * @deprecated
   * @return int[]
   */
  public function getIssueMatches()
  {
    return $this->issueMatches;
  }
  /**
   * A map associating each issue resource name with its respective number of
   * matches in the set of conversations. Key has the format:
   * `projects//locations//issueModels//issues/`
   *
   * @param GoogleCloudContactcenterinsightsV1IssueModelLabelStatsIssueStats[] $issueMatchesStats
   */
  public function setIssueMatchesStats($issueMatchesStats)
  {
    $this->issueMatchesStats = $issueMatchesStats;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1IssueModelLabelStatsIssueStats[]
   */
  public function getIssueMatchesStats()
  {
    return $this->issueMatchesStats;
  }
  /**
   * A map associating each smart highlighter display name with its respective
   * number of matches in the set of conversations.
   *
   * @param int[] $smartHighlighterMatches
   */
  public function setSmartHighlighterMatches($smartHighlighterMatches)
  {
    $this->smartHighlighterMatches = $smartHighlighterMatches;
  }
  /**
   * @return int[]
   */
  public function getSmartHighlighterMatches()
  {
    return $this->smartHighlighterMatches;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1CalculateStatsResponse::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1CalculateStatsResponse');
