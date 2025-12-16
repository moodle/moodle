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

namespace Google\Service\BinaryAuthorization;

class ListPlatformPoliciesResponse extends \Google\Collection
{
  protected $collection_key = 'platformPolicies';
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * ListPlatformPoliciesRequest.page_token field in the subsequent call to the
   * `ListPlatformPolicies` method to retrieve the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $platformPoliciesType = PlatformPolicy::class;
  protected $platformPoliciesDataType = 'array';

  /**
   * A token to retrieve the next page of results. Pass this value in the
   * ListPlatformPoliciesRequest.page_token field in the subsequent call to the
   * `ListPlatformPolicies` method to retrieve the next page of results.
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
   * The list of platform policies.
   *
   * @param PlatformPolicy[] $platformPolicies
   */
  public function setPlatformPolicies($platformPolicies)
  {
    $this->platformPolicies = $platformPolicies;
  }
  /**
   * @return PlatformPolicy[]
   */
  public function getPlatformPolicies()
  {
    return $this->platformPolicies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListPlatformPoliciesResponse::class, 'Google_Service_BinaryAuthorization_ListPlatformPoliciesResponse');
