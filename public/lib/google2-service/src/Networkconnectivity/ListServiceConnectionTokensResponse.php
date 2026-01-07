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

namespace Google\Service\Networkconnectivity;

class ListServiceConnectionTokensResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * The next pagination token in the List response. It should be used as
   * page_token for the following request. An empty value means no more result.
   *
   * @var string
   */
  public $nextPageToken;
  protected $serviceConnectionTokensType = ServiceConnectionToken::class;
  protected $serviceConnectionTokensDataType = 'array';
  /**
   * Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The next pagination token in the List response. It should be used as
   * page_token for the following request. An empty value means no more result.
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
   * ServiceConnectionTokens to be returned.
   *
   * @param ServiceConnectionToken[] $serviceConnectionTokens
   */
  public function setServiceConnectionTokens($serviceConnectionTokens)
  {
    $this->serviceConnectionTokens = $serviceConnectionTokens;
  }
  /**
   * @return ServiceConnectionToken[]
   */
  public function getServiceConnectionTokens()
  {
    return $this->serviceConnectionTokens;
  }
  /**
   * Locations that could not be reached.
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
class_alias(ListServiceConnectionTokensResponse::class, 'Google_Service_Networkconnectivity_ListServiceConnectionTokensResponse');
