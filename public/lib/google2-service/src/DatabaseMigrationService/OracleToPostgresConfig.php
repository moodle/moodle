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

namespace Google\Service\DatabaseMigrationService;

class OracleToPostgresConfig extends \Google\Model
{
  protected $oracleSourceConfigType = OracleSourceConfig::class;
  protected $oracleSourceConfigDataType = '';
  protected $postgresDestinationConfigType = PostgresDestinationConfig::class;
  protected $postgresDestinationConfigDataType = '';

  /**
   * Optional. Configuration for Oracle source.
   *
   * @param OracleSourceConfig $oracleSourceConfig
   */
  public function setOracleSourceConfig(OracleSourceConfig $oracleSourceConfig)
  {
    $this->oracleSourceConfig = $oracleSourceConfig;
  }
  /**
   * @return OracleSourceConfig
   */
  public function getOracleSourceConfig()
  {
    return $this->oracleSourceConfig;
  }
  /**
   * Optional. Configuration for Postgres destination.
   *
   * @param PostgresDestinationConfig $postgresDestinationConfig
   */
  public function setPostgresDestinationConfig(PostgresDestinationConfig $postgresDestinationConfig)
  {
    $this->postgresDestinationConfig = $postgresDestinationConfig;
  }
  /**
   * @return PostgresDestinationConfig
   */
  public function getPostgresDestinationConfig()
  {
    return $this->postgresDestinationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OracleToPostgresConfig::class, 'Google_Service_DatabaseMigrationService_OracleToPostgresConfig');
