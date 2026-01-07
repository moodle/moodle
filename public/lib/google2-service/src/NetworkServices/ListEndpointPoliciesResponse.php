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

namespace Google\Service\NetworkServices;

class ListEndpointPoliciesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $endpointPoliciesType = EndpointPolicy::class;
  protected $endpointPoliciesDataType = 'array';
  /**
   * If there might be more results than those appearing in this response, then
   * `next_page_token` is included. To get the next set of results, call this
   * method again using the value of `next_page_token` as `page_token`.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Unreachable resources. Populated when the request opts into
   * return_partial_success and reading across collections e.g. when attempting
   * to list all resources across all supported locations.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * List of EndpointPolicy resources.
   *
   * @param EndpointPolicy[] $endpointPolicies
   */
  public function setEndpointPolicies($endpointPolicies)
  {
    $this->endpointPolicies = $endpointPolicies;
  }
  /**
   * @return EndpointPolicy[]
   */
  public function getEndpointPolicies()
  {
    return $this->endpointPolicies;
  }
  /**
   * If there might be more results than those appearing in this response, then
   * `next_page_token` is included. To get the next set of results, call this
   * method again using the value of `next_page_token` as `page_token`.
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
   * Unreachable resources. Populated when the request opts into
   * return_partial_success and reading across collections e.g. when attempting
   * to list all resources across all supported locations.
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
class_alias(ListEndpointPoliciesResponse::class, 'Google_Service_NetworkServices_ListEndpointPoliciesResponse');
