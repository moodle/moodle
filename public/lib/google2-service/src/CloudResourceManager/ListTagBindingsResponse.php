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

namespace Google\Service\CloudResourceManager;

class ListTagBindingsResponse extends \Google\Collection
{
  protected $collection_key = 'tagBindings';
  /**
   * Pagination token. If the result set is too large to fit in a single
   * response, this token is returned. It encodes the position of the current
   * result cursor. Feeding this value into a new list request with the
   * `page_token` parameter gives the next page of the results. When
   * `next_page_token` is not filled in, there is no next page and the list
   * returned is the last page in the result set. Pagination tokens have a
   * limited lifetime.
   *
   * @var string
   */
  public $nextPageToken;
  protected $tagBindingsType = TagBinding::class;
  protected $tagBindingsDataType = 'array';

  /**
   * Pagination token. If the result set is too large to fit in a single
   * response, this token is returned. It encodes the position of the current
   * result cursor. Feeding this value into a new list request with the
   * `page_token` parameter gives the next page of the results. When
   * `next_page_token` is not filled in, there is no next page and the list
   * returned is the last page in the result set. Pagination tokens have a
   * limited lifetime.
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
   * A possibly paginated list of TagBindings for the specified resource.
   *
   * @param TagBinding[] $tagBindings
   */
  public function setTagBindings($tagBindings)
  {
    $this->tagBindings = $tagBindings;
  }
  /**
   * @return TagBinding[]
   */
  public function getTagBindings()
  {
    return $this->tagBindings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListTagBindingsResponse::class, 'Google_Service_CloudResourceManager_ListTagBindingsResponse');
