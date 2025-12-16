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

namespace Google\Service\BigtableAdmin;

class RestoreTableRequest extends \Google\Model
{
  /**
   * Name of the backup from which to restore. Values are of the form
   * `projects//instances//clusters//backups/`.
   *
   * @var string
   */
  public $backup;
  /**
   * Required. The id of the table to create and restore to. This table must not
   * already exist. The `table_id` appended to `parent` forms the full table
   * name of the form `projects//instances//tables/`.
   *
   * @var string
   */
  public $tableId;

  /**
   * Name of the backup from which to restore. Values are of the form
   * `projects//instances//clusters//backups/`.
   *
   * @param string $backup
   */
  public function setBackup($backup)
  {
    $this->backup = $backup;
  }
  /**
   * @return string
   */
  public function getBackup()
  {
    return $this->backup;
  }
  /**
   * Required. The id of the table to create and restore to. This table must not
   * already exist. The `table_id` appended to `parent` forms the full table
   * name of the form `projects//instances//tables/`.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreTableRequest::class, 'Google_Service_BigtableAdmin_RestoreTableRequest');
