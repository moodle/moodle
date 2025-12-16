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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1BigQueryDateShardedSpec extends \Google\Model
{
  /**
   * Output only. The Data Catalog resource name of the dataset entry the
   * current table belongs to. For example: `projects/{PROJECT_ID}/locations/{LO
   * CATION}/entrygroups/{ENTRY_GROUP_ID}/entries/{ENTRY_ID}`.
   *
   * @var string
   */
  public $dataset;
  /**
   * Output only. BigQuery resource name of the latest shard.
   *
   * @var string
   */
  public $latestShardResource;
  /**
   * Output only. Total number of shards.
   *
   * @var string
   */
  public $shardCount;
  /**
   * Output only. The table name prefix of the shards. The name of any given
   * shard is `[table_prefix]YYYYMMDD`. For example, for the `MyTable20180101`
   * shard, the `table_prefix` is `MyTable`.
   *
   * @var string
   */
  public $tablePrefix;

  /**
   * Output only. The Data Catalog resource name of the dataset entry the
   * current table belongs to. For example: `projects/{PROJECT_ID}/locations/{LO
   * CATION}/entrygroups/{ENTRY_GROUP_ID}/entries/{ENTRY_ID}`.
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Output only. BigQuery resource name of the latest shard.
   *
   * @param string $latestShardResource
   */
  public function setLatestShardResource($latestShardResource)
  {
    $this->latestShardResource = $latestShardResource;
  }
  /**
   * @return string
   */
  public function getLatestShardResource()
  {
    return $this->latestShardResource;
  }
  /**
   * Output only. Total number of shards.
   *
   * @param string $shardCount
   */
  public function setShardCount($shardCount)
  {
    $this->shardCount = $shardCount;
  }
  /**
   * @return string
   */
  public function getShardCount()
  {
    return $this->shardCount;
  }
  /**
   * Output only. The table name prefix of the shards. The name of any given
   * shard is `[table_prefix]YYYYMMDD`. For example, for the `MyTable20180101`
   * shard, the `table_prefix` is `MyTable`.
   *
   * @param string $tablePrefix
   */
  public function setTablePrefix($tablePrefix)
  {
    $this->tablePrefix = $tablePrefix;
  }
  /**
   * @return string
   */
  public function getTablePrefix()
  {
    return $this->tablePrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1BigQueryDateShardedSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1BigQueryDateShardedSpec');
