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

class HivePartitioningOptions extends \Google\Collection
{
  protected $collection_key = 'fields';
  /**
   * Output only. For permanent external tables, this field is populated with
   * the hive partition keys in the order they were inferred. The types of the
   * partition keys can be deduced by checking the table schema (which will
   * include the partition keys). Not every API will populate this field in the
   * output. For example, Tables.Get will populate it, but Tables.List will not
   * contain this field.
   *
   * @var string[]
   */
  public $fields;
  /**
   * Optional. When set, what mode of hive partitioning to use when reading
   * data. The following modes are supported: * AUTO: automatically infer
   * partition key name(s) and type(s). * STRINGS: automatically infer partition
   * key name(s). All types are strings. * CUSTOM: partition key schema is
   * encoded in the source URI prefix. Not all storage formats support hive
   * partitioning. Requesting hive partitioning on an unsupported format will
   * lead to an error. Currently supported formats are: JSON, CSV, ORC, Avro and
   * Parquet.
   *
   * @var string
   */
  public $mode;
  /**
   * Optional. If set to true, queries over this table require a partition
   * filter that can be used for partition elimination to be specified. Note
   * that this field should only be true when creating a permanent external
   * table or querying a temporary external table. Hive-partitioned loads with
   * require_partition_filter explicitly set to true will fail.
   *
   * @var bool
   */
  public $requirePartitionFilter;
  /**
   * Optional. When hive partition detection is requested, a common prefix for
   * all source uris must be required. The prefix must end immediately before
   * the partition key encoding begins. For example, consider files following
   * this data layout:
   * gs://bucket/path_to_table/dt=2019-06-01/country=USA/id=7/file.avro
   * gs://bucket/path_to_table/dt=2019-05-31/country=CA/id=3/file.avro When hive
   * partitioning is requested with either AUTO or STRINGS detection, the common
   * prefix can be either of gs://bucket/path_to_table or
   * gs://bucket/path_to_table/. CUSTOM detection requires encoding the
   * partitioning schema immediately after the common prefix. For CUSTOM, any of
   * * gs://bucket/path_to_table/{dt:DATE}/{country:STRING}/{id:INTEGER} *
   * gs://bucket/path_to_table/{dt:STRING}/{country:STRING}/{id:INTEGER} *
   * gs://bucket/path_to_table/{dt:DATE}/{country:STRING}/{id:STRING} would all
   * be valid source URI prefixes.
   *
   * @var string
   */
  public $sourceUriPrefix;

  /**
   * Output only. For permanent external tables, this field is populated with
   * the hive partition keys in the order they were inferred. The types of the
   * partition keys can be deduced by checking the table schema (which will
   * include the partition keys). Not every API will populate this field in the
   * output. For example, Tables.Get will populate it, but Tables.List will not
   * contain this field.
   *
   * @param string[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return string[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Optional. When set, what mode of hive partitioning to use when reading
   * data. The following modes are supported: * AUTO: automatically infer
   * partition key name(s) and type(s). * STRINGS: automatically infer partition
   * key name(s). All types are strings. * CUSTOM: partition key schema is
   * encoded in the source URI prefix. Not all storage formats support hive
   * partitioning. Requesting hive partitioning on an unsupported format will
   * lead to an error. Currently supported formats are: JSON, CSV, ORC, Avro and
   * Parquet.
   *
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Optional. If set to true, queries over this table require a partition
   * filter that can be used for partition elimination to be specified. Note
   * that this field should only be true when creating a permanent external
   * table or querying a temporary external table. Hive-partitioned loads with
   * require_partition_filter explicitly set to true will fail.
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
   * Optional. When hive partition detection is requested, a common prefix for
   * all source uris must be required. The prefix must end immediately before
   * the partition key encoding begins. For example, consider files following
   * this data layout:
   * gs://bucket/path_to_table/dt=2019-06-01/country=USA/id=7/file.avro
   * gs://bucket/path_to_table/dt=2019-05-31/country=CA/id=3/file.avro When hive
   * partitioning is requested with either AUTO or STRINGS detection, the common
   * prefix can be either of gs://bucket/path_to_table or
   * gs://bucket/path_to_table/. CUSTOM detection requires encoding the
   * partitioning schema immediately after the common prefix. For CUSTOM, any of
   * * gs://bucket/path_to_table/{dt:DATE}/{country:STRING}/{id:INTEGER} *
   * gs://bucket/path_to_table/{dt:STRING}/{country:STRING}/{id:INTEGER} *
   * gs://bucket/path_to_table/{dt:DATE}/{country:STRING}/{id:STRING} would all
   * be valid source URI prefixes.
   *
   * @param string $sourceUriPrefix
   */
  public function setSourceUriPrefix($sourceUriPrefix)
  {
    $this->sourceUriPrefix = $sourceUriPrefix;
  }
  /**
   * @return string
   */
  public function getSourceUriPrefix()
  {
    return $this->sourceUriPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HivePartitioningOptions::class, 'Google_Service_Bigquery_HivePartitioningOptions');
