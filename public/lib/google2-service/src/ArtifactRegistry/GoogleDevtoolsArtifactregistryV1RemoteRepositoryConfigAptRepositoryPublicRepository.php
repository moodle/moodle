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

class GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryPublicRepository extends \Google\Model
{
  /**
   * Unspecified repository base.
   */
  public const REPOSITORY_BASE_REPOSITORY_BASE_UNSPECIFIED = 'REPOSITORY_BASE_UNSPECIFIED';
  /**
   * Debian.
   */
  public const REPOSITORY_BASE_DEBIAN = 'DEBIAN';
  /**
   * Ubuntu LTS/Pro.
   */
  public const REPOSITORY_BASE_UBUNTU = 'UBUNTU';
  /**
   * Archived Debian.
   */
  public const REPOSITORY_BASE_DEBIAN_SNAPSHOT = 'DEBIAN_SNAPSHOT';
  /**
   * A common public repository base for Apt.
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
   * A common public repository base for Apt.
   *
   * Accepted values: REPOSITORY_BASE_UNSPECIFIED, DEBIAN, UBUNTU,
   * DEBIAN_SNAPSHOT
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
class_alias(GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryPublicRepository::class, 'Google_Service_ArtifactRegistry_GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryPublicRepository');
