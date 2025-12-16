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

namespace Google\Service\BigtableAdmin;

class ListAuthorizedViewsResponse extends \Google\Collection
{
  protected $collection_key = 'authorizedViews';
  protected $authorizedViewsType = AuthorizedView::class;
  protected $authorizedViewsDataType = 'array';
  /**
   * Set if not all tables could be returned in a single response. Pass this
   * value to `page_token` in another request to get the next page of results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The AuthorizedViews present in the requested table.
   *
   * @param AuthorizedView[] $authorizedViews
   */
  public function setAuthorizedViews($authorizedViews)
  {
    $this->authorizedViews = $authorizedViews;
  }
  /**
   * @return AuthorizedView[]
   */
  public function getAuthorizedViews()
  {
    return $this->authorizedViews;
  }
  /**
   * Set if not all tables could be returned in a single response. Pass this
   * value to `page_token` in another request to get the next page of results.
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
class_alias(ListAuthorizedViewsResponse::class, 'Google_Service_BigtableAdmin_ListAuthorizedViewsResponse');
