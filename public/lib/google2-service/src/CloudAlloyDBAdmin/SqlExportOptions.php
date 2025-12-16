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

namespace Google\Service\CloudAlloyDBAdmin;

class SqlExportOptions extends \Google\Collection
{
  protected $collection_key = 'tables';
  /**
   * Optional. If true, output commands to DROP all the dumped database objects
   * prior to outputting the commands for creating them.
   *
   * @var bool
   */
  public $cleanTargetObjects;
  /**
   * Optional. If true, use DROP ... IF EXISTS commands to check for the
   * object's existence before dropping it in clean_target_objects mode.
   *
   * @var bool
   */
  public $ifExistTargetObjects;
  /**
   * Optional. If true, only export the schema.
   *
   * @var bool
   */
  public $schemaOnly;
  /**
   * Optional. Tables to export from.
   *
   * @var string[]
   */
  public $tables;

  /**
   * Optional. If true, output commands to DROP all the dumped database objects
   * prior to outputting the commands for creating them.
   *
   * @param bool $cleanTargetObjects
   */
  public function setCleanTargetObjects($cleanTargetObjects)
  {
    $this->cleanTargetObjects = $cleanTargetObjects;
  }
  /**
   * @return bool
   */
  public function getCleanTargetObjects()
  {
    return $this->cleanTargetObjects;
  }
  /**
   * Optional. If true, use DROP ... IF EXISTS commands to check for the
   * object's existence before dropping it in clean_target_objects mode.
   *
   * @param bool $ifExistTargetObjects
   */
  public function setIfExistTargetObjects($ifExistTargetObjects)
  {
    $this->ifExistTargetObjects = $ifExistTargetObjects;
  }
  /**
   * @return bool
   */
  public function getIfExistTargetObjects()
  {
    return $this->ifExistTargetObjects;
  }
  /**
   * Optional. If true, only export the schema.
   *
   * @param bool $schemaOnly
   */
  public function setSchemaOnly($schemaOnly)
  {
    $this->schemaOnly = $schemaOnly;
  }
  /**
   * @return bool
   */
  public function getSchemaOnly()
  {
    return $this->schemaOnly;
  }
  /**
   * Optional. Tables to export from.
   *
   * @param string[] $tables
   */
  public function setTables($tables)
  {
    $this->tables = $tables;
  }
  /**
   * @return string[]
   */
  public function getTables()
  {
    return $this->tables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlExportOptions::class, 'Google_Service_CloudAlloyDBAdmin_SqlExportOptions');
