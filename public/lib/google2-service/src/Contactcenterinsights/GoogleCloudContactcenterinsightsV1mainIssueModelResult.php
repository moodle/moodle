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

class GoogleCloudContactcenterinsightsV1mainIssueModelResult extends \Google\Collection
{
  protected $collection_key = 'issues';
  /**
   * Issue model that generates the result. Format:
   * projects/{project}/locations/{location}/issueModels/{issue_model}
   *
   * @var string
   */
  public $issueModel;
  protected $issuesType = GoogleCloudContactcenterinsightsV1mainIssueAssignment::class;
  protected $issuesDataType = 'array';

  /**
   * Issue model that generates the result. Format:
   * projects/{project}/locations/{location}/issueModels/{issue_model}
   *
   * @param string $issueModel
   */
  public function setIssueModel($issueModel)
  {
    $this->issueModel = $issueModel;
  }
  /**
   * @return string
   */
  public function getIssueModel()
  {
    return $this->issueModel;
  }
  /**
   * All the matched issues.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIssueAssignment[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIssueAssignment[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainIssueModelResult::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainIssueModelResult');
