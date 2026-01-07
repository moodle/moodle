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

class GoogleCloudDataplexV1ActionIncompatibleDataSchema extends \Google\Collection
{
  /**
   * Schema change unspecified.
   */
  public const SCHEMA_CHANGE_SCHEMA_CHANGE_UNSPECIFIED = 'SCHEMA_CHANGE_UNSPECIFIED';
  /**
   * Newly discovered schema is incompatible with existing schema.
   */
  public const SCHEMA_CHANGE_INCOMPATIBLE = 'INCOMPATIBLE';
  /**
   * Newly discovered schema has changed from existing schema for data in a
   * curated zone.
   */
  public const SCHEMA_CHANGE_MODIFIED = 'MODIFIED';
  protected $collection_key = 'sampledDataLocations';
  /**
   * The existing and expected schema of the table. The schema is provided as a
   * JSON formatted structure listing columns and data types.
   *
   * @var string
   */
  public $existingSchema;
  /**
   * The new and incompatible schema within the table. The schema is provided as
   * a JSON formatted structured listing columns and data types.
   *
   * @var string
   */
  public $newSchema;
  /**
   * The list of data locations sampled and used for format/schema inference.
   *
   * @var string[]
   */
  public $sampledDataLocations;
  /**
   * Whether the action relates to a schema that is incompatible or modified.
   *
   * @var string
   */
  public $schemaChange;
  /**
   * The name of the table containing invalid data.
   *
   * @var string
   */
  public $table;

  /**
   * The existing and expected schema of the table. The schema is provided as a
   * JSON formatted structure listing columns and data types.
   *
   * @param string $existingSchema
   */
  public function setExistingSchema($existingSchema)
  {
    $this->existingSchema = $existingSchema;
  }
  /**
   * @return string
   */
  public function getExistingSchema()
  {
    return $this->existingSchema;
  }
  /**
   * The new and incompatible schema within the table. The schema is provided as
   * a JSON formatted structured listing columns and data types.
   *
   * @param string $newSchema
   */
  public function setNewSchema($newSchema)
  {
    $this->newSchema = $newSchema;
  }
  /**
   * @return string
   */
  public function getNewSchema()
  {
    return $this->newSchema;
  }
  /**
   * The list of data locations sampled and used for format/schema inference.
   *
   * @param string[] $sampledDataLocations
   */
  public function setSampledDataLocations($sampledDataLocations)
  {
    $this->sampledDataLocations = $sampledDataLocations;
  }
  /**
   * @return string[]
   */
  public function getSampledDataLocations()
  {
    return $this->sampledDataLocations;
  }
  /**
   * Whether the action relates to a schema that is incompatible or modified.
   *
   * Accepted values: SCHEMA_CHANGE_UNSPECIFIED, INCOMPATIBLE, MODIFIED
   *
   * @param self::SCHEMA_CHANGE_* $schemaChange
   */
  public function setSchemaChange($schemaChange)
  {
    $this->schemaChange = $schemaChange;
  }
  /**
   * @return self::SCHEMA_CHANGE_*
   */
  public function getSchemaChange()
  {
    return $this->schemaChange;
  }
  /**
   * The name of the table containing invalid data.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ActionIncompatibleDataSchema::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ActionIncompatibleDataSchema');
