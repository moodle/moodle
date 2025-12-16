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

class ConnectionProfile extends \Google\Model
{
  protected $bigqueryProfileType = BigQueryProfile::class;
  protected $bigqueryProfileDataType = '';
  /**
   * Output only. The create time of the resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Display name.
   *
   * @var string
   */
  public $displayName;
  protected $forwardSshConnectivityType = ForwardSshTunnelConnectivity::class;
  protected $forwardSshConnectivityDataType = '';
  protected $gcsProfileType = GcsProfile::class;
  protected $gcsProfileDataType = '';
  /**
   * Labels.
   *
   * @var string[]
   */
  public $labels;
  protected $mongodbProfileType = MongodbProfile::class;
  protected $mongodbProfileDataType = '';
  protected $mysqlProfileType = MysqlProfile::class;
  protected $mysqlProfileDataType = '';
  /**
   * Output only. Identifier. The resource's name.
   *
   * @var string
   */
  public $name;
  protected $oracleProfileType = OracleProfile::class;
  protected $oracleProfileDataType = '';
  protected $postgresqlProfileType = PostgresqlProfile::class;
  protected $postgresqlProfileDataType = '';
  protected $privateConnectivityType = PrivateConnectivity::class;
  protected $privateConnectivityDataType = '';
  protected $salesforceProfileType = SalesforceProfile::class;
  protected $salesforceProfileDataType = '';
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
  protected $sqlServerProfileType = SqlServerProfile::class;
  protected $sqlServerProfileDataType = '';
  protected $staticServiceIpConnectivityType = StaticServiceIpConnectivity::class;
  protected $staticServiceIpConnectivityDataType = '';
  /**
   * Output only. The update time of the resource.
   *
   * @var string
   */
  public $updateTime;

  /**
   * BigQuery Connection Profile configuration.
   *
   * @param BigQueryProfile $bigqueryProfile
   */
  public function setBigqueryProfile(BigQueryProfile $bigqueryProfile)
  {
    $this->bigqueryProfile = $bigqueryProfile;
  }
  /**
   * @return BigQueryProfile
   */
  public function getBigqueryProfile()
  {
    return $this->bigqueryProfile;
  }
  /**
   * Output only. The create time of the resource.
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
   * Required. Display name.
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
   * Forward SSH tunnel connectivity.
   *
   * @param ForwardSshTunnelConnectivity $forwardSshConnectivity
   */
  public function setForwardSshConnectivity(ForwardSshTunnelConnectivity $forwardSshConnectivity)
  {
    $this->forwardSshConnectivity = $forwardSshConnectivity;
  }
  /**
   * @return ForwardSshTunnelConnectivity
   */
  public function getForwardSshConnectivity()
  {
    return $this->forwardSshConnectivity;
  }
  /**
   * Cloud Storage ConnectionProfile configuration.
   *
   * @param GcsProfile $gcsProfile
   */
  public function setGcsProfile(GcsProfile $gcsProfile)
  {
    $this->gcsProfile = $gcsProfile;
  }
  /**
   * @return GcsProfile
   */
  public function getGcsProfile()
  {
    return $this->gcsProfile;
  }
  /**
   * Labels.
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
   * MongoDB Connection Profile configuration.
   *
   * @param MongodbProfile $mongodbProfile
   */
  public function setMongodbProfile(MongodbProfile $mongodbProfile)
  {
    $this->mongodbProfile = $mongodbProfile;
  }
  /**
   * @return MongodbProfile
   */
  public function getMongodbProfile()
  {
    return $this->mongodbProfile;
  }
  /**
   * MySQL ConnectionProfile configuration.
   *
   * @param MysqlProfile $mysqlProfile
   */
  public function setMysqlProfile(MysqlProfile $mysqlProfile)
  {
    $this->mysqlProfile = $mysqlProfile;
  }
  /**
   * @return MysqlProfile
   */
  public function getMysqlProfile()
  {
    return $this->mysqlProfile;
  }
  /**
   * Output only. Identifier. The resource's name.
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
   * Oracle ConnectionProfile configuration.
   *
   * @param OracleProfile $oracleProfile
   */
  public function setOracleProfile(OracleProfile $oracleProfile)
  {
    $this->oracleProfile = $oracleProfile;
  }
  /**
   * @return OracleProfile
   */
  public function getOracleProfile()
  {
    return $this->oracleProfile;
  }
  /**
   * PostgreSQL Connection Profile configuration.
   *
   * @param PostgresqlProfile $postgresqlProfile
   */
  public function setPostgresqlProfile(PostgresqlProfile $postgresqlProfile)
  {
    $this->postgresqlProfile = $postgresqlProfile;
  }
  /**
   * @return PostgresqlProfile
   */
  public function getPostgresqlProfile()
  {
    return $this->postgresqlProfile;
  }
  /**
   * Private connectivity.
   *
   * @param PrivateConnectivity $privateConnectivity
   */
  public function setPrivateConnectivity(PrivateConnectivity $privateConnectivity)
  {
    $this->privateConnectivity = $privateConnectivity;
  }
  /**
   * @return PrivateConnectivity
   */
  public function getPrivateConnectivity()
  {
    return $this->privateConnectivity;
  }
  /**
   * Salesforce Connection Profile configuration.
   *
   * @param SalesforceProfile $salesforceProfile
   */
  public function setSalesforceProfile(SalesforceProfile $salesforceProfile)
  {
    $this->salesforceProfile = $salesforceProfile;
  }
  /**
   * @return SalesforceProfile
   */
  public function getSalesforceProfile()
  {
    return $this->salesforceProfile;
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
   * SQLServer Connection Profile configuration.
   *
   * @param SqlServerProfile $sqlServerProfile
   */
  public function setSqlServerProfile(SqlServerProfile $sqlServerProfile)
  {
    $this->sqlServerProfile = $sqlServerProfile;
  }
  /**
   * @return SqlServerProfile
   */
  public function getSqlServerProfile()
  {
    return $this->sqlServerProfile;
  }
  /**
   * Static Service IP connectivity.
   *
   * @param StaticServiceIpConnectivity $staticServiceIpConnectivity
   */
  public function setStaticServiceIpConnectivity(StaticServiceIpConnectivity $staticServiceIpConnectivity)
  {
    $this->staticServiceIpConnectivity = $staticServiceIpConnectivity;
  }
  /**
   * @return StaticServiceIpConnectivity
   */
  public function getStaticServiceIpConnectivity()
  {
    return $this->staticServiceIpConnectivity;
  }
  /**
   * Output only. The update time of the resource.
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
class_alias(ConnectionProfile::class, 'Google_Service_Datastream_ConnectionProfile');
