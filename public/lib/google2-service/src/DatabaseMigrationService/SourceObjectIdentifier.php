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

namespace Google\Service\DatabaseMigrationService;

class SourceObjectIdentifier extends \Google\Model
{
  /**
   * The type of the migration job object is unknown.
   */
  public const TYPE_MIGRATION_JOB_OBJECT_TYPE_UNSPECIFIED = 'MIGRATION_JOB_OBJECT_TYPE_UNSPECIFIED';
  /**
   * The migration job object is a database.
   */
  public const TYPE_DATABASE = 'DATABASE';
  /**
   * The migration job object is a schema.
   */
  public const TYPE_SCHEMA = 'SCHEMA';
  /**
   * The migration job object is a table.
   */
  public const TYPE_TABLE = 'TABLE';
  /**
   * Optional. The database name. This will be required only if the object uses
   * a database name as part of its unique identifier.
   *
   * @var string
   */
  public $database;
  /**
   * Optional. The schema name. This will be required only if the object uses a
   * schema name as part of its unique identifier.
   *
   * @var string
   */
  public $schema;
  /**
   * Optional. The table name. This will be required only if the object is a
   * level below database or schema.
   *
   * @var string
   */
  public $table;
  /**
   * Required. The type of the migration job object.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The database name. This will be required only if the object uses
   * a database name as part of its unique identifier.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Optional. The schema name. This will be required only if the object uses a
   * schema name as part of its unique identifier.
   *
   * @param string $schema
   */
  public function setSchema($schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return string
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Optional. The table name. This will be required only if the object is a
   * level below database or schema.
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
   * Required. The type of the migration job object.
   *
   * Accepted values: MIGRATION_JOB_OBJECT_TYPE_UNSPECIFIED, DATABASE, SCHEMA,
   * TABLE
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
class_alias(SourceObjectIdentifier::class, 'Google_Service_DatabaseMigrationService_SourceObjectIdentifier');
