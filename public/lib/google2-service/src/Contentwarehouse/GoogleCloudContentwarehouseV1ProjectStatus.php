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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1ProjectStatus extends \Google\Model
{
  /**
   * This value is required by protobuf best practices
   */
  public const ACCESS_CONTROL_MODE_ACL_MODE_UNKNOWN = 'ACL_MODE_UNKNOWN';
  /**
   * Universal Access: No document level access control.
   */
  public const ACCESS_CONTROL_MODE_ACL_MODE_UNIVERSAL_ACCESS = 'ACL_MODE_UNIVERSAL_ACCESS';
  /**
   * Document level access control with customer own Identity Service.
   */
  public const ACCESS_CONTROL_MODE_ACL_MODE_DOCUMENT_LEVEL_ACCESS_CONTROL_BYOID = 'ACL_MODE_DOCUMENT_LEVEL_ACCESS_CONTROL_BYOID';
  /**
   * Document level access control using Google Cloud Identity.
   */
  public const ACCESS_CONTROL_MODE_ACL_MODE_DOCUMENT_LEVEL_ACCESS_CONTROL_GCI = 'ACL_MODE_DOCUMENT_LEVEL_ACCESS_CONTROL_GCI';
  /**
   * This value is required by protobuf best practices
   */
  public const DATABASE_TYPE_DB_UNKNOWN = 'DB_UNKNOWN';
  /**
   * Internal Spanner
   */
  public const DATABASE_TYPE_DB_INFRA_SPANNER = 'DB_INFRA_SPANNER';
  /**
   * Cloud Sql with a Postgres Sql instance
   *
   * @deprecated
   */
  public const DATABASE_TYPE_DB_CLOUD_SQL_POSTGRES = 'DB_CLOUD_SQL_POSTGRES';
  /**
   * Default status, required by protobuf best practices.
   */
  public const STATE_PROJECT_STATE_UNSPECIFIED = 'PROJECT_STATE_UNSPECIFIED';
  /**
   * The project is in the middle of a provision process.
   */
  public const STATE_PROJECT_STATE_PENDING = 'PROJECT_STATE_PENDING';
  /**
   * All dependencies have been provisioned.
   */
  public const STATE_PROJECT_STATE_COMPLETED = 'PROJECT_STATE_COMPLETED';
  /**
   * A provision process was previously initiated, but failed.
   */
  public const STATE_PROJECT_STATE_FAILED = 'PROJECT_STATE_FAILED';
  /**
   * The project is in the middle of a deletion process.
   */
  public const STATE_PROJECT_STATE_DELETING = 'PROJECT_STATE_DELETING';
  /**
   * A deleting process was initiated, but failed.
   */
  public const STATE_PROJECT_STATE_DELETING_FAILED = 'PROJECT_STATE_DELETING_FAILED';
  /**
   * The project is deleted.
   */
  public const STATE_PROJECT_STATE_DELETED = 'PROJECT_STATE_DELETED';
  /**
   * The project is not found.
   */
  public const STATE_PROJECT_STATE_NOT_FOUND = 'PROJECT_STATE_NOT_FOUND';
  /**
   * Access control mode.
   *
   * @var string
   */
  public $accessControlMode;
  /**
   * Database type.
   *
   * @var string
   */
  public $databaseType;
  /**
   * The default role for the person who create a document.
   *
   * @var string
   */
  public $documentCreatorDefaultRole;
  /**
   * The location of the queried project.
   *
   * @var string
   */
  public $location;
  /**
   * If the qa is enabled on this project.
   *
   * @var bool
   */
  public $qaEnabled;
  /**
   * State of the project.
   *
   * @var string
   */
  public $state;

  /**
   * Access control mode.
   *
   * Accepted values: ACL_MODE_UNKNOWN, ACL_MODE_UNIVERSAL_ACCESS,
   * ACL_MODE_DOCUMENT_LEVEL_ACCESS_CONTROL_BYOID,
   * ACL_MODE_DOCUMENT_LEVEL_ACCESS_CONTROL_GCI
   *
   * @param self::ACCESS_CONTROL_MODE_* $accessControlMode
   */
  public function setAccessControlMode($accessControlMode)
  {
    $this->accessControlMode = $accessControlMode;
  }
  /**
   * @return self::ACCESS_CONTROL_MODE_*
   */
  public function getAccessControlMode()
  {
    return $this->accessControlMode;
  }
  /**
   * Database type.
   *
   * Accepted values: DB_UNKNOWN, DB_INFRA_SPANNER, DB_CLOUD_SQL_POSTGRES
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
   * The default role for the person who create a document.
   *
   * @param string $documentCreatorDefaultRole
   */
  public function setDocumentCreatorDefaultRole($documentCreatorDefaultRole)
  {
    $this->documentCreatorDefaultRole = $documentCreatorDefaultRole;
  }
  /**
   * @return string
   */
  public function getDocumentCreatorDefaultRole()
  {
    return $this->documentCreatorDefaultRole;
  }
  /**
   * The location of the queried project.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * If the qa is enabled on this project.
   *
   * @param bool $qaEnabled
   */
  public function setQaEnabled($qaEnabled)
  {
    $this->qaEnabled = $qaEnabled;
  }
  /**
   * @return bool
   */
  public function getQaEnabled()
  {
    return $this->qaEnabled;
  }
  /**
   * State of the project.
   *
   * Accepted values: PROJECT_STATE_UNSPECIFIED, PROJECT_STATE_PENDING,
   * PROJECT_STATE_COMPLETED, PROJECT_STATE_FAILED, PROJECT_STATE_DELETING,
   * PROJECT_STATE_DELETING_FAILED, PROJECT_STATE_DELETED,
   * PROJECT_STATE_NOT_FOUND
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1ProjectStatus::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1ProjectStatus');
