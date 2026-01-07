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

namespace Google\Service\Spanner;

class UpdateDatabaseRequest extends \Google\Model
{
  protected $databaseType = Database::class;
  protected $databaseDataType = '';
  /**
   * Required. The list of fields to update. Currently, only
   * `enable_drop_protection` field can be updated.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The database to update. The `name` field of the database is of
   * the form `projects//instances//databases/`.
   *
   * @param Database $database
   */
  public function setDatabase(Database $database)
  {
    $this->database = $database;
  }
  /**
   * @return Database
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Required. The list of fields to update. Currently, only
   * `enable_drop_protection` field can be updated.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateDatabaseRequest::class, 'Google_Service_Spanner_UpdateDatabaseRequest');
