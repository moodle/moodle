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

namespace Google\Service\SecureSourceManager;

class Repository extends \Google\Model
{
  /**
   * Output only. Create timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the repository, which cannot exceed 500
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  protected $initialConfigType = InitialConfig::class;
  protected $initialConfigDataType = '';
  /**
   * Optional. The name of the instance in which the repository is hosted,
   * formatted as
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}`
   * When creating repository via securesourcemanager.googleapis.com, this field
   * is used as input. When creating repository via *.sourcemanager.dev, this
   * field is output only.
   *
   * @var string
   */
  public $instance;
  /**
   * Optional. A unique identifier for a repository. The name should be of the
   * format:
   * `projects/{project}/locations/{location_id}/repositories/{repository_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Unique identifier of the repository.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Update timestamp.
   *
   * @var string
   */
  public $updateTime;
  protected $urisType = URIs::class;
  protected $urisDataType = '';

  /**
   * Output only. Create timestamp.
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
   * Optional. Description of the repository, which cannot exceed 500
   * characters.
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
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
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
   * Input only. Initial configurations for the repository.
   *
   * @param InitialConfig $initialConfig
   */
  public function setInitialConfig(InitialConfig $initialConfig)
  {
    $this->initialConfig = $initialConfig;
  }
  /**
   * @return InitialConfig
   */
  public function getInitialConfig()
  {
    return $this->initialConfig;
  }
  /**
   * Optional. The name of the instance in which the repository is hosted,
   * formatted as
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}`
   * When creating repository via securesourcemanager.googleapis.com, this field
   * is used as input. When creating repository via *.sourcemanager.dev, this
   * field is output only.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Optional. A unique identifier for a repository. The name should be of the
   * format:
   * `projects/{project}/locations/{location_id}/repositories/{repository_id}`
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
   * Output only. Unique identifier of the repository.
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
   * Output only. Update timestamp.
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
  /**
   * Output only. URIs for the repository.
   *
   * @param URIs $uris
   */
  public function setUris(URIs $uris)
  {
    $this->uris = $uris;
  }
  /**
   * @return URIs
   */
  public function getUris()
  {
    return $this->uris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Repository::class, 'Google_Service_SecureSourceManager_Repository');
