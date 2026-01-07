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

class MysqlSourceConfig extends \Google\Model
{
  protected $binaryLogPositionType = BinaryLogPosition::class;
  protected $binaryLogPositionDataType = '';
  protected $excludeObjectsType = MysqlRdbms::class;
  protected $excludeObjectsDataType = '';
  protected $gtidType = Gtid::class;
  protected $gtidDataType = '';
  protected $includeObjectsType = MysqlRdbms::class;
  protected $includeObjectsDataType = '';
  /**
   * Maximum number of concurrent backfill tasks. The number should be non
   * negative. If not set (or set to 0), the system's default value will be
   * used.
   *
   * @var int
   */
  public $maxConcurrentBackfillTasks;
  /**
   * Maximum number of concurrent CDC tasks. The number should be non negative.
   * If not set (or set to 0), the system's default value will be used.
   *
   * @var int
   */
  public $maxConcurrentCdcTasks;

  /**
   * Use Binary log position based replication.
   *
   * @param BinaryLogPosition $binaryLogPosition
   */
  public function setBinaryLogPosition(BinaryLogPosition $binaryLogPosition)
  {
    $this->binaryLogPosition = $binaryLogPosition;
  }
  /**
   * @return BinaryLogPosition
   */
  public function getBinaryLogPosition()
  {
    return $this->binaryLogPosition;
  }
  /**
   * MySQL objects to exclude from the stream.
   *
   * @param MysqlRdbms $excludeObjects
   */
  public function setExcludeObjects(MysqlRdbms $excludeObjects)
  {
    $this->excludeObjects = $excludeObjects;
  }
  /**
   * @return MysqlRdbms
   */
  public function getExcludeObjects()
  {
    return $this->excludeObjects;
  }
  /**
   * Use GTID based replication.
   *
   * @param Gtid $gtid
   */
  public function setGtid(Gtid $gtid)
  {
    $this->gtid = $gtid;
  }
  /**
   * @return Gtid
   */
  public function getGtid()
  {
    return $this->gtid;
  }
  /**
   * MySQL objects to retrieve from the source.
   *
   * @param MysqlRdbms $includeObjects
   */
  public function setIncludeObjects(MysqlRdbms $includeObjects)
  {
    $this->includeObjects = $includeObjects;
  }
  /**
   * @return MysqlRdbms
   */
  public function getIncludeObjects()
  {
    return $this->includeObjects;
  }
  /**
   * Maximum number of concurrent backfill tasks. The number should be non
   * negative. If not set (or set to 0), the system's default value will be
   * used.
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
   * Maximum number of concurrent CDC tasks. The number should be non negative.
   * If not set (or set to 0), the system's default value will be used.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MysqlSourceConfig::class, 'Google_Service_Datastream_MysqlSourceConfig');
