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

namespace Google\Service\SQLAdmin;

class DemoteMasterConfiguration extends \Google\Model
{
  /**
   * This is always `sql#demoteMasterConfiguration`.
   *
   * @var string
   */
  public $kind;
  protected $mysqlReplicaConfigurationType = DemoteMasterMySqlReplicaConfiguration::class;
  protected $mysqlReplicaConfigurationDataType = '';

  /**
   * This is always `sql#demoteMasterConfiguration`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * MySQL specific configuration when replicating from a MySQL on-premises
   * primary instance. Replication configuration information such as the
   * username, password, certificates, and keys are not stored in the instance
   * metadata. The configuration information is used only to set up the
   * replication connection and is stored by MySQL in a file named `master.info`
   * in the data directory.
   *
   * @param DemoteMasterMySqlReplicaConfiguration $mysqlReplicaConfiguration
   */
  public function setMysqlReplicaConfiguration(DemoteMasterMySqlReplicaConfiguration $mysqlReplicaConfiguration)
  {
    $this->mysqlReplicaConfiguration = $mysqlReplicaConfiguration;
  }
  /**
   * @return DemoteMasterMySqlReplicaConfiguration
   */
  public function getMysqlReplicaConfiguration()
  {
    return $this->mysqlReplicaConfiguration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DemoteMasterConfiguration::class, 'Google_Service_SQLAdmin_DemoteMasterConfiguration');
