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

class BigQueryPartitioning extends \Google\Model
{
  protected $ingestionTimePartitionType = IngestionTimePartition::class;
  protected $ingestionTimePartitionDataType = '';
  protected $integerRangePartitionType = IntegerRangePartition::class;
  protected $integerRangePartitionDataType = '';
  /**
   * Optional. If true, queries over the table require a partition filter.
   *
   * @var bool
   */
  public $requirePartitionFilter;
  protected $timeUnitPartitionType = TimeUnitPartition::class;
  protected $timeUnitPartitionDataType = '';

  /**
   * Ingestion time partitioning.
   *
   * @param IngestionTimePartition $ingestionTimePartition
   */
  public function setIngestionTimePartition(IngestionTimePartition $ingestionTimePartition)
  {
    $this->ingestionTimePartition = $ingestionTimePartition;
  }
  /**
   * @return IngestionTimePartition
   */
  public function getIngestionTimePartition()
  {
    return $this->ingestionTimePartition;
  }
  /**
   * Integer range partitioning.
   *
   * @param IntegerRangePartition $integerRangePartition
   */
  public function setIntegerRangePartition(IntegerRangePartition $integerRangePartition)
  {
    $this->integerRangePartition = $integerRangePartition;
  }
  /**
   * @return IntegerRangePartition
   */
  public function getIntegerRangePartition()
  {
    return $this->integerRangePartition;
  }
  /**
   * Optional. If true, queries over the table require a partition filter.
   *
   * @param bool $requirePartitionFilter
   */
  public function setRequirePartitionFilter($requirePartitionFilter)
  {
    $this->requirePartitionFilter = $requirePartitionFilter;
  }
  /**
   * @return bool
   */
  public function getRequirePartitionFilter()
  {
    return $this->requirePartitionFilter;
  }
  /**
   * Time unit column partitioning.
   *
   * @param TimeUnitPartition $timeUnitPartition
   */
  public function setTimeUnitPartition(TimeUnitPartition $timeUnitPartition)
  {
    $this->timeUnitPartition = $timeUnitPartition;
  }
  /**
   * @return TimeUnitPartition
   */
  public function getTimeUnitPartition()
  {
    return $this->timeUnitPartition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryPartitioning::class, 'Google_Service_Datastream_BigQueryPartitioning');
