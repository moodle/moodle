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

class GoogleChromeManagementV1FetchUsersRequestingExtensionResponse extends \Google\Collection
{
  protected $collection_key = 'userDetails';
  /**
   * Token to specify the next page in the list.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Total number of users in response.
   *
   * @var int
   */
  public $totalSize;
  protected $userDetailsType = GoogleChromeManagementV1UserRequestingExtensionDetails::class;
  protected $userDetailsDataType = 'array';

  /**
   * Token to specify the next page in the list.
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
   * Total number of users in response.
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
  /**
   * Details of users that have requested the queried extension.
   *
   * @param GoogleChromeManagementV1UserRequestingExtensionDetails[] $userDetails
   */
  public function setUserDetails($userDetails)
  {
    $this->userDetails = $userDetails;
  }
  /**
   * @return GoogleChromeManagementV1UserRequestingExtensionDetails[]
   */
  public function getUserDetails()
  {
    return $this->userDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1FetchUsersRequestingExtensionResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1FetchUsersRequestingExtensionResponse');
