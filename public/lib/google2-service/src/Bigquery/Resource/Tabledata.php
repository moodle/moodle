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

namespace Google\Service\Bigquery\Resource;

use Google\Service\Bigquery\TableDataInsertAllRequest;
use Google\Service\Bigquery\TableDataInsertAllResponse;
use Google\Service\Bigquery\TableDataList;

/**
 * The "tabledata" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigqueryService = new Google\Service\Bigquery(...);
 *   $tabledata = $bigqueryService->tabledata;
 *  </code>
 */
class Tabledata extends \Google\Service\Resource
{
  /**
   * Streams data into BigQuery one record at a time without needing to run a load
   * job. (tabledata.insertAll)
   *
   * @param string $projectId Required. Project ID of the destination.
   * @param string $datasetId Required. Dataset ID of the destination.
   * @param string $tableId Required. Table ID of the destination.
   * @param TableDataInsertAllRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TableDataInsertAllResponse
   * @throws \Google\Service\Exception
   */
  public function insertAll($projectId, $datasetId, $tableId, TableDataInsertAllRequest $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'tableId' => $tableId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insertAll', [$params], TableDataInsertAllResponse::class);
  }
  /**
   * List the content of a table in rows. (tabledata.listTabledata)
   *
   * @param string $projectId Required. Project id of the table to list.
   * @param string $datasetId Required. Dataset id of the table to list.
   * @param string $tableId Required. Table id of the table to list.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string formatOptions.timestampOutputFormat Optional. The API
   * output format for a timestamp. This offers more explicit control over the
   * timestamp output format as compared to the existing `use_int64_timestamp`
   * option.
   * @opt_param bool formatOptions.useInt64Timestamp Optional. Output timestamp as
   * usec int64. Default is false.
   * @opt_param string maxResults Row limit of the table.
   * @opt_param string pageToken To retrieve the next page of table data, set this
   * field to the string provided in the pageToken field of the response body from
   * your previous call to tabledata.list.
   * @opt_param string selectedFields Subset of fields to return, supports select
   * into sub fields. Example: selected_fields = "a,e.d.f";
   * @opt_param string startIndex Start row index of the table.
   * @return TableDataList
   * @throws \Google\Service\Exception
   */
  public function listTabledata($projectId, $datasetId, $tableId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'tableId' => $tableId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], TableDataList::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Tabledata::class, 'Google_Service_Bigquery_Resource_Tabledata');
