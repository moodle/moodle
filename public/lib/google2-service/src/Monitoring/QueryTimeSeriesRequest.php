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

namespace Google\Service\Monitoring;

class QueryTimeSeriesRequest extends \Google\Model
{
  /**
   * A positive number that is the maximum number of time_series_data to return.
   *
   * @var int
   */
  public $pageSize;
  /**
   * If this field is not empty then it must contain the nextPageToken value
   * returned by a previous call to this method. Using this field causes the
   * method to return additional results from the previous method call.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. The query in the Monitoring Query Language
   * (https://cloud.google.com/monitoring/mql/reference) format. The default
   * time zone is in UTC.
   *
   * @var string
   */
  public $query;

  /**
   * A positive number that is the maximum number of time_series_data to return.
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
   * If this field is not empty then it must contain the nextPageToken value
   * returned by a previous call to this method. Using this field causes the
   * method to return additional results from the previous method call.
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
   * Required. The query in the Monitoring Query Language
   * (https://cloud.google.com/monitoring/mql/reference) format. The default
   * time zone is in UTC.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryTimeSeriesRequest::class, 'Google_Service_Monitoring_QueryTimeSeriesRequest');
