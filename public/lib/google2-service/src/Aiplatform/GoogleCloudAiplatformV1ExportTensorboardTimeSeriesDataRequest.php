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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataRequest extends \Google\Model
{
  /**
   * Exports the TensorboardTimeSeries' data that match the filter expression.
   *
   * @var string
   */
  public $filter;
  /**
   * Field to use to sort the TensorboardTimeSeries' data. By default,
   * TensorboardTimeSeries' data is returned in a pseudo random order.
   *
   * @var string
   */
  public $orderBy;
  /**
   * The maximum number of data points to return per page. The default page_size
   * is 1000. Values must be between 1 and 10000. Values above 10000 are coerced
   * to 10000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A page token, received from a previous ExportTensorboardTimeSeriesData
   * call. Provide this to retrieve the subsequent page. When paginating, all
   * other parameters provided to ExportTensorboardTimeSeriesData must match the
   * call that provided the page token.
   *
   * @var string
   */
  public $pageToken;

  /**
   * Exports the TensorboardTimeSeries' data that match the filter expression.
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
   * Field to use to sort the TensorboardTimeSeries' data. By default,
   * TensorboardTimeSeries' data is returned in a pseudo random order.
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
   * The maximum number of data points to return per page. The default page_size
   * is 1000. Values must be between 1 and 10000. Values above 10000 are coerced
   * to 10000.
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
   * A page token, received from a previous ExportTensorboardTimeSeriesData
   * call. Provide this to retrieve the subsequent page. When paginating, all
   * other parameters provided to ExportTensorboardTimeSeriesData must match the
   * call that provided the page token.
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
class_alias(GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataRequest');
