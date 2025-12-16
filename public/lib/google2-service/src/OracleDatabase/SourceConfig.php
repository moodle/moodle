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

namespace Google\Service\OracleDatabase;

class SourceConfig extends \Google\Model
{
  /**
   * Optional. This field specifies if the replication of automatic backups is
   * enabled when creating a Data Guard.
   *
   * @var bool
   */
  public $automaticBackupsReplicationEnabled;
  /**
   * Optional. The name of the primary Autonomous Database that is used to
   * create a Peer Autonomous Database from a source.
   *
   * @var string
   */
  public $autonomousDatabase;

  /**
   * Optional. This field specifies if the replication of automatic backups is
   * enabled when creating a Data Guard.
   *
   * @param bool $automaticBackupsReplicationEnabled
   */
  public function setAutomaticBackupsReplicationEnabled($automaticBackupsReplicationEnabled)
  {
    $this->automaticBackupsReplicationEnabled = $automaticBackupsReplicationEnabled;
  }
  /**
   * @return bool
   */
  public function getAutomaticBackupsReplicationEnabled()
  {
    return $this->automaticBackupsReplicationEnabled;
  }
  /**
   * Optional. The name of the primary Autonomous Database that is used to
   * create a Peer Autonomous Database from a source.
   *
   * @param string $autonomousDatabase
   */
  public function setAutonomousDatabase($autonomousDatabase)
  {
    $this->autonomousDatabase = $autonomousDatabase;
  }
  /**
   * @return string
   */
  public function getAutonomousDatabase()
  {
    return $this->autonomousDatabase;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceConfig::class, 'Google_Service_OracleDatabase_SourceConfig');
