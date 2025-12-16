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

namespace Google\Service\Bigquery;

class PartitioningDefinition extends \Google\Collection
{
  protected $collection_key = 'partitionedColumn';
  protected $partitionedColumnType = PartitionedColumn::class;
  protected $partitionedColumnDataType = 'array';

  /**
   * Optional. Details about each partitioning column. This field is output only
   * for all partitioning types other than metastore partitioned tables.
   * BigQuery native tables only support 1 partitioning column. Other table
   * types may support 0, 1 or more partitioning columns. For metastore
   * partitioned tables, the order must match the definition order in the Hive
   * Metastore, where it must match the physical layout of the table. For
   * example, CREATE TABLE a_table(id BIGINT, name STRING) PARTITIONED BY (city
   * STRING, state STRING). In this case the values must be ['city', 'state'] in
   * that order.
   *
   * @param PartitionedColumn[] $partitionedColumn
   */
  public function setPartitionedColumn($partitionedColumn)
  {
    $this->partitionedColumn = $partitionedColumn;
  }
  /**
   * @return PartitionedColumn[]
   */
  public function getPartitionedColumn()
  {
    return $this->partitionedColumn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitioningDefinition::class, 'Google_Service_Bigquery_PartitioningDefinition');
