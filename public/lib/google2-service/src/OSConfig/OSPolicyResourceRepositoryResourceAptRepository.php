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

class OSPolicyResourceRepositoryResourceAptRepository extends \Google\Collection
{
  /**
   * Unspecified is invalid.
   */
  public const ARCHIVE_TYPE_ARCHIVE_TYPE_UNSPECIFIED = 'ARCHIVE_TYPE_UNSPECIFIED';
  /**
   * Deb indicates that the archive contains binary files.
   */
  public const ARCHIVE_TYPE_DEB = 'DEB';
  /**
   * Deb-src indicates that the archive contains source files.
   */
  public const ARCHIVE_TYPE_DEB_SRC = 'DEB_SRC';
  protected $collection_key = 'components';
  /**
   * Required. Type of archive files in this repository.
   *
   * @var string
   */
  public $archiveType;
  /**
   * Required. List of components for this repository. Must contain at least one
   * item.
   *
   * @var string[]
   */
  public $components;
  /**
   * Required. Distribution of this repository.
   *
   * @var string
   */
  public $distribution;
  /**
   * URI of the key file for this repository. The agent maintains a keyring at
   * `/etc/apt/trusted.gpg.d/osconfig_agent_managed.gpg`.
   *
   * @var string
   */
  public $gpgKey;
  /**
   * Required. URI for this repository.
   *
   * @var string
   */
  public $uri;

  /**
   * Required. Type of archive files in this repository.
   *
   * Accepted values: ARCHIVE_TYPE_UNSPECIFIED, DEB, DEB_SRC
   *
   * @param self::ARCHIVE_TYPE_* $archiveType
   */
  public function setArchiveType($archiveType)
  {
    $this->archiveType = $archiveType;
  }
  /**
   * @return self::ARCHIVE_TYPE_*
   */
  public function getArchiveType()
  {
    return $this->archiveType;
  }
  /**
   * Required. List of components for this repository. Must contain at least one
   * item.
   *
   * @param string[] $components
   */
  public function setComponents($components)
  {
    $this->components = $components;
  }
  /**
   * @return string[]
   */
  public function getComponents()
  {
    return $this->components;
  }
  /**
   * Required. Distribution of this repository.
   *
   * @param string $distribution
   */
  public function setDistribution($distribution)
  {
    $this->distribution = $distribution;
  }
  /**
   * @return string
   */
  public function getDistribution()
  {
    return $this->distribution;
  }
  /**
   * URI of the key file for this repository. The agent maintains a keyring at
   * `/etc/apt/trusted.gpg.d/osconfig_agent_managed.gpg`.
   *
   * @param string $gpgKey
   */
  public function setGpgKey($gpgKey)
  {
    $this->gpgKey = $gpgKey;
  }
  /**
   * @return string
   */
  public function getGpgKey()
  {
    return $this->gpgKey;
  }
  /**
   * Required. URI for this repository.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyResourceRepositoryResourceAptRepository::class, 'Google_Service_OSConfig_OSPolicyResourceRepositoryResourceAptRepository');
