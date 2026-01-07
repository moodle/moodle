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

namespace Google\Service\Logging;

class ListLogEntriesRequest extends \Google\Collection
{
  protected $collection_key = 'resourceNames';
  /**
   * Optional. A filter that chooses which log entries to return. For more
   * information, see Logging query language
   * (https://cloud.google.com/logging/docs/view/logging-query-language).Only
   * log entries that match the filter are returned. An empty filter matches all
   * log entries in the resources listed in resource_names. Referencing a parent
   * resource that is not listed in resource_names will cause the filter to
   * return no results. The maximum length of a filter is 20,000 characters.To
   * make queries faster, you can make the filter more selective by using
   * restrictions on indexed fields
   * (https://cloud.google.com/logging/docs/view/logging-query-language#indexed-
   * fields) as well as limit the time range of the query by adding range
   * restrictions on the timestamp field.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. How the results should be sorted. Presently, the only permitted
   * values are "timestamp asc" (default) and "timestamp desc". The first option
   * returns entries in order of increasing values of LogEntry.timestamp (oldest
   * first), and the second option returns entries in order of decreasing
   * timestamps (newest first). Entries with equal timestamps are returned in
   * order of their insert_id values.We recommend setting the order_by field to
   * "timestamp desc" when listing recently ingested log entries. If not set,
   * the default value of "timestamp asc" may take a long time to fetch matching
   * logs that are only recently ingested.
   *
   * @var string
   */
  public $orderBy;
  /**
   * Optional. The maximum number of results to return from this request.
   * Default is 50. If the value is negative, the request is rejected.The
   * presence of next_page_token in the response indicates that more results
   * might be available.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. If present, then retrieve the next batch of results from the
   * preceding call to this method. page_token must be the value of
   * next_page_token from the previous response. The values of other method
   * parameters should be identical to those in the previous call.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Optional. Deprecated. Use resource_names instead. One or more project
   * identifiers or project numbers from which to retrieve log entries. Example:
   * "my-project-1A".
   *
   * @deprecated
   * @var string[]
   */
  public $projectIds;
  /**
   * Required. Names of one or more parent resources from which to retrieve log
   * entries. Resources may either be resource containers or specific LogViews.
   * For the case of resource containers, all logs ingested into that container
   * will be returned regardless of which LogBuckets they are actually stored in
   * - i.e. these queries may fan out to multiple regions. In the event of
   * region unavailability, specify a specific set of LogViews that do not
   * include the unavailable region. projects/[PROJECT_ID]
   * organizations/[ORGANIZATION_ID] billingAccounts/[BILLING_ACCOUNT_ID]
   * folders/[FOLDER_ID] projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[
   * BUCKET_ID]/views/[VIEW_ID] organizations/[ORGANIZATION_ID]/locations/[LOCAT
   * ION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID] billingAccounts/[BILLING_ACCOUN
   * T_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID] folders/[
   * FOLDER_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID]Proje
   * cts listed in the project_ids field are added to this list. A maximum of
   * 100 resources may be specified in a single request.
   *
   * @var string[]
   */
  public $resourceNames;

  /**
   * Optional. A filter that chooses which log entries to return. For more
   * information, see Logging query language
   * (https://cloud.google.com/logging/docs/view/logging-query-language).Only
   * log entries that match the filter are returned. An empty filter matches all
   * log entries in the resources listed in resource_names. Referencing a parent
   * resource that is not listed in resource_names will cause the filter to
   * return no results. The maximum length of a filter is 20,000 characters.To
   * make queries faster, you can make the filter more selective by using
   * restrictions on indexed fields
   * (https://cloud.google.com/logging/docs/view/logging-query-language#indexed-
   * fields) as well as limit the time range of the query by adding range
   * restrictions on the timestamp field.
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
   * Optional. How the results should be sorted. Presently, the only permitted
   * values are "timestamp asc" (default) and "timestamp desc". The first option
   * returns entries in order of increasing values of LogEntry.timestamp (oldest
   * first), and the second option returns entries in order of decreasing
   * timestamps (newest first). Entries with equal timestamps are returned in
   * order of their insert_id values.We recommend setting the order_by field to
   * "timestamp desc" when listing recently ingested log entries. If not set,
   * the default value of "timestamp asc" may take a long time to fetch matching
   * logs that are only recently ingested.
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
   * Optional. The maximum number of results to return from this request.
   * Default is 50. If the value is negative, the request is rejected.The
   * presence of next_page_token in the response indicates that more results
   * might be available.
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
   * Optional. If present, then retrieve the next batch of results from the
   * preceding call to this method. page_token must be the value of
   * next_page_token from the previous response. The values of other method
   * parameters should be identical to those in the previous call.
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
   * Optional. Deprecated. Use resource_names instead. One or more project
   * identifiers or project numbers from which to retrieve log entries. Example:
   * "my-project-1A".
   *
   * @deprecated
   * @param string[] $projectIds
   */
  public function setProjectIds($projectIds)
  {
    $this->projectIds = $projectIds;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getProjectIds()
  {
    return $this->projectIds;
  }
  /**
   * Required. Names of one or more parent resources from which to retrieve log
   * entries. Resources may either be resource containers or specific LogViews.
   * For the case of resource containers, all logs ingested into that container
   * will be returned regardless of which LogBuckets they are actually stored in
   * - i.e. these queries may fan out to multiple regions. In the event of
   * region unavailability, specify a specific set of LogViews that do not
   * include the unavailable region. projects/[PROJECT_ID]
   * organizations/[ORGANIZATION_ID] billingAccounts/[BILLING_ACCOUNT_ID]
   * folders/[FOLDER_ID] projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[
   * BUCKET_ID]/views/[VIEW_ID] organizations/[ORGANIZATION_ID]/locations/[LOCAT
   * ION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID] billingAccounts/[BILLING_ACCOUN
   * T_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID] folders/[
   * FOLDER_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/views/[VIEW_ID]Proje
   * cts listed in the project_ids field are added to this list. A maximum of
   * 100 resources may be specified in a single request.
   *
   * @param string[] $resourceNames
   */
  public function setResourceNames($resourceNames)
  {
    $this->resourceNames = $resourceNames;
  }
  /**
   * @return string[]
   */
  public function getResourceNames()
  {
    return $this->resourceNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListLogEntriesRequest::class, 'Google_Service_Logging_ListLogEntriesRequest');
