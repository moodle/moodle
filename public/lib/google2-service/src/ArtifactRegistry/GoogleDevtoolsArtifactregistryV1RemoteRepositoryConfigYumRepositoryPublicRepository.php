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

namespace Google\Service\ArtifactRegistry;

class GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigYumRepositoryPublicRepository extends \Google\Model
{
  /**
   * Unspecified repository base.
   */
  public const REPOSITORY_BASE_REPOSITORY_BASE_UNSPECIFIED = 'REPOSITORY_BASE_UNSPECIFIED';
  /**
   * CentOS.
   */
  public const REPOSITORY_BASE_CENTOS = 'CENTOS';
  /**
   * CentOS Debug.
   */
  public const REPOSITORY_BASE_CENTOS_DEBUG = 'CENTOS_DEBUG';
  /**
   * CentOS Vault.
   */
  public const REPOSITORY_BASE_CENTOS_VAULT = 'CENTOS_VAULT';
  /**
   * CentOS Stream.
   */
  public const REPOSITORY_BASE_CENTOS_STREAM = 'CENTOS_STREAM';
  /**
   * Rocky.
   */
  public const REPOSITORY_BASE_ROCKY = 'ROCKY';
  /**
   * Fedora Extra Packages for Enterprise Linux (EPEL).
   */
  public const REPOSITORY_BASE_EPEL = 'EPEL';
  /**
   * A common public repository base for Yum.
   *
   * @var string
   */
  public $repositoryBase;
  /**
   * A custom field to define a path to a specific repository from the base.
   *
   * @var string
   */
  public $repositoryPath;

  /**
   * A common public repository base for Yum.
   *
   * Accepted values: REPOSITORY_BASE_UNSPECIFIED, CENTOS, CENTOS_DEBUG,
   * CENTOS_VAULT, CENTOS_STREAM, ROCKY, EPEL
   *
   * @param self::REPOSITORY_BASE_* $repositoryBase
   */
  public function setRepositoryBase($repositoryBase)
  {
    $this->repositoryBase = $repositoryBase;
  }
  /**
   * @return self::REPOSITORY_BASE_*
   */
  public function getRepositoryBase()
  {
    return $this->repositoryBase;
  }
  /**
   * A custom field to define a path to a specific repository from the base.
   *
   * @param string $repositoryPath
   */
  public function setRepositoryPath($repositoryPath)
  {
    $this->repositoryPath = $repositoryPath;
  }
  /**
   * @return string
   */
  public function getRepositoryPath()
  {
    return $this->repositoryPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigYumRepositoryPublicRepository::class, 'Google_Service_ArtifactRegistry_GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigYumRepositoryPublicRepository');
