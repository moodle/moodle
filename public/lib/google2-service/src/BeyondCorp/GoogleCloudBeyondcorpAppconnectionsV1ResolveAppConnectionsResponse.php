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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpAppconnectionsV1ResolveAppConnectionsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $appConnectionDetailsType = GoogleCloudBeyondcorpAppconnectionsV1ResolveAppConnectionsResponseAppConnectionDetails::class;
  protected $appConnectionDetailsDataType = 'array';
  /**
   * A token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * A list of locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * A list of BeyondCorp AppConnections with details in the project.
   *
   * @param GoogleCloudBeyondcorpAppconnectionsV1ResolveAppConnectionsResponseAppConnectionDetails[] $appConnectionDetails
   */
  public function setAppConnectionDetails($appConnectionDetails)
  {
    $this->appConnectionDetails = $appConnectionDetails;
  }
  /**
   * @return GoogleCloudBeyondcorpAppconnectionsV1ResolveAppConnectionsResponseAppConnectionDetails[]
   */
  public function getAppConnectionDetails()
  {
    return $this->appConnectionDetails;
  }
  /**
   * A token to retrieve the next page of results, or empty if there are no more
   * results in the list.
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
   * A list of locations that could not be reached.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpAppconnectionsV1ResolveAppConnectionsResponse::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpAppconnectionsV1ResolveAppConnectionsResponse');
