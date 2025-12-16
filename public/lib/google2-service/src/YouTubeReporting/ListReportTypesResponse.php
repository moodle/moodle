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

namespace Google\Service\YouTubeReporting;

class ListReportTypesResponse extends \Google\Collection
{
  protected $collection_key = 'reportTypes';
  /**
   * A token to retrieve next page of results. Pass this value in the
   * ListReportTypesRequest.page_token field in the subsequent call to
   * `ListReportTypes` method to retrieve the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $reportTypesType = ReportType::class;
  protected $reportTypesDataType = 'array';

  /**
   * A token to retrieve next page of results. Pass this value in the
   * ListReportTypesRequest.page_token field in the subsequent call to
   * `ListReportTypes` method to retrieve the next page of results.
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
  /**
   * The list of report types.
   *
   * @param ReportType[] $reportTypes
   */
  public function setReportTypes($reportTypes)
  {
    $this->reportTypes = $reportTypes;
  }
  /**
   * @return ReportType[]
   */
  public function getReportTypes()
  {
    return $this->reportTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListReportTypesResponse::class, 'Google_Service_YouTubeReporting_ListReportTypesResponse');
