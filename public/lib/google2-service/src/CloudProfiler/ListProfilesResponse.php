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

namespace Google\Service\CloudProfiler;

class ListProfilesResponse extends \Google\Collection
{
  protected $collection_key = 'profiles';
  /**
   * Token to receive the next page of results. This field maybe empty if there
   * are no more profiles to fetch.
   *
   * @var string
   */
  public $nextPageToken;
  protected $profilesType = Profile::class;
  protected $profilesDataType = 'array';
  /**
   * Number of profiles that were skipped in the current page since they were
   * not able to be fetched successfully. This should typically be zero. A non-
   * zero value may indicate a transient failure, in which case if the number is
   * too high for your use case, the call may be retried.
   *
   * @var int
   */
  public $skippedProfiles;

  /**
   * Token to receive the next page of results. This field maybe empty if there
   * are no more profiles to fetch.
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
   * List of profiles fetched.
   *
   * @param Profile[] $profiles
   */
  public function setProfiles($profiles)
  {
    $this->profiles = $profiles;
  }
  /**
   * @return Profile[]
   */
  public function getProfiles()
  {
    return $this->profiles;
  }
  /**
   * Number of profiles that were skipped in the current page since they were
   * not able to be fetched successfully. This should typically be zero. A non-
   * zero value may indicate a transient failure, in which case if the number is
   * too high for your use case, the call may be retried.
   *
   * @param int $skippedProfiles
   */
  public function setSkippedProfiles($skippedProfiles)
  {
    $this->skippedProfiles = $skippedProfiles;
  }
  /**
   * @return int
   */
  public function getSkippedProfiles()
  {
    return $this->skippedProfiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListProfilesResponse::class, 'Google_Service_CloudProfiler_ListProfilesResponse');
