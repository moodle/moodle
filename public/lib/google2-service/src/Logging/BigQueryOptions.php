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

namespace Google\Service\Logging;

class BigQueryOptions extends \Google\Model
{
  /**
   * Optional. Whether to use BigQuery's partition tables
   * (https://cloud.google.com/bigquery/docs/partitioned-tables). By default,
   * Cloud Logging creates dated tables based on the log entries' timestamps,
   * e.g. syslog_20170523. With partitioned tables the date suffix is no longer
   * present and special query syntax
   * (https://cloud.google.com/bigquery/docs/querying-partitioned-tables) has to
   * be used instead. In both cases, tables are sharded based on UTC timezone.
   *
   * @var bool
   */
  public $usePartitionedTables;
  /**
   * Output only. True if new timestamp column based partitioning is in use,
   * false if legacy ingress-time partitioning is in use.All new sinks will have
   * this field set true and will use timestamp column based partitioning. If
   * use_partitioned_tables is false, this value has no meaning and will be
   * false. Legacy sinks using partitioned tables will have this field set to
   * false.
   *
   * @var bool
   */
  public $usesTimestampColumnPartitioning;

  /**
   * Optional. Whether to use BigQuery's partition tables
   * (https://cloud.google.com/bigquery/docs/partitioned-tables). By default,
   * Cloud Logging creates dated tables based on the log entries' timestamps,
   * e.g. syslog_20170523. With partitioned tables the date suffix is no longer
   * present and special query syntax
   * (https://cloud.google.com/bigquery/docs/querying-partitioned-tables) has to
   * be used instead. In both cases, tables are sharded based on UTC timezone.
   *
   * @param bool $usePartitionedTables
   */
  public function setUsePartitionedTables($usePartitionedTables)
  {
    $this->usePartitionedTables = $usePartitionedTables;
  }
  /**
   * @return bool
   */
  public function getUsePartitionedTables()
  {
    return $this->usePartitionedTables;
  }
  /**
   * Output only. True if new timestamp column based partitioning is in use,
   * false if legacy ingress-time partitioning is in use.All new sinks will have
   * this field set true and will use timestamp column based partitioning. If
   * use_partitioned_tables is false, this value has no meaning and will be
   * false. Legacy sinks using partitioned tables will have this field set to
   * false.
   *
   * @param bool $usesTimestampColumnPartitioning
   */
  public function setUsesTimestampColumnPartitioning($usesTimestampColumnPartitioning)
  {
    $this->usesTimestampColumnPartitioning = $usesTimestampColumnPartitioning;
  }
  /**
   * @return bool
   */
  public function getUsesTimestampColumnPartitioning()
  {
    return $this->usesTimestampColumnPartitioning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryOptions::class, 'Google_Service_Logging_BigQueryOptions');
