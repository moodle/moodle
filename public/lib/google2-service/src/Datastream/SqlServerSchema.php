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

class SqlServerSchema extends \Google\Collection
{
  protected $collection_key = 'tables';
  /**
   * Schema name.
   *
   * @var string
   */
  public $schema;
  protected $tablesType = SqlServerTable::class;
  protected $tablesDataType = 'array';

  /**
   * Schema name.
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
   * Tables in the schema.
   *
   * @param SqlServerTable[] $tables
   */
  public function setTables($tables)
  {
    $this->tables = $tables;
  }
  /**
   * @return SqlServerTable[]
   */
  public function getTables()
  {
    return $this->tables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerSchema::class, 'Google_Service_Datastream_SqlServerSchema');
