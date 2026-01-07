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

namespace Google\Service\BackupforGKE;

class BackupChannel extends \Google\Model
{
  /**
   * Output only. The timestamp when this BackupChannel resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User specified descriptive string for this BackupChannel.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Immutable. The project where Backups are allowed to be stored.
   * The format is `projects/{projectId}` or `projects/{projectNumber}`.
   *
   * @var string
   */
  public $destinationProject;
  /**
   * Output only. The project_id where Backups are allowed to be stored. Example
   * Project ID: "my-project-id". This will be an OUTPUT_ONLY field to return
   * the project_id of the destination project.
   *
   * @var string
   */
  public $destinationProjectId;
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a BackupChannel from overwriting each
   * other. It is strongly suggested that systems make use of the 'etag' in the
   * read-modify-write cycle to perform BackupChannel updates in order to avoid
   * race conditions: An `etag` is returned in the response to
   * `GetBackupChannel`, and systems are expected to put that etag in the
   * request to `UpdateBackupChannel` or `DeleteBackupChannel` to ensure that
   * their change will be applied to the same version of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. A set of custom labels supplied by user.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The fully qualified name of the BackupChannel.
   * `projects/locations/backupChannels`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp when this BackupChannel resource was last
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when this BackupChannel resource was created.
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
   * Optional. User specified descriptive string for this BackupChannel.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Immutable. The project where Backups are allowed to be stored.
   * The format is `projects/{projectId}` or `projects/{projectNumber}`.
   *
   * @param string $destinationProject
   */
  public function setDestinationProject($destinationProject)
  {
    $this->destinationProject = $destinationProject;
  }
  /**
   * @return string
   */
  public function getDestinationProject()
  {
    return $this->destinationProject;
  }
  /**
   * Output only. The project_id where Backups are allowed to be stored. Example
   * Project ID: "my-project-id". This will be an OUTPUT_ONLY field to return
   * the project_id of the destination project.
   *
   * @param string $destinationProjectId
   */
  public function setDestinationProjectId($destinationProjectId)
  {
    $this->destinationProjectId = $destinationProjectId;
  }
  /**
   * @return string
   */
  public function getDestinationProjectId()
  {
    return $this->destinationProjectId;
  }
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a BackupChannel from overwriting each
   * other. It is strongly suggested that systems make use of the 'etag' in the
   * read-modify-write cycle to perform BackupChannel updates in order to avoid
   * race conditions: An `etag` is returned in the response to
   * `GetBackupChannel`, and systems are expected to put that etag in the
   * request to `UpdateBackupChannel` or `DeleteBackupChannel` to ensure that
   * their change will be applied to the same version of the resource.
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
   * Optional. A set of custom labels supplied by user.
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
   * Identifier. The fully qualified name of the BackupChannel.
   * `projects/locations/backupChannels`
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
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The timestamp when this BackupChannel resource was last
   * updated.
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
class_alias(BackupChannel::class, 'Google_Service_BackupforGKE_BackupChannel');
