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

class SourceConfig extends \Google\Model
{
  protected $mongodbSourceConfigType = MongodbSourceConfig::class;
  protected $mongodbSourceConfigDataType = '';
  protected $mysqlSourceConfigType = MysqlSourceConfig::class;
  protected $mysqlSourceConfigDataType = '';
  protected $oracleSourceConfigType = OracleSourceConfig::class;
  protected $oracleSourceConfigDataType = '';
  protected $postgresqlSourceConfigType = PostgresqlSourceConfig::class;
  protected $postgresqlSourceConfigDataType = '';
  protected $salesforceSourceConfigType = SalesforceSourceConfig::class;
  protected $salesforceSourceConfigDataType = '';
  /**
   * Required. Source connection profile resource. Format:
   * `projects/{project}/locations/{location}/connectionProfiles/{name}`
   *
   * @var string
   */
  public $sourceConnectionProfile;
  protected $sqlServerSourceConfigType = SqlServerSourceConfig::class;
  protected $sqlServerSourceConfigDataType = '';

  /**
   * MongoDB data source configuration.
   *
   * @param MongodbSourceConfig $mongodbSourceConfig
   */
  public function setMongodbSourceConfig(MongodbSourceConfig $mongodbSourceConfig)
  {
    $this->mongodbSourceConfig = $mongodbSourceConfig;
  }
  /**
   * @return MongodbSourceConfig
   */
  public function getMongodbSourceConfig()
  {
    return $this->mongodbSourceConfig;
  }
  /**
   * MySQL data source configuration.
   *
   * @param MysqlSourceConfig $mysqlSourceConfig
   */
  public function setMysqlSourceConfig(MysqlSourceConfig $mysqlSourceConfig)
  {
    $this->mysqlSourceConfig = $mysqlSourceConfig;
  }
  /**
   * @return MysqlSourceConfig
   */
  public function getMysqlSourceConfig()
  {
    return $this->mysqlSourceConfig;
  }
  /**
   * Oracle data source configuration.
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
   * PostgreSQL data source configuration.
   *
   * @param PostgresqlSourceConfig $postgresqlSourceConfig
   */
  public function setPostgresqlSourceConfig(PostgresqlSourceConfig $postgresqlSourceConfig)
  {
    $this->postgresqlSourceConfig = $postgresqlSourceConfig;
  }
  /**
   * @return PostgresqlSourceConfig
   */
  public function getPostgresqlSourceConfig()
  {
    return $this->postgresqlSourceConfig;
  }
  /**
   * Salesforce data source configuration.
   *
   * @param SalesforceSourceConfig $salesforceSourceConfig
   */
  public function setSalesforceSourceConfig(SalesforceSourceConfig $salesforceSourceConfig)
  {
    $this->salesforceSourceConfig = $salesforceSourceConfig;
  }
  /**
   * @return SalesforceSourceConfig
   */
  public function getSalesforceSourceConfig()
  {
    return $this->salesforceSourceConfig;
  }
  /**
   * Required. Source connection profile resource. Format:
   * `projects/{project}/locations/{location}/connectionProfiles/{name}`
   *
   * @param string $sourceConnectionProfile
   */
  public function setSourceConnectionProfile($sourceConnectionProfile)
  {
    $this->sourceConnectionProfile = $sourceConnectionProfile;
  }
  /**
   * @return string
   */
  public function getSourceConnectionProfile()
  {
    return $this->sourceConnectionProfile;
  }
  /**
   * SQLServer data source configuration.
   *
   * @param SqlServerSourceConfig $sqlServerSourceConfig
   */
  public function setSqlServerSourceConfig(SqlServerSourceConfig $sqlServerSourceConfig)
  {
    $this->sqlServerSourceConfig = $sqlServerSourceConfig;
  }
  /**
   * @return SqlServerSourceConfig
   */
  public function getSqlServerSourceConfig()
  {
    return $this->sqlServerSourceConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceConfig::class, 'Google_Service_Datastream_SourceConfig');
