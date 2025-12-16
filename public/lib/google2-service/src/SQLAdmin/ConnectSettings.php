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

class ConnectSettings extends \Google\Collection
{
  /**
   * This is an unknown backend type for instance.
   */
  public const BACKEND_TYPE_SQL_BACKEND_TYPE_UNSPECIFIED = 'SQL_BACKEND_TYPE_UNSPECIFIED';
  /**
   * V1 speckle instance.
   *
   * @deprecated
   */
  public const BACKEND_TYPE_FIRST_GEN = 'FIRST_GEN';
  /**
   * V2 speckle instance.
   */
  public const BACKEND_TYPE_SECOND_GEN = 'SECOND_GEN';
  /**
   * On premises instance.
   */
  public const BACKEND_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * This is an unknown database version.
   */
  public const DATABASE_VERSION_SQL_DATABASE_VERSION_UNSPECIFIED = 'SQL_DATABASE_VERSION_UNSPECIFIED';
  /**
   * The database version is MySQL 5.1.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_5_1 = 'MYSQL_5_1';
  /**
   * The database version is MySQL 5.5.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_5_5 = 'MYSQL_5_5';
  /**
   * The database version is MySQL 5.6.
   */
  public const DATABASE_VERSION_MYSQL_5_6 = 'MYSQL_5_6';
  /**
   * The database version is MySQL 5.7.
   */
  public const DATABASE_VERSION_MYSQL_5_7 = 'MYSQL_5_7';
  /**
   * The database version is MySQL 8.
   */
  public const DATABASE_VERSION_MYSQL_8_0 = 'MYSQL_8_0';
  /**
   * The database major version is MySQL 8.0 and the minor version is 18.
   */
  public const DATABASE_VERSION_MYSQL_8_0_18 = 'MYSQL_8_0_18';
  /**
   * The database major version is MySQL 8.0 and the minor version is 26.
   */
  public const DATABASE_VERSION_MYSQL_8_0_26 = 'MYSQL_8_0_26';
  /**
   * The database major version is MySQL 8.0 and the minor version is 27.
   */
  public const DATABASE_VERSION_MYSQL_8_0_27 = 'MYSQL_8_0_27';
  /**
   * The database major version is MySQL 8.0 and the minor version is 28.
   */
  public const DATABASE_VERSION_MYSQL_8_0_28 = 'MYSQL_8_0_28';
  /**
   * The database major version is MySQL 8.0 and the minor version is 29.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_8_0_29 = 'MYSQL_8_0_29';
  /**
   * The database major version is MySQL 8.0 and the minor version is 30.
   */
  public const DATABASE_VERSION_MYSQL_8_0_30 = 'MYSQL_8_0_30';
  /**
   * The database major version is MySQL 8.0 and the minor version is 31.
   */
  public const DATABASE_VERSION_MYSQL_8_0_31 = 'MYSQL_8_0_31';
  /**
   * The database major version is MySQL 8.0 and the minor version is 32.
   */
  public const DATABASE_VERSION_MYSQL_8_0_32 = 'MYSQL_8_0_32';
  /**
   * The database major version is MySQL 8.0 and the minor version is 33.
   */
  public const DATABASE_VERSION_MYSQL_8_0_33 = 'MYSQL_8_0_33';
  /**
   * The database major version is MySQL 8.0 and the minor version is 34.
   */
  public const DATABASE_VERSION_MYSQL_8_0_34 = 'MYSQL_8_0_34';
  /**
   * The database major version is MySQL 8.0 and the minor version is 35.
   */
  public const DATABASE_VERSION_MYSQL_8_0_35 = 'MYSQL_8_0_35';
  /**
   * The database major version is MySQL 8.0 and the minor version is 36.
   */
  public const DATABASE_VERSION_MYSQL_8_0_36 = 'MYSQL_8_0_36';
  /**
   * The database major version is MySQL 8.0 and the minor version is 37.
   */
  public const DATABASE_VERSION_MYSQL_8_0_37 = 'MYSQL_8_0_37';
  /**
   * The database major version is MySQL 8.0 and the minor version is 39.
   */
  public const DATABASE_VERSION_MYSQL_8_0_39 = 'MYSQL_8_0_39';
  /**
   * The database major version is MySQL 8.0 and the minor version is 40.
   */
  public const DATABASE_VERSION_MYSQL_8_0_40 = 'MYSQL_8_0_40';
  /**
   * The database major version is MySQL 8.0 and the minor version is 41.
   */
  public const DATABASE_VERSION_MYSQL_8_0_41 = 'MYSQL_8_0_41';
  /**
   * The database major version is MySQL 8.0 and the minor version is 42.
   */
  public const DATABASE_VERSION_MYSQL_8_0_42 = 'MYSQL_8_0_42';
  /**
   * The database major version is MySQL 8.0 and the minor version is 43.
   */
  public const DATABASE_VERSION_MYSQL_8_0_43 = 'MYSQL_8_0_43';
  /**
   * The database major version is MySQL 8.0 and the minor version is 44.
   */
  public const DATABASE_VERSION_MYSQL_8_0_44 = 'MYSQL_8_0_44';
  /**
   * The database major version is MySQL 8.0 and the minor version is 45.
   */
  public const DATABASE_VERSION_MYSQL_8_0_45 = 'MYSQL_8_0_45';
  /**
   * The database major version is MySQL 8.0 and the minor version is 46.
   */
  public const DATABASE_VERSION_MYSQL_8_0_46 = 'MYSQL_8_0_46';
  /**
   * The database version is MySQL 8.4.
   */
  public const DATABASE_VERSION_MYSQL_8_4 = 'MYSQL_8_4';
  /**
   * The database version is SQL Server 2017 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_STANDARD = 'SQLSERVER_2017_STANDARD';
  /**
   * The database version is SQL Server 2017 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_ENTERPRISE = 'SQLSERVER_2017_ENTERPRISE';
  /**
   * The database version is SQL Server 2017 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_EXPRESS = 'SQLSERVER_2017_EXPRESS';
  /**
   * The database version is SQL Server 2017 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_WEB = 'SQLSERVER_2017_WEB';
  /**
   * The database version is PostgreSQL 9.6.
   */
  public const DATABASE_VERSION_POSTGRES_9_6 = 'POSTGRES_9_6';
  /**
   * The database version is PostgreSQL 10.
   */
  public const DATABASE_VERSION_POSTGRES_10 = 'POSTGRES_10';
  /**
   * The database version is PostgreSQL 11.
   */
  public const DATABASE_VERSION_POSTGRES_11 = 'POSTGRES_11';
  /**
   * The database version is PostgreSQL 12.
   */
  public const DATABASE_VERSION_POSTGRES_12 = 'POSTGRES_12';
  /**
   * The database version is PostgreSQL 13.
   */
  public const DATABASE_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * The database version is PostgreSQL 14.
   */
  public const DATABASE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is PostgreSQL 15.
   */
  public const DATABASE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is PostgreSQL 16.
   */
  public const DATABASE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is PostgreSQL 17.
   */
  public const DATABASE_VERSION_POSTGRES_17 = 'POSTGRES_17';
  /**
   * The database version is PostgreSQL 18.
   */
  public const DATABASE_VERSION_POSTGRES_18 = 'POSTGRES_18';
  /**
   * The database version is SQL Server 2019 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_STANDARD = 'SQLSERVER_2019_STANDARD';
  /**
   * The database version is SQL Server 2019 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_ENTERPRISE = 'SQLSERVER_2019_ENTERPRISE';
  /**
   * The database version is SQL Server 2019 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_EXPRESS = 'SQLSERVER_2019_EXPRESS';
  /**
   * The database version is SQL Server 2019 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_WEB = 'SQLSERVER_2019_WEB';
  /**
   * The database version is SQL Server 2022 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_STANDARD = 'SQLSERVER_2022_STANDARD';
  /**
   * The database version is SQL Server 2022 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_ENTERPRISE = 'SQLSERVER_2022_ENTERPRISE';
  /**
   * The database version is SQL Server 2022 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_EXPRESS = 'SQLSERVER_2022_EXPRESS';
  /**
   * The database version is SQL Server 2022 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_WEB = 'SQLSERVER_2022_WEB';
  /**
   * CA mode is unknown.
   */
  public const SERVER_CA_MODE_CA_MODE_UNSPECIFIED = 'CA_MODE_UNSPECIFIED';
  /**
   * Google-managed self-signed internal CA.
   */
  public const SERVER_CA_MODE_GOOGLE_MANAGED_INTERNAL_CA = 'GOOGLE_MANAGED_INTERNAL_CA';
  /**
   * Google-managed regional CA part of root CA hierarchy hosted on Google
   * Cloud's Certificate Authority Service (CAS).
   */
  public const SERVER_CA_MODE_GOOGLE_MANAGED_CAS_CA = 'GOOGLE_MANAGED_CAS_CA';
  /**
   * Customer-managed CA hosted on Google Cloud's Certificate Authority Service
   * (CAS).
   */
  public const SERVER_CA_MODE_CUSTOMER_MANAGED_CAS_CA = 'CUSTOMER_MANAGED_CAS_CA';
  protected $collection_key = 'nodes';
  /**
   * `SECOND_GEN`: Cloud SQL database instance. `EXTERNAL`: A database server
   * that is not managed by Google. This property is read-only; use the `tier`
   * property in the `settings` object to determine the database type.
   *
   * @var string
   */
  public $backendType;
  /**
   * Custom subject alternative names for the server certificate.
   *
   * @var string[]
   */
  public $customSubjectAlternativeNames;
  /**
   * The database engine type and version. The `databaseVersion` field cannot be
   * changed after instance creation. MySQL instances: `MYSQL_8_0`, `MYSQL_5_7`
   * (default), or `MYSQL_5_6`. PostgreSQL instances: `POSTGRES_9_6`,
   * `POSTGRES_10`, `POSTGRES_11`, `POSTGRES_12` (default), `POSTGRES_13`, or
   * `POSTGRES_14`. SQL Server instances: `SQLSERVER_2017_STANDARD` (default),
   * `SQLSERVER_2017_ENTERPRISE`, `SQLSERVER_2017_EXPRESS`,
   * `SQLSERVER_2017_WEB`, `SQLSERVER_2019_STANDARD`,
   * `SQLSERVER_2019_ENTERPRISE`, `SQLSERVER_2019_EXPRESS`, or
   * `SQLSERVER_2019_WEB`.
   *
   * @var string
   */
  public $databaseVersion;
  /**
   * The dns name of the instance.
   *
   * @var string
   */
  public $dnsName;
  protected $dnsNamesType = DnsNameMapping::class;
  protected $dnsNamesDataType = 'array';
  protected $ipAddressesType = IpMapping::class;
  protected $ipAddressesDataType = 'array';
  /**
   * This is always `sql#connectSettings`.
   *
   * @var string
   */
  public $kind;
  /**
   * Optional. Output only. mdx_protocol_support controls how the client uses
   * metadata exchange when connecting to the instance. The values in the list
   * representing parts of the MDX protocol that are supported by this instance.
   * When the list is empty, the instance does not support MDX, so the client
   * must not send an MDX request. The default is empty.
   *
   * @var string[]
   */
  public $mdxProtocolSupport;
  /**
   * The number of read pool nodes in a read pool.
   *
   * @var int
   */
  public $nodeCount;
  protected $nodesType = ConnectPoolNodeConfig::class;
  protected $nodesDataType = 'array';
  /**
   * Whether PSC connectivity is enabled for this instance.
   *
   * @var bool
   */
  public $pscEnabled;
  /**
   * The cloud region for the instance. For example, `us-central1`, `europe-
   * west1`. The region cannot be changed after instance creation.
   *
   * @var string
   */
  public $region;
  protected $serverCaCertType = SslCert::class;
  protected $serverCaCertDataType = '';
  /**
   * Specify what type of CA is used for the server certificate.
   *
   * @var string
   */
  public $serverCaMode;

  /**
   * `SECOND_GEN`: Cloud SQL database instance. `EXTERNAL`: A database server
   * that is not managed by Google. This property is read-only; use the `tier`
   * property in the `settings` object to determine the database type.
   *
   * Accepted values: SQL_BACKEND_TYPE_UNSPECIFIED, FIRST_GEN, SECOND_GEN,
   * EXTERNAL
   *
   * @param self::BACKEND_TYPE_* $backendType
   */
  public function setBackendType($backendType)
  {
    $this->backendType = $backendType;
  }
  /**
   * @return self::BACKEND_TYPE_*
   */
  public function getBackendType()
  {
    return $this->backendType;
  }
  /**
   * Custom subject alternative names for the server certificate.
   *
   * @param string[] $customSubjectAlternativeNames
   */
  public function setCustomSubjectAlternativeNames($customSubjectAlternativeNames)
  {
    $this->customSubjectAlternativeNames = $customSubjectAlternativeNames;
  }
  /**
   * @return string[]
   */
  public function getCustomSubjectAlternativeNames()
  {
    return $this->customSubjectAlternativeNames;
  }
  /**
   * The database engine type and version. The `databaseVersion` field cannot be
   * changed after instance creation. MySQL instances: `MYSQL_8_0`, `MYSQL_5_7`
   * (default), or `MYSQL_5_6`. PostgreSQL instances: `POSTGRES_9_6`,
   * `POSTGRES_10`, `POSTGRES_11`, `POSTGRES_12` (default), `POSTGRES_13`, or
   * `POSTGRES_14`. SQL Server instances: `SQLSERVER_2017_STANDARD` (default),
   * `SQLSERVER_2017_ENTERPRISE`, `SQLSERVER_2017_EXPRESS`,
   * `SQLSERVER_2017_WEB`, `SQLSERVER_2019_STANDARD`,
   * `SQLSERVER_2019_ENTERPRISE`, `SQLSERVER_2019_EXPRESS`, or
   * `SQLSERVER_2019_WEB`.
   *
   * Accepted values: SQL_DATABASE_VERSION_UNSPECIFIED, MYSQL_5_1, MYSQL_5_5,
   * MYSQL_5_6, MYSQL_5_7, MYSQL_8_0, MYSQL_8_0_18, MYSQL_8_0_26, MYSQL_8_0_27,
   * MYSQL_8_0_28, MYSQL_8_0_29, MYSQL_8_0_30, MYSQL_8_0_31, MYSQL_8_0_32,
   * MYSQL_8_0_33, MYSQL_8_0_34, MYSQL_8_0_35, MYSQL_8_0_36, MYSQL_8_0_37,
   * MYSQL_8_0_39, MYSQL_8_0_40, MYSQL_8_0_41, MYSQL_8_0_42, MYSQL_8_0_43,
   * MYSQL_8_0_44, MYSQL_8_0_45, MYSQL_8_0_46, MYSQL_8_4,
   * SQLSERVER_2017_STANDARD, SQLSERVER_2017_ENTERPRISE, SQLSERVER_2017_EXPRESS,
   * SQLSERVER_2017_WEB, POSTGRES_9_6, POSTGRES_10, POSTGRES_11, POSTGRES_12,
   * POSTGRES_13, POSTGRES_14, POSTGRES_15, POSTGRES_16, POSTGRES_17,
   * POSTGRES_18, SQLSERVER_2019_STANDARD, SQLSERVER_2019_ENTERPRISE,
   * SQLSERVER_2019_EXPRESS, SQLSERVER_2019_WEB, SQLSERVER_2022_STANDARD,
   * SQLSERVER_2022_ENTERPRISE, SQLSERVER_2022_EXPRESS, SQLSERVER_2022_WEB
   *
   * @param self::DATABASE_VERSION_* $databaseVersion
   */
  public function setDatabaseVersion($databaseVersion)
  {
    $this->databaseVersion = $databaseVersion;
  }
  /**
   * @return self::DATABASE_VERSION_*
   */
  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }
  /**
   * The dns name of the instance.
   *
   * @param string $dnsName
   */
  public function setDnsName($dnsName)
  {
    $this->dnsName = $dnsName;
  }
  /**
   * @return string
   */
  public function getDnsName()
  {
    return $this->dnsName;
  }
  /**
   * Output only. The list of DNS names used by this instance.
   *
   * @param DnsNameMapping[] $dnsNames
   */
  public function setDnsNames($dnsNames)
  {
    $this->dnsNames = $dnsNames;
  }
  /**
   * @return DnsNameMapping[]
   */
  public function getDnsNames()
  {
    return $this->dnsNames;
  }
  /**
   * The assigned IP addresses for the instance.
   *
   * @param IpMapping[] $ipAddresses
   */
  public function setIpAddresses($ipAddresses)
  {
    $this->ipAddresses = $ipAddresses;
  }
  /**
   * @return IpMapping[]
   */
  public function getIpAddresses()
  {
    return $this->ipAddresses;
  }
  /**
   * This is always `sql#connectSettings`.
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
   * Optional. Output only. mdx_protocol_support controls how the client uses
   * metadata exchange when connecting to the instance. The values in the list
   * representing parts of the MDX protocol that are supported by this instance.
   * When the list is empty, the instance does not support MDX, so the client
   * must not send an MDX request. The default is empty.
   *
   * @param string[] $mdxProtocolSupport
   */
  public function setMdxProtocolSupport($mdxProtocolSupport)
  {
    $this->mdxProtocolSupport = $mdxProtocolSupport;
  }
  /**
   * @return string[]
   */
  public function getMdxProtocolSupport()
  {
    return $this->mdxProtocolSupport;
  }
  /**
   * The number of read pool nodes in a read pool.
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * Output only. Entries containing information about each read pool node of
   * the read pool.
   *
   * @param ConnectPoolNodeConfig[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return ConnectPoolNodeConfig[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * Whether PSC connectivity is enabled for this instance.
   *
   * @param bool $pscEnabled
   */
  public function setPscEnabled($pscEnabled)
  {
    $this->pscEnabled = $pscEnabled;
  }
  /**
   * @return bool
   */
  public function getPscEnabled()
  {
    return $this->pscEnabled;
  }
  /**
   * The cloud region for the instance. For example, `us-central1`, `europe-
   * west1`. The region cannot be changed after instance creation.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * SSL configuration.
   *
   * @param SslCert $serverCaCert
   */
  public function setServerCaCert(SslCert $serverCaCert)
  {
    $this->serverCaCert = $serverCaCert;
  }
  /**
   * @return SslCert
   */
  public function getServerCaCert()
  {
    return $this->serverCaCert;
  }
  /**
   * Specify what type of CA is used for the server certificate.
   *
   * Accepted values: CA_MODE_UNSPECIFIED, GOOGLE_MANAGED_INTERNAL_CA,
   * GOOGLE_MANAGED_CAS_CA, CUSTOMER_MANAGED_CAS_CA
   *
   * @param self::SERVER_CA_MODE_* $serverCaMode
   */
  public function setServerCaMode($serverCaMode)
  {
    $this->serverCaMode = $serverCaMode;
  }
  /**
   * @return self::SERVER_CA_MODE_*
   */
  public function getServerCaMode()
  {
    return $this->serverCaMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectSettings::class, 'Google_Service_SQLAdmin_ConnectSettings');
