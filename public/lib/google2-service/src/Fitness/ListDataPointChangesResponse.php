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

class ListDataPointChangesResponse extends \Google\Collection
{
  protected $collection_key = 'insertedDataPoint';
  /**
   * The data stream ID of the data source with data point changes.
   *
   * @var string
   */
  public $dataSourceId;
  protected $deletedDataPointType = DataPoint::class;
  protected $deletedDataPointDataType = 'array';
  protected $insertedDataPointType = DataPoint::class;
  protected $insertedDataPointDataType = 'array';
  /**
   * The continuation token, which is used to page through large result sets.
   * Provide this value in a subsequent request to return the next page of
   * results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The data stream ID of the data source with data point changes.
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
   * Deleted data points for the user. Note, for modifications this should be
   * parsed before handling insertions.
   *
   * @param DataPoint[] $deletedDataPoint
   */
  public function setDeletedDataPoint($deletedDataPoint)
  {
    $this->deletedDataPoint = $deletedDataPoint;
  }
  /**
   * @return DataPoint[]
   */
  public function getDeletedDataPoint()
  {
    return $this->deletedDataPoint;
  }
  /**
   * Inserted data points for the user.
   *
   * @param DataPoint[] $insertedDataPoint
   */
  public function setInsertedDataPoint($insertedDataPoint)
  {
    $this->insertedDataPoint = $insertedDataPoint;
  }
  /**
   * @return DataPoint[]
   */
  public function getInsertedDataPoint()
  {
    return $this->insertedDataPoint;
  }
  /**
   * The continuation token, which is used to page through large result sets.
   * Provide this value in a subsequent request to return the next page of
   * results.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListDataPointChangesResponse::class, 'Google_Service_Fitness_ListDataPointChangesResponse');
