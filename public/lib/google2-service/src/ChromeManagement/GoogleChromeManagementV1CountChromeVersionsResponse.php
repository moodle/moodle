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

class GoogleChromeManagementV1CountChromeVersionsResponse extends \Google\Collection
{
  protected $collection_key = 'browserVersions';
  protected $browserVersionsType = GoogleChromeManagementV1BrowserVersion::class;
  protected $browserVersionsDataType = 'array';
  /**
   * Token to specify the next page of the request.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Total number browser versions matching request.
   *
   * @var int
   */
  public $totalSize;

  /**
   * List of all browser versions and their install counts.
   *
   * @param GoogleChromeManagementV1BrowserVersion[] $browserVersions
   */
  public function setBrowserVersions($browserVersions)
  {
    $this->browserVersions = $browserVersions;
  }
  /**
   * @return GoogleChromeManagementV1BrowserVersion[]
   */
  public function getBrowserVersions()
  {
    return $this->browserVersions;
  }
  /**
   * Token to specify the next page of the request.
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
   * Total number browser versions matching request.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountChromeVersionsResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountChromeVersionsResponse');
