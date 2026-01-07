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

class DatabaseDeploymentDetails extends \Google\Model
{
  protected $aggregatedStatsType = DatabaseDeploymentDetailsAggregatedStats::class;
  protected $aggregatedStatsDataType = '';
  protected $awsRdsType = AwsRds::class;
  protected $awsRdsDataType = '';
  /**
   * Optional. The database deployment edition.
   *
   * @var string
   */
  public $edition;
  /**
   * Optional. The database deployment generated ID.
   *
   * @var string
   */
  public $generatedId;
  /**
   * Optional. A manual unique ID set by the user.
   *
   * @var string
   */
  public $manualUniqueId;
  protected $mysqlType = MysqlDatabaseDeployment::class;
  protected $mysqlDataType = '';
  protected $postgresqlType = PostgreSqlDatabaseDeployment::class;
  protected $postgresqlDataType = '';
  protected $sqlServerType = SqlServerDatabaseDeployment::class;
  protected $sqlServerDataType = '';
  protected $topologyType = DatabaseDeploymentTopology::class;
  protected $topologyDataType = '';
  /**
   * Optional. The database deployment version.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Aggregated stats for the database deployment.
   *
   * @param DatabaseDeploymentDetailsAggregatedStats $aggregatedStats
   */
  public function setAggregatedStats(DatabaseDeploymentDetailsAggregatedStats $aggregatedStats)
  {
    $this->aggregatedStats = $aggregatedStats;
  }
  /**
   * @return DatabaseDeploymentDetailsAggregatedStats
   */
  public function getAggregatedStats()
  {
    return $this->aggregatedStats;
  }
  /**
   * Optional. Details of an AWS RDS instance.
   *
   * @param AwsRds $awsRds
   */
  public function setAwsRds(AwsRds $awsRds)
  {
    $this->awsRds = $awsRds;
  }
  /**
   * @return AwsRds
   */
  public function getAwsRds()
  {
    return $this->awsRds;
  }
  /**
   * Optional. The database deployment edition.
   *
   * @param string $edition
   */
  public function setEdition($edition)
  {
    $this->edition = $edition;
  }
  /**
   * @return string
   */
  public function getEdition()
  {
    return $this->edition;
  }
  /**
   * Optional. The database deployment generated ID.
   *
   * @param string $generatedId
   */
  public function setGeneratedId($generatedId)
  {
    $this->generatedId = $generatedId;
  }
  /**
   * @return string
   */
  public function getGeneratedId()
  {
    return $this->generatedId;
  }
  /**
   * Optional. A manual unique ID set by the user.
   *
   * @param string $manualUniqueId
   */
  public function setManualUniqueId($manualUniqueId)
  {
    $this->manualUniqueId = $manualUniqueId;
  }
  /**
   * @return string
   */
  public function getManualUniqueId()
  {
    return $this->manualUniqueId;
  }
  /**
   * Optional. Details of a MYSQL database deployment.
   *
   * @param MysqlDatabaseDeployment $mysql
   */
  public function setMysql(MysqlDatabaseDeployment $mysql)
  {
    $this->mysql = $mysql;
  }
  /**
   * @return MysqlDatabaseDeployment
   */
  public function getMysql()
  {
    return $this->mysql;
  }
  /**
   * Optional. Details of a PostgreSQL database deployment.
   *
   * @param PostgreSqlDatabaseDeployment $postgresql
   */
  public function setPostgresql(PostgreSqlDatabaseDeployment $postgresql)
  {
    $this->postgresql = $postgresql;
  }
  /**
   * @return PostgreSqlDatabaseDeployment
   */
  public function getPostgresql()
  {
    return $this->postgresql;
  }
  /**
   * Optional. Details of a Microsoft SQL Server database deployment.
   *
   * @param SqlServerDatabaseDeployment $sqlServer
   */
  public function setSqlServer(SqlServerDatabaseDeployment $sqlServer)
  {
    $this->sqlServer = $sqlServer;
  }
  /**
   * @return SqlServerDatabaseDeployment
   */
  public function getSqlServer()
  {
    return $this->sqlServer;
  }
  /**
   * Optional. Details of the database deployment topology.
   *
   * @param DatabaseDeploymentTopology $topology
   */
  public function setTopology(DatabaseDeploymentTopology $topology)
  {
    $this->topology = $topology;
  }
  /**
   * @return DatabaseDeploymentTopology
   */
  public function getTopology()
  {
    return $this->topology;
  }
  /**
   * Optional. The database deployment version.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseDeploymentDetails::class, 'Google_Service_MigrationCenterAPI_DatabaseDeploymentDetails');
