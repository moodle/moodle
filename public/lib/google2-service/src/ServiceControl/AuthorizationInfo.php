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

namespace Google\Service\ServiceControl;

class AuthorizationInfo extends \Google\Model
{
  /**
   * Default. Should not be used.
   */
  public const PERMISSION_TYPE_PERMISSION_TYPE_UNSPECIFIED = 'PERMISSION_TYPE_UNSPECIFIED';
  /**
   * Permissions that gate reading resource configuration or metadata.
   */
  public const PERMISSION_TYPE_ADMIN_READ = 'ADMIN_READ';
  /**
   * Permissions that gate modification of resource configuration or metadata.
   */
  public const PERMISSION_TYPE_ADMIN_WRITE = 'ADMIN_WRITE';
  /**
   * Permissions that gate reading user-provided data.
   */
  public const PERMISSION_TYPE_DATA_READ = 'DATA_READ';
  /**
   * Permissions that gate writing user-provided data.
   */
  public const PERMISSION_TYPE_DATA_WRITE = 'DATA_WRITE';
  /**
   * Whether or not authorization for `resource` and `permission` was granted.
   *
   * @var bool
   */
  public $granted;
  /**
   * The required IAM permission.
   *
   * @var string
   */
  public $permission;
  /**
   * The type of the permission that was checked. For data access audit logs
   * this corresponds with the permission type that must be enabled in the
   * project/folder/organization IAM policy in order for the log to be written.
   *
   * @var string
   */
  public $permissionType;
  /**
   * The resource being accessed, as a REST-style or cloud resource string. For
   * example: bigquery.googleapis.com/projects/PROJECTID/datasets/DATASETID or
   * projects/PROJECTID/datasets/DATASETID
   *
   * @var string
   */
  public $resource;
  protected $resourceAttributesType = ServicecontrolResource::class;
  protected $resourceAttributesDataType = '';

  /**
   * Whether or not authorization for `resource` and `permission` was granted.
   *
   * @param bool $granted
   */
  public function setGranted($granted)
  {
    $this->granted = $granted;
  }
  /**
   * @return bool
   */
  public function getGranted()
  {
    return $this->granted;
  }
  /**
   * The required IAM permission.
   *
   * @param string $permission
   */
  public function setPermission($permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return string
   */
  public function getPermission()
  {
    return $this->permission;
  }
  /**
   * The type of the permission that was checked. For data access audit logs
   * this corresponds with the permission type that must be enabled in the
   * project/folder/organization IAM policy in order for the log to be written.
   *
   * Accepted values: PERMISSION_TYPE_UNSPECIFIED, ADMIN_READ, ADMIN_WRITE,
   * DATA_READ, DATA_WRITE
   *
   * @param self::PERMISSION_TYPE_* $permissionType
   */
  public function setPermissionType($permissionType)
  {
    $this->permissionType = $permissionType;
  }
  /**
   * @return self::PERMISSION_TYPE_*
   */
  public function getPermissionType()
  {
    return $this->permissionType;
  }
  /**
   * The resource being accessed, as a REST-style or cloud resource string. For
   * example: bigquery.googleapis.com/projects/PROJECTID/datasets/DATASETID or
   * projects/PROJECTID/datasets/DATASETID
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Resource attributes used in IAM condition evaluation. This field contains
   * resource attributes like resource type and resource name. To get the whole
   * view of the attributes used in IAM condition evaluation, the user must also
   * look into `AuditLog.request_metadata.request_attributes`.
   *
   * @param ServicecontrolResource $resourceAttributes
   */
  public function setResourceAttributes(ServicecontrolResource $resourceAttributes)
  {
    $this->resourceAttributes = $resourceAttributes;
  }
  /**
   * @return ServicecontrolResource
   */
  public function getResourceAttributes()
  {
    return $this->resourceAttributes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthorizationInfo::class, 'Google_Service_ServiceControl_AuthorizationInfo');
