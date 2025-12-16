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

class GoogleCloudDiscoveryengineV1RankRequest extends \Google\Collection
{
  protected $collection_key = 'records';
  /**
   * If true, the response will contain only record ID and score. By default, it
   * is false, the response will contain record details.
   *
   * @var bool
   */
  public $ignoreRecordDetailsInResponse;
  /**
   * The identifier of the model to use. It is one of: * `semantic-
   * ranker-512@latest`: Semantic ranking model with maximum input token size
   * 512. It is set to `semantic-ranker-512@latest` by default if unspecified.
   *
   * @var string
   */
  public $model;
  /**
   * The query to use.
   *
   * @var string
   */
  public $query;
  protected $recordsType = GoogleCloudDiscoveryengineV1RankingRecord::class;
  protected $recordsDataType = 'array';
  /**
   * The number of results to return. If this is unset or no bigger than zero,
   * returns all results.
   *
   * @var int
   */
  public $topN;
  /**
   * The user labels applied to a resource must meet the following requirements:
   * * Each resource can have multiple labels, up to a maximum of 64. * Each
   * label must be a key-value pair. * Keys have a minimum length of 1 character
   * and a maximum length of 63 characters and cannot be empty. Values can be
   * empty and have a maximum length of 63 characters. * Keys and values can
   * contain only lowercase letters, numeric characters, underscores, and
   * dashes. All characters must use UTF-8 encoding, and international
   * characters are allowed. * The key portion of a label must be unique.
   * However, you can use the same key with multiple resources. * Keys must
   * start with a lowercase letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @var string[]
   */
  public $userLabels;

  /**
   * If true, the response will contain only record ID and score. By default, it
   * is false, the response will contain record details.
   *
   * @param bool $ignoreRecordDetailsInResponse
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
   * The identifier of the model to use. It is one of: * `semantic-
   * ranker-512@latest`: Semantic ranking model with maximum input token size
   * 512. It is set to `semantic-ranker-512@latest` by default if unspecified.
   *
   * @param string $model
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
   * The query to use.
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
  /**
   * Required. A list of records to rank.
   *
   * @param GoogleCloudDiscoveryengineV1RankingRecord[] $records
   */
  public function setRecords($records)
  {
    $this->records = $records;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1RankingRecord[]
   */
  public function getRecords()
  {
    return $this->records;
  }
  /**
   * The number of results to return. If this is unset or no bigger than zero,
   * returns all results.
   *
   * @param int $topN
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
  /**
   * The user labels applied to a resource must meet the following requirements:
   * * Each resource can have multiple labels, up to a maximum of 64. * Each
   * label must be a key-value pair. * Keys have a minimum length of 1 character
   * and a maximum length of 63 characters and cannot be empty. Values can be
   * empty and have a maximum length of 63 characters. * Keys and values can
   * contain only lowercase letters, numeric characters, underscores, and
   * dashes. All characters must use UTF-8 encoding, and international
   * characters are allowed. * The key portion of a label must be unique.
   * However, you can use the same key with multiple resources. * Keys must
   * start with a lowercase letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1RankRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1RankRequest');
