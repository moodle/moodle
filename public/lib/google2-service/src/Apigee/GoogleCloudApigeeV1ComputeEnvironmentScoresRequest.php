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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ComputeEnvironmentScoresRequest extends \Google\Collection
{
  protected $collection_key = 'filters';
  protected $filtersType = GoogleCloudApigeeV1ComputeEnvironmentScoresRequestFilter::class;
  protected $filtersDataType = 'array';
  /**
   * Optional. The maximum number of subcomponents to be returned in a single
   * page. The service may return fewer than this value. If unspecified, at most
   * 100 subcomponents will be returned in a single page.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A token that can be sent as `page_token` to retrieve the next
   * page. If this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $pageToken;
  protected $timeRangeType = GoogleTypeInterval::class;
  protected $timeRangeDataType = '';

  /**
   * Optional. Filters are used to filter scored components. Return all the
   * components if no filter is mentioned. Example: [{ "scorePath":
   * "/org@myorg/envgroup@myenvgroup/env@myenv/proxies/proxy@myproxy/source" },
   * { "scorePath":
   * "/org@myorg/envgroup@myenvgroup/env@myenv/proxies/proxy@myproxy/target", }]
   * This will return components with path:
   * "/org@myorg/envgroup@myenvgroup/env@myenv/proxies/proxy@myproxy/source" OR
   * "/org@myorg/envgroup@myenvgroup/env@myenv/proxies/proxy@myproxy/target"
   *
   * @param GoogleCloudApigeeV1ComputeEnvironmentScoresRequestFilter[] $filters
   */
  public function setFilters($filters)
  {
    $this->filters = $filters;
  }
  /**
   * @return GoogleCloudApigeeV1ComputeEnvironmentScoresRequestFilter[]
   */
  public function getFilters()
  {
    return $this->filters;
  }
  /**
   * Optional. The maximum number of subcomponents to be returned in a single
   * page. The service may return fewer than this value. If unspecified, at most
   * 100 subcomponents will be returned in a single page.
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
   * Optional. A token that can be sent as `page_token` to retrieve the next
   * page. If this field is omitted, there are no subsequent pages.
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
   * Required. Time range for score calculation. At most 14 days of scores will
   * be returned, and both the start and end dates must be within the last 90
   * days.
   *
   * @param GoogleTypeInterval $timeRange
   */
  public function setTimeRange(GoogleTypeInterval $timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return GoogleTypeInterval
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ComputeEnvironmentScoresRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ComputeEnvironmentScoresRequest');
