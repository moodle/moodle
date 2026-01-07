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

namespace Google\Service\Datastream;

class IngestionTimePartition extends \Google\Model
{
  /**
   * Unspecified partitioing interval.
   */
  public const PARTITIONING_TIME_GRANULARITY_PARTITIONING_TIME_GRANULARITY_UNSPECIFIED = 'PARTITIONING_TIME_GRANULARITY_UNSPECIFIED';
  /**
   * Hourly partitioning.
   */
  public const PARTITIONING_TIME_GRANULARITY_PARTITIONING_TIME_GRANULARITY_HOUR = 'PARTITIONING_TIME_GRANULARITY_HOUR';
  /**
   * Daily partitioning.
   */
  public const PARTITIONING_TIME_GRANULARITY_PARTITIONING_TIME_GRANULARITY_DAY = 'PARTITIONING_TIME_GRANULARITY_DAY';
  /**
   * Monthly partitioning.
   */
  public const PARTITIONING_TIME_GRANULARITY_PARTITIONING_TIME_GRANULARITY_MONTH = 'PARTITIONING_TIME_GRANULARITY_MONTH';
  /**
   * Yearly partitioning.
   */
  public const PARTITIONING_TIME_GRANULARITY_PARTITIONING_TIME_GRANULARITY_YEAR = 'PARTITIONING_TIME_GRANULARITY_YEAR';
  /**
   * Optional. Partition granularity
   *
   * @var string
   */
  public $partitioningTimeGranularity;

  /**
   * Optional. Partition granularity
   *
   * Accepted values: PARTITIONING_TIME_GRANULARITY_UNSPECIFIED,
   * PARTITIONING_TIME_GRANULARITY_HOUR, PARTITIONING_TIME_GRANULARITY_DAY,
   * PARTITIONING_TIME_GRANULARITY_MONTH, PARTITIONING_TIME_GRANULARITY_YEAR
   *
   * @param self::PARTITIONING_TIME_GRANULARITY_* $partitioningTimeGranularity
   */
  public function setPartitioningTimeGranularity($partitioningTimeGranularity)
  {
    $this->partitioningTimeGranularity = $partitioningTimeGranularity;
  }
  /**
   * @return self::PARTITIONING_TIME_GRANULARITY_*
   */
  public function getPartitioningTimeGranularity()
  {
    return $this->partitioningTimeGranularity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IngestionTimePartition::class, 'Google_Service_Datastream_IngestionTimePartition');
