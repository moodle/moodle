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

namespace Google\Service\Fitness;

class Dataset extends \Google\Collection
{
  protected $collection_key = 'point';
  /**
   * The data stream ID of the data source that created the points in this
   * dataset.
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * The largest end time of all data points in this possibly partial
   * representation of the dataset. Time is in nanoseconds from epoch. This
   * should also match the second part of the dataset identifier.
   *
   * @var string
   */
  public $maxEndTimeNs;
  /**
   * The smallest start time of all data points in this possibly partial
   * representation of the dataset. Time is in nanoseconds from epoch. This
   * should also match the first part of the dataset identifier.
   *
   * @var string
   */
  public $minStartTimeNs;
  /**
   * This token will be set when a dataset is received in response to a GET
   * request and the dataset is too large to be included in a single response.
   * Provide this value in a subsequent GET request to return the next page of
   * data points within this dataset.
   *
   * @var string
   */
  public $nextPageToken;
  protected $pointType = DataPoint::class;
  protected $pointDataType = 'array';

  /**
   * The data stream ID of the data source that created the points in this
   * dataset.
   *
   * @param string $dataSourceId
   */
  public function setDataSourceId($dataSourceId)
  {
    $this->dataSourceId = $dataSourceId;
  }
  /**
   * @return string
   */
  public function getDataSourceId()
  {
    return $this->dataSourceId;
  }
  /**
   * The largest end time of all data points in this possibly partial
   * representation of the dataset. Time is in nanoseconds from epoch. This
   * should also match the second part of the dataset identifier.
   *
   * @param string $maxEndTimeNs
   */
  public function setMaxEndTimeNs($maxEndTimeNs)
  {
    $this->maxEndTimeNs = $maxEndTimeNs;
  }
  /**
   * @return string
   */
  public function getMaxEndTimeNs()
  {
    return $this->maxEndTimeNs;
  }
  /**
   * The smallest start time of all data points in this possibly partial
   * representation of the dataset. Time is in nanoseconds from epoch. This
   * should also match the first part of the dataset identifier.
   *
   * @param string $minStartTimeNs
   */
  public function setMinStartTimeNs($minStartTimeNs)
  {
    $this->minStartTimeNs = $minStartTimeNs;
  }
  /**
   * @return string
   */
  public function getMinStartTimeNs()
  {
    return $this->minStartTimeNs;
  }
  /**
   * This token will be set when a dataset is received in response to a GET
   * request and the dataset is too large to be included in a single response.
   * Provide this value in a subsequent GET request to return the next page of
   * data points within this dataset.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * A partial list of data points contained in the dataset, ordered by
   * endTimeNanos. This list is considered complete when retrieving a small
   * dataset and partial when patching a dataset or retrieving a dataset that is
   * too large to include in a single response.
   *
   * @param DataPoint[] $point
   */
  public function setPoint($point)
  {
    $this->point = $point;
  }
  /**
   * @return DataPoint[]
   */
  public function getPoint()
  {
    return $this->point;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Dataset::class, 'Google_Service_Fitness_Dataset');
