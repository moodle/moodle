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

class GoogleCloudContactcenterinsightsV1mainIssueModelLabelStats extends \Google\Model
{
  /**
   * Number of conversations the issue model has analyzed at this point in time.
   *
   * @var string
   */
  public $analyzedConversationsCount;
  protected $issueStatsType = GoogleCloudContactcenterinsightsV1mainIssueModelLabelStatsIssueStats::class;
  protected $issueStatsDataType = 'map';
  /**
   * Number of analyzed conversations for which no issue was applicable at this
   * point in time.
   *
   * @var string
   */
  public $unclassifiedConversationsCount;

  /**
   * Number of conversations the issue model has analyzed at this point in time.
   *
   * @param string $analyzedConversationsCount
   */
  public function setAnalyzedConversationsCount($analyzedConversationsCount)
  {
    $this->analyzedConversationsCount = $analyzedConversationsCount;
  }
  /**
   * @return string
   */
  public function getAnalyzedConversationsCount()
  {
    return $this->analyzedConversationsCount;
  }
  /**
   * Statistics on each issue. Key is the issue's resource name.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIssueModelLabelStatsIssueStats[] $issueStats
   */
  public function setIssueStats($issueStats)
  {
    $this->issueStats = $issueStats;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIssueModelLabelStatsIssueStats[]
   */
  public function getIssueStats()
  {
    return $this->issueStats;
  }
  /**
   * Number of analyzed conversations for which no issue was applicable at this
   * point in time.
   *
   * @param string $unclassifiedConversationsCount
   */
  public function setUnclassifiedConversationsCount($unclassifiedConversationsCount)
  {
    $this->unclassifiedConversationsCount = $unclassifiedConversationsCount;
  }
  /**
   * @return string
   */
  public function getUnclassifiedConversationsCount()
  {
    return $this->unclassifiedConversationsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainIssueModelLabelStats::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainIssueModelLabelStats');
