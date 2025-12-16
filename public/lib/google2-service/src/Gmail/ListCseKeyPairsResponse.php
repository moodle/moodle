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

namespace Google\Service\Gmail;

class ListCseKeyPairsResponse extends \Google\Collection
{
  protected $collection_key = 'cseKeyPairs';
  protected $cseKeyPairsType = CseKeyPair::class;
  protected $cseKeyPairsDataType = 'array';
  /**
   * Pagination token to be passed to a subsequent ListCseKeyPairs call in order
   * to retrieve the next page of key pairs. If this value is not returned, then
   * no further pages remain.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * One page of the list of CSE key pairs installed for the user.
   *
   * @param CseKeyPair[] $cseKeyPairs
   */
  public function setCseKeyPairs($cseKeyPairs)
  {
    $this->cseKeyPairs = $cseKeyPairs;
  }
  /**
   * @return CseKeyPair[]
   */
  public function getCseKeyPairs()
  {
    return $this->cseKeyPairs;
  }
  /**
   * Pagination token to be passed to a subsequent ListCseKeyPairs call in order
   * to retrieve the next page of key pairs. If this value is not returned, then
   * no further pages remain.
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
class_alias(ListCseKeyPairsResponse::class, 'Google_Service_Gmail_ListCseKeyPairsResponse');
