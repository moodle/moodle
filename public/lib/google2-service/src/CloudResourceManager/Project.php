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

namespace Google\Service\CloudResourceManager;

class Project extends \Google\Collection
{
  /**
   * Unspecified state. This is only used/useful for distinguishing unset
   * values.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The normal and active state.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The project has been marked for deletion by the user (by invoking
   * DeleteProject) or by the system (Google Cloud Platform). This can generally
   * be reversed by invoking UndeleteProject.
   */
  public const STATE_DELETE_REQUESTED = 'DELETE_REQUESTED';
  protected $collection_key = 'configuredCapabilities';
  /**
   * Output only. If this project is a Management Project, list of capabilities
   * configured on the parent folder. Note, presence of any capability implies
   * that this is a Management Project. Example: `folders/123/capabilities/app-
   * management`. OUTPUT ONLY.
   *
   * @var string[]
   */
  public $configuredCapabilities;
  /**
   * Output only. Creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time at which this resource was requested for deletion.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. A user-assigned display name of the project. When present it must
   * be between 4 to 30 characters. Allowed characters are: lowercase and
   * uppercase letters, numbers, hyphen, single-quote, double-quote, space, and
   * exclamation point. Example: `My Project`
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. A checksum computed by the server based on the current value
   * of the Project resource. This may be sent on update and delete requests to
   * ensure the client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels associated with this project. Label keys must be
   * between 1 and 63 characters long and must conform to the following regular
   * expression: \[a-z\](\[-a-z0-9\]*\[a-z0-9\])?. Label values must be between
   * 0 and 63 characters long and must conform to the regular expression
   * (\[a-z\](\[-a-z0-9\]*\[a-z0-9\])?)?. No more than 64 labels can be
   * associated with a given resource. Clients should store labels in a
   * representation such as JSON that does not depend on specific characters
   * being disallowed. Example: `"myBusinessDimension" : "businessValue"`
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The unique resource name of the project. It is an int64
   * generated number prefixed by "projects/". Example: `projects/415104041262`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. A reference to a parent Resource. eg., `organizations/123` or
   * `folders/876`.
   *
   * @var string
   */
  public $parent;
  /**
   * Immutable. The unique, user-assigned id of the project. It must be 6 to 30
   * lowercase ASCII letters, digits, or hyphens. It must start with a letter.
   * Trailing hyphens are prohibited. Example: `tokyo-rain-123`
   *
   * @var string
   */
  public $projectId;
  /**
   * Output only. The project lifecycle state.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * project. Each item in the map must be expressed as " : ". For example:
   * "123/environment" : "production", "123/costCenter" : "marketing" Note:
   * Currently this field is in Preview.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Output only. The most recent time this resource was modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. If this project is a Management Project, list of capabilities
   * configured on the parent folder. Note, presence of any capability implies
   * that this is a Management Project. Example: `folders/123/capabilities/app-
   * management`. OUTPUT ONLY.
   *
   * @param string[] $configuredCapabilities
   */
  public function setConfiguredCapabilities($configuredCapabilities)
  {
    $this->configuredCapabilities = $configuredCapabilities;
  }
  /**
   * @return string[]
   */
  public function getConfiguredCapabilities()
  {
    return $this->configuredCapabilities;
  }
  /**
   * Output only. Creation time.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The time at which this resource was requested for deletion.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Optional. A user-assigned display name of the project. When present it must
   * be between 4 to 30 characters. Allowed characters are: lowercase and
   * uppercase letters, numbers, hyphen, single-quote, double-quote, space, and
   * exclamation point. Example: `My Project`
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
   * Output only. A checksum computed by the server based on the current value
   * of the Project resource. This may be sent on update and delete requests to
   * ensure the client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The labels associated with this project. Label keys must be
   * between 1 and 63 characters long and must conform to the following regular
   * expression: \[a-z\](\[-a-z0-9\]*\[a-z0-9\])?. Label values must be between
   * 0 and 63 characters long and must conform to the regular expression
   * (\[a-z\](\[-a-z0-9\]*\[a-z0-9\])?)?. No more than 64 labels can be
   * associated with a given resource. Clients should store labels in a
   * representation such as JSON that does not depend on specific characters
   * being disallowed. Example: `"myBusinessDimension" : "businessValue"`
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The unique resource name of the project. It is an int64
   * generated number prefixed by "projects/". Example: `projects/415104041262`
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
   * Optional. A reference to a parent Resource. eg., `organizations/123` or
   * `folders/876`.
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
   * Immutable. The unique, user-assigned id of the project. It must be 6 to 30
   * lowercase ASCII letters, digits, or hyphens. It must start with a letter.
   * Trailing hyphens are prohibited. Example: `tokyo-rain-123`
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Output only. The project lifecycle state.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DELETE_REQUESTED
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
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * project. Each item in the map must be expressed as " : ". For example:
   * "123/environment" : "production", "123/costCenter" : "marketing" Note:
   * Currently this field is in Preview.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Output only. The most recent time this resource was modified.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Project::class, 'Google_Service_CloudResourceManager_Project');
