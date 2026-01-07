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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1CountPrintJobsByUserResponse extends \Google\Collection
{
  protected $collection_key = 'userPrintReports';
  /**
   * Pagination token for requesting the next page.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Total number of users matching request.
   *
   * @var string
   */
  public $totalSize;
  protected $userPrintReportsType = GoogleChromeManagementV1UserPrintReport::class;
  protected $userPrintReportsDataType = 'array';

  /**
   * Pagination token for requesting the next page.
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
   * Total number of users matching request.
   *
   * @param string $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return string
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
  /**
   * List of UserPrintReports matching request.
   *
   * @param GoogleChromeManagementV1UserPrintReport[] $userPrintReports
   */
  public function setUserPrintReports($userPrintReports)
  {
    $this->userPrintReports = $userPrintReports;
  }
  /**
   * @return GoogleChromeManagementV1UserPrintReport[]
   */
  public function getUserPrintReports()
  {
    return $this->userPrintReports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountPrintJobsByUserResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountPrintJobsByUserResponse');
