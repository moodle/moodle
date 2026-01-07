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

class PreCheckMajorVersionUpgradeContext extends \Google\Collection
{
  /**
   * This is an unknown database version.
   */
  public const TARGET_DATABASE_VERSION_SQL_DATABASE_VERSION_UNSPECIFIED = 'SQL_DATABASE_VERSION_UNSPECIFIED';
  /**
   * The database version is MySQL 5.1.
   *
   * @deprecated
   */
  public const TARGET_DATABASE_VERSION_MYSQL_5_1 = 'MYSQL_5_1';
  /**
   * The database version is MySQL 5.5.
   *
   * @deprecated
   */
  public const TARGET_DATABASE_VERSION_MYSQL_5_5 = 'MYSQL_5_5';
  /**
   * The database version is MySQL 5.6.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_5_6 = 'MYSQL_5_6';
  /**
   * The database version is MySQL 5.7.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_5_7 = 'MYSQL_5_7';
  /**
   * The database version is MySQL 8.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0 = 'MYSQL_8_0';
  /**
   * The database major version is MySQL 8.0 and the minor version is 18.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_18 = 'MYSQL_8_0_18';
  /**
   * The database major version is MySQL 8.0 and the minor version is 26.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_26 = 'MYSQL_8_0_26';
  /**
   * The database major version is MySQL 8.0 and the minor version is 27.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_27 = 'MYSQL_8_0_27';
  /**
   * The database major version is MySQL 8.0 and the minor version is 28.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_28 = 'MYSQL_8_0_28';
  /**
   * The database major version is MySQL 8.0 and the minor version is 29.
   *
   * @deprecated
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_29 = 'MYSQL_8_0_29';
  /**
   * The database major version is MySQL 8.0 and the minor version is 30.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_30 = 'MYSQL_8_0_30';
  /**
   * The database major version is MySQL 8.0 and the minor version is 31.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_31 = 'MYSQL_8_0_31';
  /**
   * The database major version is MySQL 8.0 and the minor version is 32.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_32 = 'MYSQL_8_0_32';
  /**
   * The database major version is MySQL 8.0 and the minor version is 33.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_33 = 'MYSQL_8_0_33';
  /**
   * The database major version is MySQL 8.0 and the minor version is 34.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_34 = 'MYSQL_8_0_34';
  /**
   * The database major version is MySQL 8.0 and the minor version is 35.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_35 = 'MYSQL_8_0_35';
  /**
   * The database major version is MySQL 8.0 and the minor version is 36.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_36 = 'MYSQL_8_0_36';
  /**
   * The database major version is MySQL 8.0 and the minor version is 37.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_37 = 'MYSQL_8_0_37';
  /**
   * The database major version is MySQL 8.0 and the minor version is 39.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_39 = 'MYSQL_8_0_39';
  /**
   * The database major version is MySQL 8.0 and the minor version is 40.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_40 = 'MYSQL_8_0_40';
  /**
   * The database major version is MySQL 8.0 and the minor version is 41.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_41 = 'MYSQL_8_0_41';
  /**
   * The database major version is MySQL 8.0 and the minor version is 42.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_42 = 'MYSQL_8_0_42';
  /**
   * The database major version is MySQL 8.0 and the minor version is 43.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_43 = 'MYSQL_8_0_43';
  /**
   * The database major version is MySQL 8.0 and the minor version is 44.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_44 = 'MYSQL_8_0_44';
  /**
   * The database major version is MySQL 8.0 and the minor version is 45.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_45 = 'MYSQL_8_0_45';
  /**
   * The database major version is MySQL 8.0 and the minor version is 46.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_0_46 = 'MYSQL_8_0_46';
  /**
   * The database version is MySQL 8.4.
   */
  public const TARGET_DATABASE_VERSION_MYSQL_8_4 = 'MYSQL_8_4';
  /**
   * The database version is SQL Server 2017 Standard.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2017_STANDARD = 'SQLSERVER_2017_STANDARD';
  /**
   * The database version is SQL Server 2017 Enterprise.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2017_ENTERPRISE = 'SQLSERVER_2017_ENTERPRISE';
  /**
   * The database version is SQL Server 2017 Express.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2017_EXPRESS = 'SQLSERVER_2017_EXPRESS';
  /**
   * The database version is SQL Server 2017 Web.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2017_WEB = 'SQLSERVER_2017_WEB';
  /**
   * The database version is PostgreSQL 9.6.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_9_6 = 'POSTGRES_9_6';
  /**
   * The database version is PostgreSQL 10.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_10 = 'POSTGRES_10';
  /**
   * The database version is PostgreSQL 11.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_11 = 'POSTGRES_11';
  /**
   * The database version is PostgreSQL 12.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_12 = 'POSTGRES_12';
  /**
   * The database version is PostgreSQL 13.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * The database version is PostgreSQL 14.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is PostgreSQL 15.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is PostgreSQL 16.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is PostgreSQL 17.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_17 = 'POSTGRES_17';
  /**
   * The database version is PostgreSQL 18.
   */
  public const TARGET_DATABASE_VERSION_POSTGRES_18 = 'POSTGRES_18';
  /**
   * The database version is SQL Server 2019 Standard.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2019_STANDARD = 'SQLSERVER_2019_STANDARD';
  /**
   * The database version is SQL Server 2019 Enterprise.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2019_ENTERPRISE = 'SQLSERVER_2019_ENTERPRISE';
  /**
   * The database version is SQL Server 2019 Express.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2019_EXPRESS = 'SQLSERVER_2019_EXPRESS';
  /**
   * The database version is SQL Server 2019 Web.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2019_WEB = 'SQLSERVER_2019_WEB';
  /**
   * The database version is SQL Server 2022 Standard.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2022_STANDARD = 'SQLSERVER_2022_STANDARD';
  /**
   * The database version is SQL Server 2022 Enterprise.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2022_ENTERPRISE = 'SQLSERVER_2022_ENTERPRISE';
  /**
   * The database version is SQL Server 2022 Express.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2022_EXPRESS = 'SQLSERVER_2022_EXPRESS';
  /**
   * The database version is SQL Server 2022 Web.
   */
  public const TARGET_DATABASE_VERSION_SQLSERVER_2022_WEB = 'SQLSERVER_2022_WEB';
  protected $collection_key = 'preCheckResponse';
  /**
   * Optional. This is always `sql#preCheckMajorVersionUpgradeContext`.
   *
   * @var string
   */
  public $kind;
  protected $preCheckResponseType = PreCheckResponse::class;
  protected $preCheckResponseDataType = 'array';
  /**
   * Required. The target database version to upgrade to.
   *
   * @var string
   */
  public $targetDatabaseVersion;

  /**
   * Optional. This is always `sql#preCheckMajorVersionUpgradeContext`.
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
   * Output only. The responses from the precheck operation.
   *
   * @param PreCheckResponse[] $preCheckResponse
   */
  public function setPreCheckResponse($preCheckResponse)
  {
    $this->preCheckResponse = $preCheckResponse;
  }
  /**
   * @return PreCheckResponse[]
   */
  public function getPreCheckResponse()
  {
    return $this->preCheckResponse;
  }
  /**
   * Required. The target database version to upgrade to.
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
   * @param self::TARGET_DATABASE_VERSION_* $targetDatabaseVersion
   */
  public function setTargetDatabaseVersion($targetDatabaseVersion)
  {
    $this->targetDatabaseVersion = $targetDatabaseVersion;
  }
  /**
   * @return self::TARGET_DATABASE_VERSION_*
   */
  public function getTargetDatabaseVersion()
  {
    return $this->targetDatabaseVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreCheckMajorVersionUpgradeContext::class, 'Google_Service_SQLAdmin_PreCheckMajorVersionUpgradeContext');
