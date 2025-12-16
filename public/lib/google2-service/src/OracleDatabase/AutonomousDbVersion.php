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

namespace Google\Service\OracleDatabase;

class AutonomousDbVersion extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const DB_WORKLOAD_DB_WORKLOAD_UNSPECIFIED = 'DB_WORKLOAD_UNSPECIFIED';
  /**
   * Autonomous Transaction Processing database.
   */
  public const DB_WORKLOAD_OLTP = 'OLTP';
  /**
   * Autonomous Data Warehouse database.
   */
  public const DB_WORKLOAD_DW = 'DW';
  /**
   * Autonomous JSON Database.
   */
  public const DB_WORKLOAD_AJD = 'AJD';
  /**
   * Autonomous Database with the Oracle APEX Application Development workload
   * type.
   */
  public const DB_WORKLOAD_APEX = 'APEX';
  /**
   * Output only. The Autonomous Database workload type.
   *
   * @var string
   */
  public $dbWorkload;
  /**
   * Identifier. The name of the Autonomous Database Version resource with the
   * format: projects/{project}/locations/{region}/autonomousDbVersions/{autonom
   * ous_db_version}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. An Oracle Database version for Autonomous Database.
   *
   * @var string
   */
  public $version;
  /**
   * Output only. A URL that points to a detailed description of the Autonomous
   * Database version.
   *
   * @var string
   */
  public $workloadUri;

  /**
   * Output only. The Autonomous Database workload type.
   *
   * Accepted values: DB_WORKLOAD_UNSPECIFIED, OLTP, DW, AJD, APEX
   *
   * @param self::DB_WORKLOAD_* $dbWorkload
   */
  public function setDbWorkload($dbWorkload)
  {
    $this->dbWorkload = $dbWorkload;
  }
  /**
   * @return self::DB_WORKLOAD_*
   */
  public function getDbWorkload()
  {
    return $this->dbWorkload;
  }
  /**
   * Identifier. The name of the Autonomous Database Version resource with the
   * format: projects/{project}/locations/{region}/autonomousDbVersions/{autonom
   * ous_db_version}
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
   * Output only. An Oracle Database version for Autonomous Database.
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
  /**
   * Output only. A URL that points to a detailed description of the Autonomous
   * Database version.
   *
   * @param string $workloadUri
   */
  public function setWorkloadUri($workloadUri)
  {
    $this->workloadUri = $workloadUri;
  }
  /**
   * @return string
   */
  public function getWorkloadUri()
  {
    return $this->workloadUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutonomousDbVersion::class, 'Google_Service_OracleDatabase_AutonomousDbVersion');
