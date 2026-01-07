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

namespace Google\Service\OSConfig;

class OSPolicyResourceRepositoryResourceYumRepository extends \Google\Collection
{
  protected $collection_key = 'gpgKeys';
  /**
   * Required. The location of the repository directory.
   *
   * @var string
   */
  public $baseUrl;
  /**
   * The display name of the repository.
   *
   * @var string
   */
  public $displayName;
  /**
   * URIs of GPG keys.
   *
   * @var string[]
   */
  public $gpgKeys;
  /**
   * Required. A one word, unique name for this repository. This is the `repo
   * id` in the yum config file and also the `display_name` if `display_name` is
   * omitted. This id is also used as the unique identifier when checking for
   * resource conflicts.
   *
   * @var string
   */
  public $id;

  /**
   * Required. The location of the repository directory.
   *
   * @param string $baseUrl
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
  }
  /**
   * @return string
   */
  public function getBaseUrl()
  {
    return $this->baseUrl;
  }
  /**
   * The display name of the repository.
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
   * URIs of GPG keys.
   *
   * @param string[] $gpgKeys
   */
  public function setGpgKeys($gpgKeys)
  {
    $this->gpgKeys = $gpgKeys;
  }
  /**
   * @return string[]
   */
  public function getGpgKeys()
  {
    return $this->gpgKeys;
  }
  /**
   * Required. A one word, unique name for this repository. This is the `repo
   * id` in the yum config file and also the `display_name` if `display_name` is
   * omitted. This id is also used as the unique identifier when checking for
   * resource conflicts.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyResourceRepositoryResourceYumRepository::class, 'Google_Service_OSConfig_OSPolicyResourceRepositoryResourceYumRepository');
