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

namespace Google\Service\SecurityCommandCenter;

class SecurityCenterProperties extends \Google\Collection
{
  protected $collection_key = 'resourceOwners';
  protected $foldersType = Folder::class;
  protected $foldersDataType = 'array';
  /**
   * The user defined display name for this resource.
   *
   * @var string
   */
  public $resourceDisplayName;
  /**
   * The full resource name of the Google Cloud resource this asset represents.
   * This field is immutable after create time. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @var string
   */
  public $resourceName;
  /**
   * Owners of the Google Cloud resource.
   *
   * @var string[]
   */
  public $resourceOwners;
  /**
   * The full resource name of the immediate parent of the resource. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @var string
   */
  public $resourceParent;
  /**
   * The user defined display name for the parent of this resource.
   *
   * @var string
   */
  public $resourceParentDisplayName;
  /**
   * The full resource name of the project the resource belongs to. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @var string
   */
  public $resourceProject;
  /**
   * The user defined display name for the project of this resource.
   *
   * @var string
   */
  public $resourceProjectDisplayName;
  /**
   * The type of the Google Cloud resource. Examples include: APPLICATION,
   * PROJECT, and ORGANIZATION. This is a case insensitive field defined by
   * Security Command Center and/or the producer of the resource and is
   * immutable after create time.
   *
   * @var string
   */
  public $resourceType;

  /**
   * Contains a Folder message for each folder in the assets ancestry. The first
   * folder is the deepest nested folder, and the last folder is the folder
   * directly under the Organization.
   *
   * @param Folder[] $folders
   */
  public function setFolders($folders)
  {
    $this->folders = $folders;
  }
  /**
   * @return Folder[]
   */
  public function getFolders()
  {
    return $this->folders;
  }
  /**
   * The user defined display name for this resource.
   *
   * @param string $resourceDisplayName
   */
  public function setResourceDisplayName($resourceDisplayName)
  {
    $this->resourceDisplayName = $resourceDisplayName;
  }
  /**
   * @return string
   */
  public function getResourceDisplayName()
  {
    return $this->resourceDisplayName;
  }
  /**
   * The full resource name of the Google Cloud resource this asset represents.
   * This field is immutable after create time. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Owners of the Google Cloud resource.
   *
   * @param string[] $resourceOwners
   */
  public function setResourceOwners($resourceOwners)
  {
    $this->resourceOwners = $resourceOwners;
  }
  /**
   * @return string[]
   */
  public function getResourceOwners()
  {
    return $this->resourceOwners;
  }
  /**
   * The full resource name of the immediate parent of the resource. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @param string $resourceParent
   */
  public function setResourceParent($resourceParent)
  {
    $this->resourceParent = $resourceParent;
  }
  /**
   * @return string
   */
  public function getResourceParent()
  {
    return $this->resourceParent;
  }
  /**
   * The user defined display name for the parent of this resource.
   *
   * @param string $resourceParentDisplayName
   */
  public function setResourceParentDisplayName($resourceParentDisplayName)
  {
    $this->resourceParentDisplayName = $resourceParentDisplayName;
  }
  /**
   * @return string
   */
  public function getResourceParentDisplayName()
  {
    return $this->resourceParentDisplayName;
  }
  /**
   * The full resource name of the project the resource belongs to. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @param string $resourceProject
   */
  public function setResourceProject($resourceProject)
  {
    $this->resourceProject = $resourceProject;
  }
  /**
   * @return string
   */
  public function getResourceProject()
  {
    return $this->resourceProject;
  }
  /**
   * The user defined display name for the project of this resource.
   *
   * @param string $resourceProjectDisplayName
   */
  public function setResourceProjectDisplayName($resourceProjectDisplayName)
  {
    $this->resourceProjectDisplayName = $resourceProjectDisplayName;
  }
  /**
   * @return string
   */
  public function getResourceProjectDisplayName()
  {
    return $this->resourceProjectDisplayName;
  }
  /**
   * The type of the Google Cloud resource. Examples include: APPLICATION,
   * PROJECT, and ORGANIZATION. This is a case insensitive field defined by
   * Security Command Center and/or the producer of the resource and is
   * immutable after create time.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityCenterProperties::class, 'Google_Service_SecurityCommandCenter_SecurityCenterProperties');
