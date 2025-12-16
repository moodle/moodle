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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1WorkloadResourceSettings extends \Google\Model
{
  /**
   * Unknown resource type.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * Deprecated. Existing workloads will continue to support this, but new
   * CreateWorkloadRequests should not specify this as an input value.
   *
   * @deprecated
   */
  public const RESOURCE_TYPE_CONSUMER_PROJECT = 'CONSUMER_PROJECT';
  /**
   * Consumer Folder.
   */
  public const RESOURCE_TYPE_CONSUMER_FOLDER = 'CONSUMER_FOLDER';
  /**
   * Consumer project containing encryption keys.
   */
  public const RESOURCE_TYPE_ENCRYPTION_KEYS_PROJECT = 'ENCRYPTION_KEYS_PROJECT';
  /**
   * Keyring resource that hosts encryption keys.
   */
  public const RESOURCE_TYPE_KEYRING = 'KEYRING';
  /**
   * User-assigned resource display name. If not empty it will be used to create
   * a resource with the specified name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource identifier. For a project this represents project_id. If the
   * project is already taken, the workload creation will fail. For KeyRing,
   * this represents the keyring_id. For a folder, don't set this value as
   * folder_id is assigned by Google.
   *
   * @var string
   */
  public $resourceId;
  /**
   * Indicates the type of resource. This field should be specified to
   * correspond the id to the right project type (CONSUMER_PROJECT or
   * ENCRYPTION_KEYS_PROJECT)
   *
   * @var string
   */
  public $resourceType;

  /**
   * User-assigned resource display name. If not empty it will be used to create
   * a resource with the specified name.
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
   * Resource identifier. For a project this represents project_id. If the
   * project is already taken, the workload creation will fail. For KeyRing,
   * this represents the keyring_id. For a folder, don't set this value as
   * folder_id is assigned by Google.
   *
   * @param string $resourceId
   */
  public function setResourceId($resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * Indicates the type of resource. This field should be specified to
   * correspond the id to the right project type (CONSUMER_PROJECT or
   * ENCRYPTION_KEYS_PROJECT)
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, CONSUMER_PROJECT,
   * CONSUMER_FOLDER, ENCRYPTION_KEYS_PROJECT, KEYRING
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1WorkloadResourceSettings::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1WorkloadResourceSettings');
