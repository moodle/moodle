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

namespace Google\Service\CloudKMS;

class ListKeyHandlesResponse extends \Google\Collection
{
  protected $collection_key = 'keyHandles';
  protected $keyHandlesType = KeyHandle::class;
  protected $keyHandlesDataType = 'array';
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListKeyHandlesRequest.page_token to retrieve the next page of results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Resulting KeyHandles.
   *
   * @param KeyHandle[] $keyHandles
   */
  public function setKeyHandles($keyHandles)
  {
    $this->keyHandles = $keyHandles;
  }
  /**
   * @return KeyHandle[]
   */
  public function getKeyHandles()
  {
    return $this->keyHandles;
  }
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListKeyHandlesRequest.page_token to retrieve the next page of results.
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
class_alias(ListKeyHandlesResponse::class, 'Google_Service_CloudKMS_ListKeyHandlesResponse');
