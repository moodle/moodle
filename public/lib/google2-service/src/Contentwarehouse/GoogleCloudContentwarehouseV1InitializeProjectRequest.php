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

class GoogleCloudContentwarehouseV1InitializeProjectRequest extends \Google\Model
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
   * Unspecified, will be default to document admin role.
   */
  public const DOCUMENT_CREATOR_DEFAULT_ROLE_DOCUMENT_CREATOR_DEFAULT_ROLE_UNSPECIFIED = 'DOCUMENT_CREATOR_DEFAULT_ROLE_UNSPECIFIED';
  /**
   * Document Admin, same as contentwarehouse.googleapis.com/documentAdmin.
   */
  public const DOCUMENT_CREATOR_DEFAULT_ROLE_DOCUMENT_ADMIN = 'DOCUMENT_ADMIN';
  /**
   * Document Editor, same as contentwarehouse.googleapis.com/documentEditor.
   */
  public const DOCUMENT_CREATOR_DEFAULT_ROLE_DOCUMENT_EDITOR = 'DOCUMENT_EDITOR';
  /**
   * Document Viewer, same as contentwarehouse.googleapis.com/documentViewer.
   */
  public const DOCUMENT_CREATOR_DEFAULT_ROLE_DOCUMENT_VIEWER = 'DOCUMENT_VIEWER';
  /**
   * Required. The access control mode for accessing the customer data
   *
   * @var string
   */
  public $accessControlMode;
  /**
   * Required. The type of database used to store customer data
   *
   * @var string
   */
  public $databaseType;
  /**
   * Optional. The default role for the person who create a document.
   *
   * @var string
   */
  public $documentCreatorDefaultRole;
  /**
   * Optional. Whether to enable CAL user email logging.
   *
   * @var bool
   */
  public $enableCalUserEmailLogging;
  /**
   * Optional. The KMS key used for CMEK encryption. It is required that the kms
   * key is in the same region as the endpoint. The same key will be used for
   * all provisioned resources, if encryption is available. If the kms_key is
   * left empty, no encryption will be enforced.
   *
   * @var string
   */
  public $kmsKey;

  /**
   * Required. The access control mode for accessing the customer data
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
   * Required. The type of database used to store customer data
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
   * Optional. The default role for the person who create a document.
   *
   * Accepted values: DOCUMENT_CREATOR_DEFAULT_ROLE_UNSPECIFIED, DOCUMENT_ADMIN,
   * DOCUMENT_EDITOR, DOCUMENT_VIEWER
   *
   * @param self::DOCUMENT_CREATOR_DEFAULT_ROLE_* $documentCreatorDefaultRole
   */
  public function setDocumentCreatorDefaultRole($documentCreatorDefaultRole)
  {
    $this->documentCreatorDefaultRole = $documentCreatorDefaultRole;
  }
  /**
   * @return self::DOCUMENT_CREATOR_DEFAULT_ROLE_*
   */
  public function getDocumentCreatorDefaultRole()
  {
    return $this->documentCreatorDefaultRole;
  }
  /**
   * Optional. Whether to enable CAL user email logging.
   *
   * @param bool $enableCalUserEmailLogging
   */
  public function setEnableCalUserEmailLogging($enableCalUserEmailLogging)
  {
    $this->enableCalUserEmailLogging = $enableCalUserEmailLogging;
  }
  /**
   * @return bool
   */
  public function getEnableCalUserEmailLogging()
  {
    return $this->enableCalUserEmailLogging;
  }
  /**
   * Optional. The KMS key used for CMEK encryption. It is required that the kms
   * key is in the same region as the endpoint. The same key will be used for
   * all provisioned resources, if encryption is available. If the kms_key is
   * left empty, no encryption will be enforced.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1InitializeProjectRequest::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1InitializeProjectRequest');
