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

namespace Google\Service\CloudAsset;

class PartitionSpec extends \Google\Model
{
  /**
   * Unspecified partition key. If used, it means using non-partitioned table.
   */
  public const PARTITION_KEY_PARTITION_KEY_UNSPECIFIED = 'PARTITION_KEY_UNSPECIFIED';
  /**
   * The time when the snapshot is taken. If specified as partition key, the
   * result table(s) is partitioned by the additional timestamp column,
   * readTime. If [read_time] in ExportAssetsRequest is specified, the readTime
   * column's value will be the same as it. Otherwise, its value will be the
   * current time that is used to take the snapshot.
   */
  public const PARTITION_KEY_READ_TIME = 'READ_TIME';
  /**
   * The time when the request is received and started to be processed. If
   * specified as partition key, the result table(s) is partitioned by the
   * requestTime column, an additional timestamp column representing when the
   * request was received.
   */
  public const PARTITION_KEY_REQUEST_TIME = 'REQUEST_TIME';
  /**
   * The partition key for BigQuery partitioned table.
   *
   * @var string
   */
  public $partitionKey;

  /**
   * The partition key for BigQuery partitioned table.
   *
   * Accepted values: PARTITION_KEY_UNSPECIFIED, READ_TIME, REQUEST_TIME
   *
   * @param self::PARTITION_KEY_* $partitionKey
   */
  public function setPartitionKey($partitionKey)
  {
    $this->partitionKey = $partitionKey;
  }
  /**
   * @return self::PARTITION_KEY_*
   */
  public function getPartitionKey()
  {
    return $this->partitionKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionSpec::class, 'Google_Service_CloudAsset_PartitionSpec');
