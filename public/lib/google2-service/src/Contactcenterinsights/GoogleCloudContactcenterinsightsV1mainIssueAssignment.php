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

class GoogleCloudContactcenterinsightsV1mainIssueAssignment extends \Google\Model
{
  /**
   * Immutable. Display name of the assigned issue. This field is set at time of
   * analysis and immutable since then.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource name of the assigned issue.
   *
   * @var string
   */
  public $issue;
  /**
   * Score indicating the likelihood of the issue assignment. currently bounded
   * on [0,1].
   *
   * @var 
   */
  public $score;

  /**
   * Immutable. Display name of the assigned issue. This field is set at time of
   * analysis and immutable since then.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Resource name of the assigned issue.
   *
   * @param string $issue
   */
  public function setIssue($issue)
  {
    $this->issue = $issue;
  }
  /**
   * @return string
   */
  public function getIssue()
  {
    return $this->issue;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainIssueAssignment::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainIssueAssignment');
