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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1SearchErrorIssuesResponse extends \Google\Collection
{
  protected $collection_key = 'errorIssues';
  protected $errorIssuesType = GooglePlayDeveloperReportingV1beta1ErrorIssue::class;
  protected $errorIssuesDataType = 'array';
  /**
   * Continuation token to fetch the next page of data.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * ErrorIssues that were found.
   *
   * @param GooglePlayDeveloperReportingV1beta1ErrorIssue[] $errorIssues
   */
  public function setErrorIssues($errorIssues)
  {
    $this->errorIssues = $errorIssues;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1ErrorIssue[]
   */
  public function getErrorIssues()
  {
    return $this->errorIssues;
  }
  /**
   * Continuation token to fetch the next page of data.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1SearchErrorIssuesResponse::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1SearchErrorIssuesResponse');
