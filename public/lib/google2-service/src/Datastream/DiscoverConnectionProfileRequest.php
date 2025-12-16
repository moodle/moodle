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

class DiscoverConnectionProfileRequest extends \Google\Model
{
  protected $connectionProfileType = ConnectionProfile::class;
  protected $connectionProfileDataType = '';
  /**
   * A reference to an existing connection profile.
   *
   * @var string
   */
  public $connectionProfileName;
  /**
   * Whether to retrieve the full hierarchy of data objects (TRUE) or only the
   * current level (FALSE).
   *
   * @var bool
   */
  public $fullHierarchy;
  /**
   * The number of hierarchy levels below the current level to be retrieved.
   *
   * @var int
   */
  public $hierarchyDepth;
  protected $mongodbClusterType = MongodbCluster::class;
  protected $mongodbClusterDataType = '';
  protected $mysqlRdbmsType = MysqlRdbms::class;
  protected $mysqlRdbmsDataType = '';
  protected $oracleRdbmsType = OracleRdbms::class;
  protected $oracleRdbmsDataType = '';
  protected $postgresqlRdbmsType = PostgresqlRdbms::class;
  protected $postgresqlRdbmsDataType = '';
  protected $salesforceOrgType = SalesforceOrg::class;
  protected $salesforceOrgDataType = '';
  protected $sqlServerRdbmsType = SqlServerRdbms::class;
  protected $sqlServerRdbmsDataType = '';

  /**
   * An ad-hoc connection profile configuration.
   *
   * @param ConnectionProfile $connectionProfile
   */
  public function setConnectionProfile(ConnectionProfile $connectionProfile)
  {
    $this->connectionProfile = $connectionProfile;
  }
  /**
   * @return ConnectionProfile
   */
  public function getConnectionProfile()
  {
    return $this->connectionProfile;
  }
  /**
   * A reference to an existing connection profile.
   *
   * @param string $connectionProfileName
   */
  public function setConnectionProfileName($connectionProfileName)
  {
    $this->connectionProfileName = $connectionProfileName;
  }
  /**
   * @return string
   */
  public function getConnectionProfileName()
  {
    return $this->connectionProfileName;
  }
  /**
   * Whether to retrieve the full hierarchy of data objects (TRUE) or only the
   * current level (FALSE).
   *
   * @param bool $fullHierarchy
   */
  public function setFullHierarchy($fullHierarchy)
  {
    $this->fullHierarchy = $fullHierarchy;
  }
  /**
   * @return bool
   */
  public function getFullHierarchy()
  {
    return $this->fullHierarchy;
  }
  /**
   * The number of hierarchy levels below the current level to be retrieved.
   *
   * @param int $hierarchyDepth
   */
  public function setHierarchyDepth($hierarchyDepth)
  {
    $this->hierarchyDepth = $hierarchyDepth;
  }
  /**
   * @return int
   */
  public function getHierarchyDepth()
  {
    return $this->hierarchyDepth;
  }
  /**
   * MongoDB cluster to enrich with child data objects and metadata.
   *
   * @param MongodbCluster $mongodbCluster
   */
  public function setMongodbCluster(MongodbCluster $mongodbCluster)
  {
    $this->mongodbCluster = $mongodbCluster;
  }
  /**
   * @return MongodbCluster
   */
  public function getMongodbCluster()
  {
    return $this->mongodbCluster;
  }
  /**
   * MySQL RDBMS to enrich with child data objects and metadata.
   *
   * @param MysqlRdbms $mysqlRdbms
   */
  public function setMysqlRdbms(MysqlRdbms $mysqlRdbms)
  {
    $this->mysqlRdbms = $mysqlRdbms;
  }
  /**
   * @return MysqlRdbms
   */
  public function getMysqlRdbms()
  {
    return $this->mysqlRdbms;
  }
  /**
   * Oracle RDBMS to enrich with child data objects and metadata.
   *
   * @param OracleRdbms $oracleRdbms
   */
  public function setOracleRdbms(OracleRdbms $oracleRdbms)
  {
    $this->oracleRdbms = $oracleRdbms;
  }
  /**
   * @return OracleRdbms
   */
  public function getOracleRdbms()
  {
    return $this->oracleRdbms;
  }
  /**
   * PostgreSQL RDBMS to enrich with child data objects and metadata.
   *
   * @param PostgresqlRdbms $postgresqlRdbms
   */
  public function setPostgresqlRdbms(PostgresqlRdbms $postgresqlRdbms)
  {
    $this->postgresqlRdbms = $postgresqlRdbms;
  }
  /**
   * @return PostgresqlRdbms
   */
  public function getPostgresqlRdbms()
  {
    return $this->postgresqlRdbms;
  }
  /**
   * Salesforce organization to enrich with child data objects and metadata.
   *
   * @param SalesforceOrg $salesforceOrg
   */
  public function setSalesforceOrg(SalesforceOrg $salesforceOrg)
  {
    $this->salesforceOrg = $salesforceOrg;
  }
  /**
   * @return SalesforceOrg
   */
  public function getSalesforceOrg()
  {
    return $this->salesforceOrg;
  }
  /**
   * SQLServer RDBMS to enrich with child data objects and metadata.
   *
   * @param SqlServerRdbms $sqlServerRdbms
   */
  public function setSqlServerRdbms(SqlServerRdbms $sqlServerRdbms)
  {
    $this->sqlServerRdbms = $sqlServerRdbms;
  }
  /**
   * @return SqlServerRdbms
   */
  public function getSqlServerRdbms()
  {
    return $this->sqlServerRdbms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoverConnectionProfileRequest::class, 'Google_Service_Datastream_DiscoverConnectionProfileRequest');
