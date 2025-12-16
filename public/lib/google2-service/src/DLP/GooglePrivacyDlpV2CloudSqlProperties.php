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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2CloudSqlProperties extends \Google\Model
{
  /**
   * An engine that is not currently supported by Sensitive Data Protection.
   */
  public const DATABASE_ENGINE_DATABASE_ENGINE_UNKNOWN = 'DATABASE_ENGINE_UNKNOWN';
  /**
   * Cloud SQL for MySQL instance.
   */
  public const DATABASE_ENGINE_DATABASE_ENGINE_MYSQL = 'DATABASE_ENGINE_MYSQL';
  /**
   * Cloud SQL for PostgreSQL instance.
   */
  public const DATABASE_ENGINE_DATABASE_ENGINE_POSTGRES = 'DATABASE_ENGINE_POSTGRES';
  protected $cloudSqlIamType = GooglePrivacyDlpV2CloudSqlIamCredential::class;
  protected $cloudSqlIamDataType = '';
  /**
   * Optional. Immutable. The Cloud SQL instance for which the connection is
   * defined. Only one connection per instance is allowed. This can only be set
   * at creation time, and cannot be updated. It is an error to use a
   * connection_name from different project or region than the one that holds
   * the connection. For example, a Connection resource for Cloud SQL
   * connection_name `project-id:us-central1:sql-instance` must be created under
   * the parent `projects/project-id/locations/us-central1`
   *
   * @var string
   */
  public $connectionName;
  /**
   * Required. The database engine used by the Cloud SQL instance that this
   * connection configures.
   *
   * @var string
   */
  public $databaseEngine;
  /**
   * Required. The DLP API will limit its connections to max_connections. Must
   * be 2 or greater.
   *
   * @var int
   */
  public $maxConnections;
  protected $usernamePasswordType = GooglePrivacyDlpV2SecretManagerCredential::class;
  protected $usernamePasswordDataType = '';

  /**
   * Built-in IAM authentication (must be configured in Cloud SQL).
   *
   * @param GooglePrivacyDlpV2CloudSqlIamCredential $cloudSqlIam
   */
  public function setCloudSqlIam(GooglePrivacyDlpV2CloudSqlIamCredential $cloudSqlIam)
  {
    $this->cloudSqlIam = $cloudSqlIam;
  }
  /**
   * @return GooglePrivacyDlpV2CloudSqlIamCredential
   */
  public function getCloudSqlIam()
  {
    return $this->cloudSqlIam;
  }
  /**
   * Optional. Immutable. The Cloud SQL instance for which the connection is
   * defined. Only one connection per instance is allowed. This can only be set
   * at creation time, and cannot be updated. It is an error to use a
   * connection_name from different project or region than the one that holds
   * the connection. For example, a Connection resource for Cloud SQL
   * connection_name `project-id:us-central1:sql-instance` must be created under
   * the parent `projects/project-id/locations/us-central1`
   *
   * @param string $connectionName
   */
  public function setConnectionName($connectionName)
  {
    $this->connectionName = $connectionName;
  }
  /**
   * @return string
   */
  public function getConnectionName()
  {
    return $this->connectionName;
  }
  /**
   * Required. The database engine used by the Cloud SQL instance that this
   * connection configures.
   *
   * Accepted values: DATABASE_ENGINE_UNKNOWN, DATABASE_ENGINE_MYSQL,
   * DATABASE_ENGINE_POSTGRES
   *
   * @param self::DATABASE_ENGINE_* $databaseEngine
   */
  public function setDatabaseEngine($databaseEngine)
  {
    $this->databaseEngine = $databaseEngine;
  }
  /**
   * @return self::DATABASE_ENGINE_*
   */
  public function getDatabaseEngine()
  {
    return $this->databaseEngine;
  }
  /**
   * Required. The DLP API will limit its connections to max_connections. Must
   * be 2 or greater.
   *
   * @param int $maxConnections
   */
  public function setMaxConnections($maxConnections)
  {
    $this->maxConnections = $maxConnections;
  }
  /**
   * @return int
   */
  public function getMaxConnections()
  {
    return $this->maxConnections;
  }
  /**
   * A username and password stored in Secret Manager.
   *
   * @param GooglePrivacyDlpV2SecretManagerCredential $usernamePassword
   */
  public function setUsernamePassword(GooglePrivacyDlpV2SecretManagerCredential $usernamePassword)
  {
    $this->usernamePassword = $usernamePassword;
  }
  /**
   * @return GooglePrivacyDlpV2SecretManagerCredential
   */
  public function getUsernamePassword()
  {
    return $this->usernamePassword;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CloudSqlProperties::class, 'Google_Service_DLP_GooglePrivacyDlpV2CloudSqlProperties');
