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

class Organization extends \Google\Model
{
  /**
   * Unspecified state. This is only useful for distinguishing unset values.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The normal and active state.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The organization has been marked for deletion by the user.
   */
  public const STATE_DELETE_REQUESTED = 'DELETE_REQUESTED';
  /**
   * Output only. Timestamp when the Organization was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Timestamp when the Organization was requested for deletion.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Immutable. The G Suite / Workspace customer id used in the Directory API.
   *
   * @var string
   */
  public $directoryCustomerId;
  /**
   * Output only. A human-readable string that refers to the organization in the
   * Google Cloud Console. This string is set by the server and cannot be
   * changed. The string will be set to the primary domain (for example,
   * "google.com") of the Google Workspace customer that owns the organization.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. A checksum computed by the server based on the current value
   * of the Organization resource. This may be sent on update and delete
   * requests to ensure the client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The resource name of the organization. This is the
   * organization's relative path in the API. Its format is
   * "organizations/[organization_id]". For example, "organizations/1234".
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The organization's current lifecycle state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp when the Organization was last modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when the Organization was created.
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
   * Output only. Timestamp when the Organization was requested for deletion.
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
   * Immutable. The G Suite / Workspace customer id used in the Directory API.
   *
   * @param string $directoryCustomerId
   */
  public function setDirectoryCustomerId($directoryCustomerId)
  {
    $this->directoryCustomerId = $directoryCustomerId;
  }
  /**
   * @return string
   */
  public function getDirectoryCustomerId()
  {
    return $this->directoryCustomerId;
  }
  /**
   * Output only. A human-readable string that refers to the organization in the
   * Google Cloud Console. This string is set by the server and cannot be
   * changed. The string will be set to the primary domain (for example,
   * "google.com") of the Google Workspace customer that owns the organization.
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
   * of the Organization resource. This may be sent on update and delete
   * requests to ensure the client has an up-to-date value before proceeding.
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
   * Output only. The resource name of the organization. This is the
   * organization's relative path in the API. Its format is
   * "organizations/[organization_id]". For example, "organizations/1234".
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
   * Output only. The organization's current lifecycle state.
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
   * Output only. Timestamp when the Organization was last modified.
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
class_alias(Organization::class, 'Google_Service_CloudResourceManager_Organization');
