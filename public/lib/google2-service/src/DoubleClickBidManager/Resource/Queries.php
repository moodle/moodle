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

namespace Google\Service\DoubleClickBidManager\Resource;

use Google\Service\DoubleClickBidManager\ListQueriesResponse;
use Google\Service\DoubleClickBidManager\Query;
use Google\Service\DoubleClickBidManager\Report;
use Google\Service\DoubleClickBidManager\RunQueryRequest;

/**
 * The "queries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $doubleclickbidmanagerService = new Google\Service\DoubleClickBidManager(...);
 *   $queries = $doubleclickbidmanagerService->queries;
 *  </code>
 */
class Queries extends \Google\Service\Resource
{
  /**
   * Creates a new query. (queries.create)
   *
   * @param Query $postBody
   * @param array $optParams Optional parameters.
   * @return Query
   * @throws \Google\Service\Exception
   */
  public function create(Query $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Query::class);
  }
  /**
   * Deletes an existing query as well as its generated reports. (queries.delete)
   *
   * @param string $queryId Required. The ID of the query to delete.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($queryId, $optParams = [])
  {
    $params = ['queryId' => $queryId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Retrieves a query. (queries.get)
   *
   * @param string $queryId Required. The ID of the query to retrieve.
   * @param array $optParams Optional parameters.
   * @return Query
   * @throws \Google\Service\Exception
   */
  public function get($queryId, $optParams = [])
  {
    $params = ['queryId' => $queryId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Query::class);
  }
  /**
   * Lists queries created by the current user. (queries.listQueries)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Field to sort the list by. Accepts the following
   * values: * `queryId` (default) * `metadata.title` The default sorting order is
   * ascending. To specify descending order for a field, add the suffix `desc` to
   * the field name. For example, `queryId desc`.
   * @opt_param int pageSize Maximum number of results per page. Must be between
   * `1` and `100`. Defaults to `100` if unspecified.
   * @opt_param string pageToken A token identifying which page of results the
   * server should return. Typically, this is the value of nextPageToken, returned
   * from the previous call to the `queries.list` method. If unspecified, the
   * first page of results is returned.
   * @return ListQueriesResponse
   * @throws \Google\Service\Exception
   */
  public function listQueries($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListQueriesResponse::class);
  }
  /**
   * Runs an existing query to generate a report. (queries.run)
   *
   * @param string $queryId Required. The ID of the query to run.
   * @param RunQueryRequest $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool synchronous Whether the query should be run synchronously.
   * When `true`, the request won't return until the resulting report has finished
   * running. This parameter is `false` by default. Setting this parameter to
   * `true` is **not recommended**.
   * @return Report
   * @throws \Google\Service\Exception
   */
  public function run($queryId, RunQueryRequest $postBody, $optParams = [])
  {
    $params = ['queryId' => $queryId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('run', [$params], Report::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Queries::class, 'Google_Service_DoubleClickBidManager_Resource_Queries');
