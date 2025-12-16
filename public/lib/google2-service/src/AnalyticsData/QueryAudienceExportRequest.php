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

namespace Google\Service\AnalyticsData;

class QueryAudienceExportRequest extends \Google\Model
{
  /**
   * Optional. The number of rows to return. If unspecified, 10,000 rows are
   * returned. The API returns a maximum of 250,000 rows per request, no matter
   * how many you ask for. `limit` must be positive. The API can also return
   * fewer rows than the requested `limit`, if there aren't as many dimension
   * values as the `limit`. To learn more about this pagination parameter, see [
   * Pagination](https://developers.google.com/analytics/devguides/reporting/dat
   * a/v1/basics#pagination).
   *
   * @var string
   */
  public $limit;
  /**
   * Optional. The row count of the start row. The first row is counted as row
   * 0. When paging, the first request does not specify offset; or equivalently,
   * sets offset to 0; the first request returns the first `limit` of rows. The
   * second request sets offset to the `limit` of the first request; the second
   * request returns the second `limit` of rows. To learn more about this
   * pagination parameter, see [Pagination](https://developers.google.com/analyt
   * ics/devguides/reporting/data/v1/basics#pagination).
   *
   * @var string
   */
  public $offset;

  /**
   * Optional. The number of rows to return. If unspecified, 10,000 rows are
   * returned. The API returns a maximum of 250,000 rows per request, no matter
   * how many you ask for. `limit` must be positive. The API can also return
   * fewer rows than the requested `limit`, if there aren't as many dimension
   * values as the `limit`. To learn more about this pagination parameter, see [
   * Pagination](https://developers.google.com/analytics/devguides/reporting/dat
   * a/v1/basics#pagination).
   *
   * @param string $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return string
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Optional. The row count of the start row. The first row is counted as row
   * 0. When paging, the first request does not specify offset; or equivalently,
   * sets offset to 0; the first request returns the first `limit` of rows. The
   * second request sets offset to the `limit` of the first request; the second
   * request returns the second `limit` of rows. To learn more about this
   * pagination parameter, see [Pagination](https://developers.google.com/analyt
   * ics/devguides/reporting/data/v1/basics#pagination).
   *
   * @param string $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return string
   */
  public function getOffset()
  {
    return $this->offset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryAudienceExportRequest::class, 'Google_Service_AnalyticsData_QueryAudienceExportRequest');
