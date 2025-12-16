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

class AptRepository extends \Google\Model
{
  protected $customRepositoryType = GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryCustomRepository::class;
  protected $customRepositoryDataType = '';
  protected $publicRepositoryType = GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryPublicRepository::class;
  protected $publicRepositoryDataType = '';

  /**
   * Customer-specified remote repository.
   *
   * @param GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryCustomRepository $customRepository
   */
  public function setCustomRepository(GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryCustomRepository $customRepository)
  {
    $this->customRepository = $customRepository;
  }
  /**
   * @return GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryCustomRepository
   */
  public function getCustomRepository()
  {
    return $this->customRepository;
  }
  /**
   * One of the publicly available Apt repositories supported by Artifact
   * Registry.
   *
   * @param GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryPublicRepository $publicRepository
   */
  public function setPublicRepository(GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryPublicRepository $publicRepository)
  {
    $this->publicRepository = $publicRepository;
  }
  /**
   * @return GoogleDevtoolsArtifactregistryV1RemoteRepositoryConfigAptRepositoryPublicRepository
   */
  public function getPublicRepository()
  {
    return $this->publicRepository;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AptRepository::class, 'Google_Service_ArtifactRegistry_AptRepository');
