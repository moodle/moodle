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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SearchInfo extends \Google\Model
{
  /**
   * An integer that specifies the current offset for pagination (the 0-indexed
   * starting location, amongst the products deemed by the API as relevant). See
   * SearchRequest.offset for definition. If this field is negative, an
   * `INVALID_ARGUMENT` is returned. This can only be set for `search` events.
   * Other event types should not set this field. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @var int
   */
  public $offset;
  /**
   * The order in which products are returned, if applicable. See
   * SearchRequest.order_by for definition and syntax. The value must be a UTF-8
   * encoded string with a length limit of 1,000 characters. Otherwise, an
   * `INVALID_ARGUMENT` error is returned. This can only be set for `search`
   * events. Other event types should not set this field. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @var string
   */
  public $orderBy;
  /**
   * The user's search query. See SearchRequest.query for definition. The value
   * must be a UTF-8 encoded string with a length limit of 5,000 characters.
   * Otherwise, an `INVALID_ARGUMENT` error is returned. At least one of
   * search_query or PageInfo.page_category is required for `search` events.
   * Other event types should not set this field. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @var string
   */
  public $searchQuery;

  /**
   * An integer that specifies the current offset for pagination (the 0-indexed
   * starting location, amongst the products deemed by the API as relevant). See
   * SearchRequest.offset for definition. If this field is negative, an
   * `INVALID_ARGUMENT` is returned. This can only be set for `search` events.
   * Other event types should not set this field. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @param int $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * The order in which products are returned, if applicable. See
   * SearchRequest.order_by for definition and syntax. The value must be a UTF-8
   * encoded string with a length limit of 1,000 characters. Otherwise, an
   * `INVALID_ARGUMENT` error is returned. This can only be set for `search`
   * events. Other event types should not set this field. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @param string $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * The user's search query. See SearchRequest.query for definition. The value
   * must be a UTF-8 encoded string with a length limit of 5,000 characters.
   * Otherwise, an `INVALID_ARGUMENT` error is returned. At least one of
   * search_query or PageInfo.page_category is required for `search` events.
   * Other event types should not set this field. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @param string $searchQuery
   */
  public function setSearchQuery($searchQuery)
  {
    $this->searchQuery = $searchQuery;
  }
  /**
   * @return string
   */
  public function getSearchQuery()
  {
    return $this->searchQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchInfo');
