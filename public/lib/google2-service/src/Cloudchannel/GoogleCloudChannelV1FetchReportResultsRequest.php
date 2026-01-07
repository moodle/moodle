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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1FetchReportResultsRequest extends \Google\Collection
{
  protected $collection_key = 'partitionKeys';
  /**
   * Optional. Requested page size of the report. The server may return fewer
   * results than requested. If you don't specify a page size, the server uses a
   * sensible default (may change over time). The maximum value is 30,000; the
   * server will change larger values to 30,000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A token that specifies a page of results beyond the first page.
   * Obtained through FetchReportResultsResponse.next_page_token of the previous
   * CloudChannelReportsService.FetchReportResults call.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Optional. List of keys specifying which report partitions to return. If
   * empty, returns all partitions.
   *
   * @var string[]
   */
  public $partitionKeys;

  /**
   * Optional. Requested page size of the report. The server may return fewer
   * results than requested. If you don't specify a page size, the server uses a
   * sensible default (may change over time). The maximum value is 30,000; the
   * server will change larger values to 30,000.
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
   * Optional. A token that specifies a page of results beyond the first page.
   * Obtained through FetchReportResultsResponse.next_page_token of the previous
   * CloudChannelReportsService.FetchReportResults call.
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
   * Optional. List of keys specifying which report partitions to return. If
   * empty, returns all partitions.
   *
   * @param string[] $partitionKeys
   */
  public function setPartitionKeys($partitionKeys)
  {
    $this->partitionKeys = $partitionKeys;
  }
  /**
   * @return string[]
   */
  public function getPartitionKeys()
  {
    return $this->partitionKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1FetchReportResultsRequest::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1FetchReportResultsRequest');
