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

namespace Google\Service\CloudRedis;

class ClusterPersistenceConfig extends \Google\Model
{
  /**
   * Not set.
   */
  public const MODE_PERSISTENCE_MODE_UNSPECIFIED = 'PERSISTENCE_MODE_UNSPECIFIED';
  /**
   * Persistence is disabled, and any snapshot data is deleted.
   */
  public const MODE_DISABLED = 'DISABLED';
  /**
   * RDB based persistence is enabled.
   */
  public const MODE_RDB = 'RDB';
  /**
   * AOF based persistence is enabled.
   */
  public const MODE_AOF = 'AOF';
  protected $aofConfigType = AOFConfig::class;
  protected $aofConfigDataType = '';
  /**
   * Optional. The mode of persistence.
   *
   * @var string
   */
  public $mode;
  protected $rdbConfigType = RDBConfig::class;
  protected $rdbConfigDataType = '';

  /**
   * Optional. AOF configuration. This field will be ignored if mode is not AOF.
   *
   * @param AOFConfig $aofConfig
   */
  public function setAofConfig(AOFConfig $aofConfig)
  {
    $this->aofConfig = $aofConfig;
  }
  /**
   * @return AOFConfig
   */
  public function getAofConfig()
  {
    return $this->aofConfig;
  }
  /**
   * Optional. The mode of persistence.
   *
   * Accepted values: PERSISTENCE_MODE_UNSPECIFIED, DISABLED, RDB, AOF
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Optional. RDB configuration. This field will be ignored if mode is not RDB.
   *
   * @param RDBConfig $rdbConfig
   */
  public function setRdbConfig(RDBConfig $rdbConfig)
  {
    $this->rdbConfig = $rdbConfig;
  }
  /**
   * @return RDBConfig
   */
  public function getRdbConfig()
  {
    return $this->rdbConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterPersistenceConfig::class, 'Google_Service_CloudRedis_ClusterPersistenceConfig');
