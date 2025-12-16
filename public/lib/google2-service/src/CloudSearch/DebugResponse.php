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

namespace Google\Service\CloudSearch;

class DebugResponse extends \Google\Model
{
  /**
   * Serialized string of GenericSearchRequest.
   *
   * @var string
   */
  public $gsrRequest;
  /**
   * Serialized string of GenericSearchResponse.
   *
   * @var string
   */
  public $gsrResponse;
  protected $searchResponseType = SearchResponse::class;
  protected $searchResponseDataType = '';

  /**
   * Serialized string of GenericSearchRequest.
   *
   * @param string $gsrRequest
   */
  public function setGsrRequest($gsrRequest)
  {
    $this->gsrRequest = $gsrRequest;
  }
  /**
   * @return string
   */
  public function getGsrRequest()
  {
    return $this->gsrRequest;
  }
  /**
   * Serialized string of GenericSearchResponse.
   *
   * @param string $gsrResponse
   */
  public function setGsrResponse($gsrResponse)
  {
    $this->gsrResponse = $gsrResponse;
  }
  /**
   * @return string
   */
  public function getGsrResponse()
  {
    return $this->gsrResponse;
  }
  /**
   * Search response.
   *
   * @param SearchResponse $searchResponse
   */
  public function setSearchResponse(SearchResponse $searchResponse)
  {
    $this->searchResponse = $searchResponse;
  }
  /**
   * @return SearchResponse
   */
  public function getSearchResponse()
  {
    return $this->searchResponse;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DebugResponse::class, 'Google_Service_CloudSearch_DebugResponse');
