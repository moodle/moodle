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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaRankRequest extends \Google\Collection
{
  protected $collection_key = 'records';
  /**
   * @var bool
   */
  public $ignoreRecordDetailsInResponse;
  /**
   * @var string
   */
  public $model;
  /**
   * @var string
   */
  public $query;
  protected $recordsType = GoogleCloudDiscoveryengineV1betaRankingRecord::class;
  protected $recordsDataType = 'array';
  /**
   * @var int
   */
  public $topN;

  /**
   * @param bool
   */
  public function setIgnoreRecordDetailsInResponse($ignoreRecordDetailsInResponse)
  {
    $this->ignoreRecordDetailsInResponse = $ignoreRecordDetailsInResponse;
  }
  /**
   * @return bool
   */
  public function getIgnoreRecordDetailsInResponse()
  {
    return $this->ignoreRecordDetailsInResponse;
  }
  /**
   * @param string
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * @param string
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
  /**
   * @param GoogleCloudDiscoveryengineV1betaRankingRecord[]
   */
  public function setRecords($records)
  {
    $this->records = $records;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaRankingRecord[]
   */
  public function getRecords()
  {
    return $this->records;
  }
  /**
   * @param int
   */
  public function setTopN($topN)
  {
    $this->topN = $topN;
  }
  /**
   * @return int
   */
  public function getTopN()
  {
    return $this->topN;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaRankRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaRankRequest');
