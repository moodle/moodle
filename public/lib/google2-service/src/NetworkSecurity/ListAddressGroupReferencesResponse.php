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

namespace Google\Service\NetworkSecurity;

class ListAddressGroupReferencesResponse extends \Google\Collection
{
  protected $collection_key = 'addressGroupReferences';
  protected $addressGroupReferencesType = ListAddressGroupReferencesResponseAddressGroupReference::class;
  protected $addressGroupReferencesDataType = 'array';
  /**
   * If there might be more results than those appearing in this response, then
   * `next_page_token` is included. To get the next set of results, call this
   * method again using the value of `next_page_token` as `page_token`.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * A list of references that matches the specified filter in the request.
   *
   * @param ListAddressGroupReferencesResponseAddressGroupReference[] $addressGroupReferences
   */
  public function setAddressGroupReferences($addressGroupReferences)
  {
    $this->addressGroupReferences = $addressGroupReferences;
  }
  /**
   * @return ListAddressGroupReferencesResponseAddressGroupReference[]
   */
  public function getAddressGroupReferences()
  {
    return $this->addressGroupReferences;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAddressGroupReferencesResponse::class, 'Google_Service_NetworkSecurity_ListAddressGroupReferencesResponse');
