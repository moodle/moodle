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

class GoogleCloudDiscoveryengineV1alphaListSessionsRequest extends \Google\Model
{
  /**
   * A comma-separated list of fields to filter by, in EBNF grammar. The
   * supported fields are: * `user_pseudo_id` * `state` * `display_name` *
   * `starred` * `is_pinned` * `labels` * `create_time` * `update_time`
   * Examples: * `user_pseudo_id = some_id` * `display_name = "some_name"` *
   * `starred = true` * `is_pinned=true AND (NOT labels:hidden)` * `create_time
   * > "1970-01-01T12:00:00Z"`
   *
   * @var string
   */
  public $filter;
  /**
   * A comma-separated list of fields to order by, sorted in ascending order.
   * Use "desc" after a field name for descending. Supported fields: *
   * `update_time` * `create_time` * `session_name` * `is_pinned` Example: *
   * `update_time desc` * `create_time` * `is_pinned desc,update_time desc`:
   * list sessions by is_pinned first, then by update_time.
   *
   * @var string
   */
  public $orderBy;
  /**
   * Maximum number of results to return. If unspecified, defaults to 50. Max
   * allowed value is 1000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A page token, received from a previous `ListSessions` call. Provide this to
   * retrieve the subsequent page.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. The data store resource name. Format: `projects/{project}/locatio
   * ns/{location}/collections/{collection}/dataStores/{data_store_id}`
   *
   * @var string
   */
  public $parent;

  /**
   * A comma-separated list of fields to filter by, in EBNF grammar. The
   * supported fields are: * `user_pseudo_id` * `state` * `display_name` *
   * `starred` * `is_pinned` * `labels` * `create_time` * `update_time`
   * Examples: * `user_pseudo_id = some_id` * `display_name = "some_name"` *
   * `starred = true` * `is_pinned=true AND (NOT labels:hidden)` * `create_time
   * > "1970-01-01T12:00:00Z"`
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * A comma-separated list of fields to order by, sorted in ascending order.
   * Use "desc" after a field name for descending. Supported fields: *
   * `update_time` * `create_time` * `session_name` * `is_pinned` Example: *
   * `update_time desc` * `create_time` * `is_pinned desc,update_time desc`:
   * list sessions by is_pinned first, then by update_time.
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
   * Maximum number of results to return. If unspecified, defaults to 50. Max
   * allowed value is 1000.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * A page token, received from a previous `ListSessions` call. Provide this to
   * retrieve the subsequent page.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Required. The data store resource name. Format: `projects/{project}/locatio
   * ns/{location}/collections/{collection}/dataStores/{data_store_id}`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaListSessionsRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaListSessionsRequest');
