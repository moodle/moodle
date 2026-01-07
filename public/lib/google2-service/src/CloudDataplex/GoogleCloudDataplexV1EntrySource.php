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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1EntrySource extends \Google\Collection
{
  protected $collection_key = 'ancestors';
  protected $ancestorsType = GoogleCloudDataplexV1EntrySourceAncestor::class;
  protected $ancestorsDataType = 'array';
  /**
   * The time when the resource was created in the source system.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description of the data resource. Maximum length is 2,000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * A user-friendly display name. Maximum length is 500 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * User-defined labels. The maximum size of keys and values is 128 characters
   * each.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Location of the resource in the source system. You can search
   * the entry by this location. By default, this should match the location of
   * the entry group containing this entry. A different value allows capturing
   * the source location for data external to Google Cloud.
   *
   * @var string
   */
  public $location;
  /**
   * The platform containing the source system. Maximum length is 64 characters.
   *
   * @var string
   */
  public $platform;
  /**
   * The name of the resource in the source system. Maximum length is 4,000
   * characters.
   *
   * @var string
   */
  public $resource;
  /**
   * The name of the source system. Maximum length is 64 characters.
   *
   * @var string
   */
  public $system;
  /**
   * The time when the resource was last updated in the source system. If the
   * entry exists in the system and its EntrySource has update_time populated,
   * further updates to the EntrySource of the entry must provide incremental
   * updates to its update_time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Immutable. The entries representing the ancestors of the data resource in
   * the source system.
   *
   * @param GoogleCloudDataplexV1EntrySourceAncestor[] $ancestors
   */
  public function setAncestors($ancestors)
  {
    $this->ancestors = $ancestors;
  }
  /**
   * @return GoogleCloudDataplexV1EntrySourceAncestor[]
   */
  public function getAncestors()
  {
    return $this->ancestors;
  }
  /**
   * The time when the resource was created in the source system.
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
   * A description of the data resource. Maximum length is 2,000 characters.
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
   * A user-friendly display name. Maximum length is 500 characters.
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
   * User-defined labels. The maximum size of keys and values is 128 characters
   * each.
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
   * Output only. Location of the resource in the source system. You can search
   * the entry by this location. By default, this should match the location of
   * the entry group containing this entry. A different value allows capturing
   * the source location for data external to Google Cloud.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The platform containing the source system. Maximum length is 64 characters.
   *
   * @param string $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return string
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * The name of the resource in the source system. Maximum length is 4,000
   * characters.
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
   * The name of the source system. Maximum length is 64 characters.
   *
   * @param string $system
   */
  public function setSystem($system)
  {
    $this->system = $system;
  }
  /**
   * @return string
   */
  public function getSystem()
  {
    return $this->system;
  }
  /**
   * The time when the resource was last updated in the source system. If the
   * entry exists in the system and its EntrySource has update_time populated,
   * further updates to the EntrySource of the entry must provide incremental
   * updates to its update_time.
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
class_alias(GoogleCloudDataplexV1EntrySource::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EntrySource');
