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

namespace Google\Service\MigrationCenterAPI;

class MySqlStorageEngineDetails extends \Google\Model
{
  /**
   * Unspecified storage engine.
   */
  public const ENGINE_ENGINE_UNSPECIFIED = 'ENGINE_UNSPECIFIED';
  /**
   * InnoDB.
   */
  public const ENGINE_INNODB = 'INNODB';
  /**
   * MyISAM.
   */
  public const ENGINE_MYISAM = 'MYISAM';
  /**
   * Memory.
   */
  public const ENGINE_MEMORY = 'MEMORY';
  /**
   * CSV.
   */
  public const ENGINE_CSV = 'CSV';
  /**
   * Archive.
   */
  public const ENGINE_ARCHIVE = 'ARCHIVE';
  /**
   * Blackhole.
   */
  public const ENGINE_BLACKHOLE = 'BLACKHOLE';
  /**
   * NDB.
   */
  public const ENGINE_NDB = 'NDB';
  /**
   * Merge.
   */
  public const ENGINE_MERGE = 'MERGE';
  /**
   * Federated.
   */
  public const ENGINE_FEDERATED = 'FEDERATED';
  /**
   * Example.
   */
  public const ENGINE_EXAMPLE = 'EXAMPLE';
  /**
   * Other.
   */
  public const ENGINE_OTHER = 'OTHER';
  /**
   * Optional. The number of encrypted tables.
   *
   * @var int
   */
  public $encryptedTableCount;
  /**
   * Required. The storage engine.
   *
   * @var string
   */
  public $engine;
  /**
   * Optional. The number of tables.
   *
   * @var int
   */
  public $tableCount;

  /**
   * Optional. The number of encrypted tables.
   *
   * @param int $encryptedTableCount
   */
  public function setEncryptedTableCount($encryptedTableCount)
  {
    $this->encryptedTableCount = $encryptedTableCount;
  }
  /**
   * @return int
   */
  public function getEncryptedTableCount()
  {
    return $this->encryptedTableCount;
  }
  /**
   * Required. The storage engine.
   *
   * Accepted values: ENGINE_UNSPECIFIED, INNODB, MYISAM, MEMORY, CSV, ARCHIVE,
   * BLACKHOLE, NDB, MERGE, FEDERATED, EXAMPLE, OTHER
   *
   * @param self::ENGINE_* $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return self::ENGINE_*
   */
  public function getEngine()
  {
    return $this->engine;
  }
  /**
   * Optional. The number of tables.
   *
   * @param int $tableCount
   */
  public function setTableCount($tableCount)
  {
    $this->tableCount = $tableCount;
  }
  /**
   * @return int
   */
  public function getTableCount()
  {
    return $this->tableCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MySqlStorageEngineDetails::class, 'Google_Service_MigrationCenterAPI_MySqlStorageEngineDetails');
