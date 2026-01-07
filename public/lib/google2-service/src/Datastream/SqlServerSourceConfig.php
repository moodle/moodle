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

class SqlServerSourceConfig extends \Google\Model
{
  protected $changeTablesType = SqlServerChangeTables::class;
  protected $changeTablesDataType = '';
  protected $excludeObjectsType = SqlServerRdbms::class;
  protected $excludeObjectsDataType = '';
  protected $includeObjectsType = SqlServerRdbms::class;
  protected $includeObjectsDataType = '';
  /**
   * Max concurrent backfill tasks.
   *
   * @var int
   */
  public $maxConcurrentBackfillTasks;
  /**
   * Max concurrent CDC tasks.
   *
   * @var int
   */
  public $maxConcurrentCdcTasks;
  protected $transactionLogsType = SqlServerTransactionLogs::class;
  protected $transactionLogsDataType = '';

  /**
   * CDC reader reads from change tables.
   *
   * @param SqlServerChangeTables $changeTables
   */
  public function setChangeTables(SqlServerChangeTables $changeTables)
  {
    $this->changeTables = $changeTables;
  }
  /**
   * @return SqlServerChangeTables
   */
  public function getChangeTables()
  {
    return $this->changeTables;
  }
  /**
   * SQLServer objects to exclude from the stream.
   *
   * @param SqlServerRdbms $excludeObjects
   */
  public function setExcludeObjects(SqlServerRdbms $excludeObjects)
  {
    $this->excludeObjects = $excludeObjects;
  }
  /**
   * @return SqlServerRdbms
   */
  public function getExcludeObjects()
  {
    return $this->excludeObjects;
  }
  /**
   * SQLServer objects to include in the stream.
   *
   * @param SqlServerRdbms $includeObjects
   */
  public function setIncludeObjects(SqlServerRdbms $includeObjects)
  {
    $this->includeObjects = $includeObjects;
  }
  /**
   * @return SqlServerRdbms
   */
  public function getIncludeObjects()
  {
    return $this->includeObjects;
  }
  /**
   * Max concurrent backfill tasks.
   *
   * @param int $maxConcurrentBackfillTasks
   */
  public function setMaxConcurrentBackfillTasks($maxConcurrentBackfillTasks)
  {
    $this->maxConcurrentBackfillTasks = $maxConcurrentBackfillTasks;
  }
  /**
   * @return int
   */
  public function getMaxConcurrentBackfillTasks()
  {
    return $this->maxConcurrentBackfillTasks;
  }
  /**
   * Max concurrent CDC tasks.
   *
   * @param int $maxConcurrentCdcTasks
   */
  public function setMaxConcurrentCdcTasks($maxConcurrentCdcTasks)
  {
    $this->maxConcurrentCdcTasks = $maxConcurrentCdcTasks;
  }
  /**
   * @return int
   */
  public function getMaxConcurrentCdcTasks()
  {
    return $this->maxConcurrentCdcTasks;
  }
  /**
   * CDC reader reads from transaction logs.
   *
   * @param SqlServerTransactionLogs $transactionLogs
   */
  public function setTransactionLogs(SqlServerTransactionLogs $transactionLogs)
  {
    $this->transactionLogs = $transactionLogs;
  }
  /**
   * @return SqlServerTransactionLogs
   */
  public function getTransactionLogs()
  {
    return $this->transactionLogs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerSourceConfig::class, 'Google_Service_Datastream_SqlServerSourceConfig');
