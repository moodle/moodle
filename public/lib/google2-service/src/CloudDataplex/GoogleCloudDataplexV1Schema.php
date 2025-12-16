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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Schema extends \Google\Collection
{
  /**
   * PartitionStyle unspecified
   */
  public const PARTITION_STYLE_PARTITION_STYLE_UNSPECIFIED = 'PARTITION_STYLE_UNSPECIFIED';
  /**
   * Partitions are hive-compatible. Examples:
   * gs://bucket/path/to/table/dt=2019-10-31/lang=en,
   * gs://bucket/path/to/table/dt=2019-10-31/lang=en/late.
   */
  public const PARTITION_STYLE_HIVE_COMPATIBLE = 'HIVE_COMPATIBLE';
  protected $collection_key = 'partitionFields';
  protected $fieldsType = GoogleCloudDataplexV1SchemaSchemaField::class;
  protected $fieldsDataType = 'array';
  protected $partitionFieldsType = GoogleCloudDataplexV1SchemaPartitionField::class;
  protected $partitionFieldsDataType = 'array';
  /**
   * Optional. The structure of paths containing partition data within the
   * entity.
   *
   * @var string
   */
  public $partitionStyle;
  /**
   * Required. Set to true if user-managed or false if managed by Dataplex
   * Universal Catalog. The default is false (managed by Dataplex Universal
   * Catalog). Set to falseto enable Dataplex Universal Catalog discovery to
   * update the schema. including new data discovery, schema inference, and
   * schema evolution. Users retain the ability to input and edit the schema.
   * Dataplex Universal Catalog treats schema input by the user as though
   * produced by a previous Dataplex Universal Catalog discovery operation, and
   * it will evolve the schema and take action based on that treatment. Set to
   * true to fully manage the entity schema. This setting guarantees that
   * Dataplex Universal Catalog will not change schema fields.
   *
   * @var bool
   */
  public $userManaged;

  /**
   * Optional. The sequence of fields describing data in table entities. Note:
   * BigQuery SchemaFields are immutable.
   *
   * @param GoogleCloudDataplexV1SchemaSchemaField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleCloudDataplexV1SchemaSchemaField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Optional. The sequence of fields describing the partition structure in
   * entities. If this field is empty, there are no partitions within the data.
   *
   * @param GoogleCloudDataplexV1SchemaPartitionField[] $partitionFields
   */
  public function setPartitionFields($partitionFields)
  {
    $this->partitionFields = $partitionFields;
  }
  /**
   * @return GoogleCloudDataplexV1SchemaPartitionField[]
   */
  public function getPartitionFields()
  {
    return $this->partitionFields;
  }
  /**
   * Optional. The structure of paths containing partition data within the
   * entity.
   *
   * Accepted values: PARTITION_STYLE_UNSPECIFIED, HIVE_COMPATIBLE
   *
   * @param self::PARTITION_STYLE_* $partitionStyle
   */
  public function setPartitionStyle($partitionStyle)
  {
    $this->partitionStyle = $partitionStyle;
  }
  /**
   * @return self::PARTITION_STYLE_*
   */
  public function getPartitionStyle()
  {
    return $this->partitionStyle;
  }
  /**
   * Required. Set to true if user-managed or false if managed by Dataplex
   * Universal Catalog. The default is false (managed by Dataplex Universal
   * Catalog). Set to falseto enable Dataplex Universal Catalog discovery to
   * update the schema. including new data discovery, schema inference, and
   * schema evolution. Users retain the ability to input and edit the schema.
   * Dataplex Universal Catalog treats schema input by the user as though
   * produced by a previous Dataplex Universal Catalog discovery operation, and
   * it will evolve the schema and take action based on that treatment. Set to
   * true to fully manage the entity schema. This setting guarantees that
   * Dataplex Universal Catalog will not change schema fields.
   *
   * @param bool $userManaged
   */
  public function setUserManaged($userManaged)
  {
    $this->userManaged = $userManaged;
  }
  /**
   * @return bool
   */
  public function getUserManaged()
  {
    return $this->userManaged;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1Schema::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Schema');
