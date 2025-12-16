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

class GoogleCloudDataplexV1DiscoveryEventTableDetails extends \Google\Model
{
  /**
   * An unspecified table type.
   */
  public const TYPE_TABLE_TYPE_UNSPECIFIED = 'TABLE_TYPE_UNSPECIFIED';
  /**
   * External table type.
   */
  public const TYPE_EXTERNAL_TABLE = 'EXTERNAL_TABLE';
  /**
   * BigLake table type.
   */
  public const TYPE_BIGLAKE_TABLE = 'BIGLAKE_TABLE';
  /**
   * Object table type for unstructured data.
   */
  public const TYPE_OBJECT_TABLE = 'OBJECT_TABLE';
  /**
   * The fully-qualified resource name of the table resource.
   *
   * @var string
   */
  public $table;
  /**
   * The type of the table resource.
   *
   * @var string
   */
  public $type;

  /**
   * The fully-qualified resource name of the table resource.
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * The type of the table resource.
   *
   * Accepted values: TABLE_TYPE_UNSPECIFIED, EXTERNAL_TABLE, BIGLAKE_TABLE,
   * OBJECT_TABLE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DiscoveryEventTableDetails::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DiscoveryEventTableDetails');
