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

class ConnectionProfile extends \Google\Model
{
  /**
   * Use this value for on-premise source database instances and ORACLE.
   */
  public const PROVIDER_DATABASE_PROVIDER_UNSPECIFIED = 'DATABASE_PROVIDER_UNSPECIFIED';
  /**
   * Cloud SQL is the source instance provider.
   */
  public const PROVIDER_CLOUDSQL = 'CLOUDSQL';
  /**
   * Amazon RDS is the source instance provider.
   */
  public const PROVIDER_RDS = 'RDS';
  /**
   * Amazon Aurora is the source instance provider.
   */
  public const PROVIDER_AURORA = 'AURORA';
  /**
   * AlloyDB for PostgreSQL is the source instance provider.
   */
  public const PROVIDER_ALLOYDB = 'ALLOYDB';
  /**
   * Microsoft Azure Database for MySQL/PostgreSQL.
   */
  public const PROVIDER_AZURE_DATABASE = 'AZURE_DATABASE';
  /**
   * The role is unspecified.
   */
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * The role is source.
   */
  public const ROLE_SOURCE = 'SOURCE';
  /**
   * The role is destination.
   */
  public const ROLE_DESTINATION = 'DESTINATION';
  /**
   * The state of the connection profile is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The connection profile is in draft mode and fully editable.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The connection profile is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The connection profile is ready.
   */
  public const STATE_READY = 'READY';
  /**
   * The connection profile is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The connection profile is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The connection profile has been deleted.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The last action on the connection profile failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $alloydbType = AlloyDbConnectionProfile::class;
  protected $alloydbDataType = '';
  protected $cloudsqlType = CloudSqlConnectionProfile::class;
  protected $cloudsqlDataType = '';
  /**
   * Output only. The timestamp when the resource was created. A timestamp in
   * RFC3339 UTC "Zulu" format, accurate to nanoseconds. Example:
   * "2014-10-02T15:01:23.045123456Z".
   *
   * @var string
   */
  public $createTime;
  /**
   * The connection profile display name.
   *
   * @var string
   */
  public $displayName;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * The resource labels for connection profile to use to annotate any related
   * underlying resources such as Compute Engine VMs. An object containing a
   * list of "key": "value" pairs. Example: `{ "name": "wrench", "mass":
   * "1.3kg", "count": "3" }`.
   *
   * @var string[]
   */
  public $labels;
  protected $mysqlType = MySqlConnectionProfile::class;
  protected $mysqlDataType = '';
  /**
   * The name of this connection profile resource in the form of projects/{proje
   * ct}/locations/{location}/connectionProfiles/{connectionProfile}.
   *
   * @var string
   */
  public $name;
  protected $oracleType = OracleConnectionProfile::class;
  protected $oracleDataType = '';
  protected $postgresqlType = PostgreSqlConnectionProfile::class;
  protected $postgresqlDataType = '';
  /**
   * The database provider.
   *
   * @var string
   */
  public $provider;
  /**
   * Optional. The connection profile role.
   *
   * @var string
   */
  public $role;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $sqlserverType = SqlServerConnectionProfile::class;
  protected $sqlserverDataType = '';
  /**
   * The current connection profile state (e.g. DRAFT, READY, or FAILED).
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The timestamp when the resource was last updated. A timestamp
   * in RFC3339 UTC "Zulu" format, accurate to nanoseconds. Example:
   * "2014-10-02T15:01:23.045123456Z".
   *
   * @var string
   */
  public $updateTime;

  /**
   * An AlloyDB cluster connection profile.
   *
   * @param AlloyDbConnectionProfile $alloydb
   */
  public function setAlloydb(AlloyDbConnectionProfile $alloydb)
  {
    $this->alloydb = $alloydb;
  }
  /**
   * @return AlloyDbConnectionProfile
   */
  public function getAlloydb()
  {
    return $this->alloydb;
  }
  /**
   * A CloudSQL database connection profile.
   *
   * @param CloudSqlConnectionProfile $cloudsql
   */
  public function setCloudsql(CloudSqlConnectionProfile $cloudsql)
  {
    $this->cloudsql = $cloudsql;
  }
  /**
   * @return CloudSqlConnectionProfile
   */
  public function getCloudsql()
  {
    return $this->cloudsql;
  }
  /**
   * Output only. The timestamp when the resource was created. A timestamp in
   * RFC3339 UTC "Zulu" format, accurate to nanoseconds. Example:
   * "2014-10-02T15:01:23.045123456Z".
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The connection profile display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The error details in case of state FAILED.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The resource labels for connection profile to use to annotate any related
   * underlying resources such as Compute Engine VMs. An object containing a
   * list of "key": "value" pairs. Example: `{ "name": "wrench", "mass":
   * "1.3kg", "count": "3" }`.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * A MySQL database connection profile.
   *
   * @param MySqlConnectionProfile $mysql
   */
  public function setMysql(MySqlConnectionProfile $mysql)
  {
    $this->mysql = $mysql;
  }
  /**
   * @return MySqlConnectionProfile
   */
  public function getMysql()
  {
    return $this->mysql;
  }
  /**
   * The name of this connection profile resource in the form of projects/{proje
   * ct}/locations/{location}/connectionProfiles/{connectionProfile}.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * An Oracle database connection profile.
   *
   * @param OracleConnectionProfile $oracle
   */
  public function setOracle(OracleConnectionProfile $oracle)
  {
    $this->oracle = $oracle;
  }
  /**
   * @return OracleConnectionProfile
   */
  public function getOracle()
  {
    return $this->oracle;
  }
  /**
   * A PostgreSQL database connection profile.
   *
   * @param PostgreSqlConnectionProfile $postgresql
   */
  public function setPostgresql(PostgreSqlConnectionProfile $postgresql)
  {
    $this->postgresql = $postgresql;
  }
  /**
   * @return PostgreSqlConnectionProfile
   */
  public function getPostgresql()
  {
    return $this->postgresql;
  }
  /**
   * The database provider.
   *
   * Accepted values: DATABASE_PROVIDER_UNSPECIFIED, CLOUDSQL, RDS, AURORA,
   * ALLOYDB, AZURE_DATABASE
   *
   * @param self::PROVIDER_* $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return self::PROVIDER_*
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Optional. The connection profile role.
   *
   * Accepted values: ROLE_UNSPECIFIED, SOURCE, DESTINATION
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Connection profile for a SQL Server data source.
   *
   * @param SqlServerConnectionProfile $sqlserver
   */
  public function setSqlserver(SqlServerConnectionProfile $sqlserver)
  {
    $this->sqlserver = $sqlserver;
  }
  /**
   * @return SqlServerConnectionProfile
   */
  public function getSqlserver()
  {
    return $this->sqlserver;
  }
  /**
   * The current connection profile state (e.g. DRAFT, READY, or FAILED).
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, CREATING, READY, UPDATING,
   * DELETING, DELETED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The timestamp when the resource was last updated. A timestamp
   * in RFC3339 UTC "Zulu" format, accurate to nanoseconds. Example:
   * "2014-10-02T15:01:23.045123456Z".
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectionProfile::class, 'Google_Service_DatabaseMigrationService_ConnectionProfile');
