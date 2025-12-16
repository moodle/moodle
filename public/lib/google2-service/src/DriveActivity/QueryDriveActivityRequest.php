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

namespace Google\Service\DriveActivity;

class QueryDriveActivityRequest extends \Google\Model
{
  /**
   * Return activities for this Drive folder, plus all children and descendants.
   * The format is `items/ITEM_ID`.
   *
   * @var string
   */
  public $ancestorName;
  protected $consolidationStrategyType = ConsolidationStrategy::class;
  protected $consolidationStrategyDataType = '';
  /**
   * The filtering for items returned from this query request. The format of the
   * filter string is a sequence of expressions, joined by an optional "AND",
   * where each expression is of the form "field operator value". Supported
   * fields: - `time`: Uses numerical operators on date values either in terms
   * of milliseconds since Jan 1, 1970 or in RFC 3339 format. Examples: - `time
   * > 1452409200000 AND time <= 1492812924310` - `time >=
   * "2016-01-10T01:02:03-05:00"` - `detail.action_detail_case`: Uses the "has"
   * operator (:) and either a singular value or a list of allowed action types
   * enclosed in parentheses, separated by a space. To exclude a result from the
   * response, prepend a hyphen (`-`) to the beginning of the filter string.
   * Examples: - `detail.action_detail_case:RENAME` -
   * `detail.action_detail_case:(CREATE RESTORE)` -
   * `-detail.action_detail_case:MOVE`
   *
   * @var string
   */
  public $filter;
  /**
   * Return activities for this Drive item. The format is `items/ITEM_ID`.
   *
   * @var string
   */
  public $itemName;
  /**
   * The minimum number of activities desired in the response; the server
   * attempts to return at least this quantity. The server may also return fewer
   * activities if it has a partial response ready before the request times out.
   * If not set, a default value is used.
   *
   * @var int
   */
  public $pageSize;
  /**
   * The token identifies which page of results to return. Set this to the
   * next_page_token value returned from a previous query to obtain the
   * following page of results. If not set, the first page of results is
   * returned.
   *
   * @var string
   */
  public $pageToken;

  /**
   * Return activities for this Drive folder, plus all children and descendants.
   * The format is `items/ITEM_ID`.
   *
   * @param string $ancestorName
   */
  public function setAncestorName($ancestorName)
  {
    $this->ancestorName = $ancestorName;
  }
  /**
   * @return string
   */
  public function getAncestorName()
  {
    return $this->ancestorName;
  }
  /**
   * Details on how to consolidate related actions that make up the activity. If
   * not set, then related actions aren't consolidated.
   *
   * @param ConsolidationStrategy $consolidationStrategy
   */
  public function setConsolidationStrategy(ConsolidationStrategy $consolidationStrategy)
  {
    $this->consolidationStrategy = $consolidationStrategy;
  }
  /**
   * @return ConsolidationStrategy
   */
  public function getConsolidationStrategy()
  {
    return $this->consolidationStrategy;
  }
  /**
   * The filtering for items returned from this query request. The format of the
   * filter string is a sequence of expressions, joined by an optional "AND",
   * where each expression is of the form "field operator value". Supported
   * fields: - `time`: Uses numerical operators on date values either in terms
   * of milliseconds since Jan 1, 1970 or in RFC 3339 format. Examples: - `time
   * > 1452409200000 AND time <= 1492812924310` - `time >=
   * "2016-01-10T01:02:03-05:00"` - `detail.action_detail_case`: Uses the "has"
   * operator (:) and either a singular value or a list of allowed action types
   * enclosed in parentheses, separated by a space. To exclude a result from the
   * response, prepend a hyphen (`-`) to the beginning of the filter string.
   * Examples: - `detail.action_detail_case:RENAME` -
   * `detail.action_detail_case:(CREATE RESTORE)` -
   * `-detail.action_detail_case:MOVE`
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
   * Return activities for this Drive item. The format is `items/ITEM_ID`.
   *
   * @param string $itemName
   */
  public function setItemName($itemName)
  {
    $this->itemName = $itemName;
  }
  /**
   * @return string
   */
  public function getItemName()
  {
    return $this->itemName;
  }
  /**
   * The minimum number of activities desired in the response; the server
   * attempts to return at least this quantity. The server may also return fewer
   * activities if it has a partial response ready before the request times out.
   * If not set, a default value is used.
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
   * The token identifies which page of results to return. Set this to the
   * next_page_token value returned from a previous query to obtain the
   * following page of results. If not set, the first page of results is
   * returned.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryDriveActivityRequest::class, 'Google_Service_DriveActivity_QueryDriveActivityRequest');
