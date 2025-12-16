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

class PythonRepository extends \Google\Model
{
  /**
   * Unspecified repository.
   */
  public const PUBLIC_REPOSITORY_PUBLIC_REPOSITORY_UNSPECIFIED = 'PUBLIC_REPOSITORY_UNSPECIFIED';
  /**
   * PyPI.
   */
  public const PUBLIC_REPOSITORY_PYPI = 'PYPI';
  protected $customRepositoryType = GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigPythonRepositoryCustomRepository::class;
  protected $customRepositoryDataType = '';
  /**
   * One of the publicly available Python repositories supported by Artifact
   * Registry.
   *
   * @var string
   */
  public $publicRepository;

  /**
   * Customer-specified remote repository.
   *
   * @param GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigPythonRepositoryCustomRepository $customRepository
   */
  public function setCustomRepository(GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigPythonRepositoryCustomRepository $customRepository)
  {
    $this->customRepository = $customRepository;
  }
  /**
   * @return GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigPythonRepositoryCustomRepository
   */
  public function getCustomRepository()
  {
    return $this->customRepository;
  }
  /**
   * One of the publicly available Python repositories supported by Artifact
   * Registry.
   *
   * Accepted values: PUBLIC_REPOSITORY_UNSPECIFIED, PYPI
   *
   * @param self::PUBLIC_REPOSITORY_* $publicRepository
   */
  public function setPublicRepository($publicRepository)
  {
    $this->publicRepository = $publicRepository;
  }
  /**
   * @return self::PUBLIC_REPOSITORY_*
   */
  public function getPublicRepository()
  {
    return $this->publicRepository;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PythonRepository::class, 'Google_Service_ArtifactRegistry_PythonRepository');
