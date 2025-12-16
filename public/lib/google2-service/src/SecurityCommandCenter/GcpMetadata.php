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

class GcpMetadata extends \Google\Collection
{
  protected $collection_key = 'folders';
  protected $foldersType = GoogleCloudSecuritycenterV2Folder::class;
  protected $foldersDataType = 'array';
  /**
   * The name of the organization that the resource belongs to.
   *
   * @var string
   */
  public $organization;
  /**
   * The full resource name of resource's parent.
   *
   * @var string
   */
  public $parent;
  /**
   * The human readable name of resource's parent.
   *
   * @var string
   */
  public $parentDisplayName;
  /**
   * The full resource name of project that the resource belongs to.
   *
   * @var string
   */
  public $project;
  /**
   * The project ID that the resource belongs to.
   *
   * @var string
   */
  public $projectDisplayName;

  /**
   * Output only. Contains a Folder message for each folder in the assets
   * ancestry. The first folder is the deepest nested folder, and the last
   * folder is the folder directly under the Organization.
   *
   * @param GoogleCloudSecuritycenterV2Folder[] $folders
   */
  public function setFolders($folders)
  {
    $this->folders = $folders;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Folder[]
   */
  public function getFolders()
  {
    return $this->folders;
  }
  /**
   * The name of the organization that the resource belongs to.
   *
   * @param string $organization
   */
  public function setOrganization($organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return string
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * The full resource name of resource's parent.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * The human readable name of resource's parent.
   *
   * @param string $parentDisplayName
   */
  public function setParentDisplayName($parentDisplayName)
  {
    $this->parentDisplayName = $parentDisplayName;
  }
  /**
   * @return string
   */
  public function getParentDisplayName()
  {
    return $this->parentDisplayName;
  }
  /**
   * The full resource name of project that the resource belongs to.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * The project ID that the resource belongs to.
   *
   * @param string $projectDisplayName
   */
  public function setProjectDisplayName($projectDisplayName)
  {
    $this->projectDisplayName = $projectDisplayName;
  }
  /**
   * @return string
   */
  public function getProjectDisplayName()
  {
    return $this->projectDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcpMetadata::class, 'Google_Service_SecurityCommandCenter_GcpMetadata');
