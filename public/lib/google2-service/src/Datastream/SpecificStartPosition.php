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

class SpecificStartPosition extends \Google\Model
{
  protected $mongodbChangeStreamPositionType = MongodbChangeStreamPosition::class;
  protected $mongodbChangeStreamPositionDataType = '';
  protected $mysqlGtidPositionType = MysqlGtidPosition::class;
  protected $mysqlGtidPositionDataType = '';
  protected $mysqlLogPositionType = MysqlLogPosition::class;
  protected $mysqlLogPositionDataType = '';
  protected $oracleScnPositionType = OracleScnPosition::class;
  protected $oracleScnPositionDataType = '';
  protected $sqlServerLsnPositionType = SqlServerLsnPosition::class;
  protected $sqlServerLsnPositionDataType = '';

  /**
   * MongoDB change stream position to start replicating from.
   *
   * @param MongodbChangeStreamPosition $mongodbChangeStreamPosition
   */
  public function setMongodbChangeStreamPosition(MongodbChangeStreamPosition $mongodbChangeStreamPosition)
  {
    $this->mongodbChangeStreamPosition = $mongodbChangeStreamPosition;
  }
  /**
   * @return MongodbChangeStreamPosition
   */
  public function getMongodbChangeStreamPosition()
  {
    return $this->mongodbChangeStreamPosition;
  }
  /**
   * MySQL GTID set to start replicating from.
   *
   * @param MysqlGtidPosition $mysqlGtidPosition
   */
  public function setMysqlGtidPosition(MysqlGtidPosition $mysqlGtidPosition)
  {
    $this->mysqlGtidPosition = $mysqlGtidPosition;
  }
  /**
   * @return MysqlGtidPosition
   */
  public function getMysqlGtidPosition()
  {
    return $this->mysqlGtidPosition;
  }
  /**
   * MySQL specific log position to start replicating from.
   *
   * @param MysqlLogPosition $mysqlLogPosition
   */
  public function setMysqlLogPosition(MysqlLogPosition $mysqlLogPosition)
  {
    $this->mysqlLogPosition = $mysqlLogPosition;
  }
  /**
   * @return MysqlLogPosition
   */
  public function getMysqlLogPosition()
  {
    return $this->mysqlLogPosition;
  }
  /**
   * Oracle SCN to start replicating from.
   *
   * @param OracleScnPosition $oracleScnPosition
   */
  public function setOracleScnPosition(OracleScnPosition $oracleScnPosition)
  {
    $this->oracleScnPosition = $oracleScnPosition;
  }
  /**
   * @return OracleScnPosition
   */
  public function getOracleScnPosition()
  {
    return $this->oracleScnPosition;
  }
  /**
   * SqlServer LSN to start replicating from.
   *
   * @param SqlServerLsnPosition $sqlServerLsnPosition
   */
  public function setSqlServerLsnPosition(SqlServerLsnPosition $sqlServerLsnPosition)
  {
    $this->sqlServerLsnPosition = $sqlServerLsnPosition;
  }
  /**
   * @return SqlServerLsnPosition
   */
  public function getSqlServerLsnPosition()
  {
    return $this->sqlServerLsnPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpecificStartPosition::class, 'Google_Service_Datastream_SpecificStartPosition');
