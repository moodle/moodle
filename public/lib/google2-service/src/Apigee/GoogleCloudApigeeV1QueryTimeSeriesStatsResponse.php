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

class GoogleCloudApigeeV1QueryTimeSeriesStatsResponse extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * Column names corresponding to the same order as the inner values in the
   * stats field.
   *
   * @var string[]
   */
  public $columns;
  /**
   * Next page token.
   *
   * @var string
   */
  public $nextPageToken;
  protected $valuesType = GoogleCloudApigeeV1QueryTimeSeriesStatsResponseSequence::class;
  protected $valuesDataType = 'array';

  /**
   * Column names corresponding to the same order as the inner values in the
   * stats field.
   *
   * @param string[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return string[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Next page token.
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
   * Results of the query returned as a JSON array.
   *
   * @param GoogleCloudApigeeV1QueryTimeSeriesStatsResponseSequence[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudApigeeV1QueryTimeSeriesStatsResponseSequence[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1QueryTimeSeriesStatsResponse::class, 'Google_Service_Apigee_GoogleCloudApigeeV1QueryTimeSeriesStatsResponse');
