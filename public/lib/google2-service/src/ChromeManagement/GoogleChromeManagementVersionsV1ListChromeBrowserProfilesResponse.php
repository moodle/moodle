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

class GoogleChromeManagementVersionsV1ListChromeBrowserProfilesResponse extends \Google\Collection
{
  protected $collection_key = 'chromeBrowserProfiles';
  protected $chromeBrowserProfilesType = GoogleChromeManagementVersionsV1ChromeBrowserProfile::class;
  protected $chromeBrowserProfilesDataType = 'array';
  /**
   * The pagination token that can be used to list the next page.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Total size represents an estimated number of resources returned. Not
   * guaranteed to be accurate above 10k profiles.
   *
   * @var string
   */
  public $totalSize;

  /**
   * The list of profiles returned.
   *
   * @param GoogleChromeManagementVersionsV1ChromeBrowserProfile[] $chromeBrowserProfiles
   */
  public function setChromeBrowserProfiles($chromeBrowserProfiles)
  {
    $this->chromeBrowserProfiles = $chromeBrowserProfiles;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ChromeBrowserProfile[]
   */
  public function getChromeBrowserProfiles()
  {
    return $this->chromeBrowserProfiles;
  }
  /**
   * The pagination token that can be used to list the next page.
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
   * Total size represents an estimated number of resources returned. Not
   * guaranteed to be accurate above 10k profiles.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ListChromeBrowserProfilesResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ListChromeBrowserProfilesResponse');
