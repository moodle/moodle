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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ApiSecurityRuntimeConfig extends \Google\Collection
{
  protected $collection_key = 'location';
  /**
   * A list of up to 5 Cloud Storage Blobs that contain SecurityActions.
   *
   * @var string[]
   */
  public $location;
  /**
   * Name of the environment API Security Runtime configuration resource.
   * Format: `organizations/{org}/environments/{env}/apiSecurityRuntimeConfig`
   *
   * @var string
   */
  public $name;
  /**
   * Revision ID of the API Security Runtime configuration. The higher the
   * value, the more recently the configuration was deployed.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Unique ID for the API Security Runtime configuration. The ID will only
   * change if the environment is deleted and recreated.
   *
   * @var string
   */
  public $uid;
  /**
   * Time that the API Security Runtime configuration was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * A list of up to 5 Cloud Storage Blobs that contain SecurityActions.
   *
   * @param string[] $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string[]
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Name of the environment API Security Runtime configuration resource.
   * Format: `organizations/{org}/environments/{env}/apiSecurityRuntimeConfig`
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
   * Revision ID of the API Security Runtime configuration. The higher the
   * value, the more recently the configuration was deployed.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Unique ID for the API Security Runtime configuration. The ID will only
   * change if the environment is deleted and recreated.
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
   * Time that the API Security Runtime configuration was updated.
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
class_alias(GoogleCloudApigeeV1ApiSecurityRuntimeConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ApiSecurityRuntimeConfig');
