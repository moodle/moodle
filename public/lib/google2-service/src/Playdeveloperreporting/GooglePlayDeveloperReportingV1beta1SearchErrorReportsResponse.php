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

class GooglePlayDeveloperReportingV1beta1SearchErrorReportsResponse extends \Google\Collection
{
  protected $collection_key = 'errorReports';
  protected $errorReportsType = GooglePlayDeveloperReportingV1beta1ErrorReport::class;
  protected $errorReportsDataType = 'array';
  /**
   * Page token to fetch the next page of reports.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Error reports that were found.
   *
   * @param GooglePlayDeveloperReportingV1beta1ErrorReport[] $errorReports
   */
  public function setErrorReports($errorReports)
  {
    $this->errorReports = $errorReports;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1ErrorReport[]
   */
  public function getErrorReports()
  {
    return $this->errorReports;
  }
  /**
   * Page token to fetch the next page of reports.
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
class_alias(GooglePlayDeveloperReportingV1beta1SearchErrorReportsResponse::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1SearchErrorReportsResponse');
