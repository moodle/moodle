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

namespace Google\Service\WorkloadManager;

class SapDiscoveryComponentDatabaseProperties extends \Google\Model
{
  /**
   * Unspecified database type.
   */
  public const DATABASE_TYPE_DATABASE_TYPE_UNSPECIFIED = 'DATABASE_TYPE_UNSPECIFIED';
  /**
   * SAP HANA
   */
  public const DATABASE_TYPE_HANA = 'HANA';
  /**
   * SAP MAX_DB
   */
  public const DATABASE_TYPE_MAX_DB = 'MAX_DB';
  /**
   * IBM DB2
   */
  public const DATABASE_TYPE_DB2 = 'DB2';
  /**
   * Oracle Database
   */
  public const DATABASE_TYPE_ORACLE = 'ORACLE';
  /**
   * Microsoft SQL Server
   */
  public const DATABASE_TYPE_SQLSERVER = 'SQLSERVER';
  /**
   * SAP Sybase ASE
   */
  public const DATABASE_TYPE_ASE = 'ASE';
  /**
   * Optional. SID of the system database.
   *
   * @var string
   */
  public $databaseSid;
  /**
   * Required. Type of the database. HANA, DB2, etc.
   *
   * @var string
   */
  public $databaseType;
  /**
   * Optional. The version of the database software running in the system.
   *
   * @var string
   */
  public $databaseVersion;
  /**
   * Optional. Instance number of the SAP instance.
   *
   * @var string
   */
  public $instanceNumber;
  /**
   * Optional. Landscape ID from the HANA nameserver.
   *
   * @var string
   */
  public $landscapeId;
  /**
   * Required. URI of the recognized primary instance of the database.
   *
   * @var string
   */
  public $primaryInstanceUri;
  /**
   * Optional. URI of the recognized shared NFS of the database. May be empty if
   * the database has only a single node.
   *
   * @var string
   */
  public $sharedNfsUri;

  /**
   * Optional. SID of the system database.
   *
   * @param string $databaseSid
   */
  public function setDatabaseSid($databaseSid)
  {
    $this->databaseSid = $databaseSid;
  }
  /**
   * @return string
   */
  public function getDatabaseSid()
  {
    return $this->databaseSid;
  }
  /**
   * Required. Type of the database. HANA, DB2, etc.
   *
   * Accepted values: DATABASE_TYPE_UNSPECIFIED, HANA, MAX_DB, DB2, ORACLE,
   * SQLSERVER, ASE
   *
   * @param self::DATABASE_TYPE_* $databaseType
   */
  public function setDatabaseType($databaseType)
  {
    $this->databaseType = $databaseType;
  }
  /**
   * @return self::DATABASE_TYPE_*
   */
  public function getDatabaseType()
  {
    return $this->databaseType;
  }
  /**
   * Optional. The version of the database software running in the system.
   *
   * @param string $databaseVersion
   */
  public function setDatabaseVersion($databaseVersion)
  {
    $this->databaseVersion = $databaseVersion;
  }
  /**
   * @return string
   */
  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }
  /**
   * Optional. Instance number of the SAP instance.
   *
   * @param string $instanceNumber
   */
  public function setInstanceNumber($instanceNumber)
  {
    $this->instanceNumber = $instanceNumber;
  }
  /**
   * @return string
   */
  public function getInstanceNumber()
  {
    return $this->instanceNumber;
  }
  /**
   * Optional. Landscape ID from the HANA nameserver.
   *
   * @param string $landscapeId
   */
  public function setLandscapeId($landscapeId)
  {
    $this->landscapeId = $landscapeId;
  }
  /**
   * @return string
   */
  public function getLandscapeId()
  {
    return $this->landscapeId;
  }
  /**
   * Required. URI of the recognized primary instance of the database.
   *
   * @param string $primaryInstanceUri
   */
  public function setPrimaryInstanceUri($primaryInstanceUri)
  {
    $this->primaryInstanceUri = $primaryInstanceUri;
  }
  /**
   * @return string
   */
  public function getPrimaryInstanceUri()
  {
    return $this->primaryInstanceUri;
  }
  /**
   * Optional. URI of the recognized shared NFS of the database. May be empty if
   * the database has only a single node.
   *
   * @param string $sharedNfsUri
   */
  public function setSharedNfsUri($sharedNfsUri)
  {
    $this->sharedNfsUri = $sharedNfsUri;
  }
  /**
   * @return string
   */
  public function getSharedNfsUri()
  {
    return $this->sharedNfsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryComponentDatabaseProperties::class, 'Google_Service_WorkloadManager_SapDiscoveryComponentDatabaseProperties');
